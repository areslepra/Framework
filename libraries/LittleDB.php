<?php
/**
 * Clase de Abstracci�n de bases de datos sencilla y de f�cil implementaci�n
 * @package class.littledb.php
 * @author Cody Roodaka <roodakazo@hotmail.com>
 * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 * @version  $Revision: 0.2.9
 * @access public
 * @see https://github.com/roodaka/littledb
 */

namespace Roodaka\Framework;



defined('ROOT') or exit('No tienes Permitido el acceso.');


/**
 * LDB: Funci�n improvisada para el manejo de errores
 * @param string $query Consulta que origina el error
 * @param string $error Mensaje de error provisto por el servidor MySQL
 * @return nothing
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function LittleDB_Error($query, $error)
 {
  echo('<h2>Data Base Error</h2>'.(($query !== '') ?'<span>Error en la consulta <i>'.$query.'</i></br>' : '').'<b>'.$error.'</b></span>');
  // throw new LDB_Exception('<h2>Data Base Error</h2>'.(($query !== '') ?'<span>Error en la consulta <i>'.$query.'</i></br>' : '').'<b>'.$error.'</b></span>');
 }




class LittleDB
 {
  // Instancia de LittleDB
  protected static $instance = null;

  /**
   * Recurso MySQL
   * @var resource
   */
  public $conn = null;

  /**
   * Arreglo con los datos de conexi�n al servidor MySQL
   * @var array
   */
  protected $data = array(
   'host' => null,
   'port' => null,
   'user' => null,
   'pass' => null,
   'name' => null
   );

  /**
   * Funci�n de registro
   * @var array|string
   */
  protected $logger = null;

  /**
   * Funci�n de manejo de errores
   * @var array|string
   */
  protected $errors = null;

  /**
   * Prefijo de la base de datos
   * @var string
   */
  public $prefix = '';

  /**
   * Cantidad de consultas realizadas
   * @var integer
   */
  public static $count = 0;


  /**
   * Constantes utilizadas en la actualizaci�n de columnas
   */
  const ALL = '*';
  const ADD = '+';
  const REST = '-';
  const FIELDS = 0;
  const VALUES = 1;



  /**
   * Constructor de la clase
   * @param string $host Url o DNS del Servidor MySQL
   * @param string $user Usuario del servidor
   * @param string &pass Contrase�a del servidor
   * @param string $db Nombre de la base de datos
   * @param mixed $logger Funci�n para el registro de datos
   * @param mixed $errors Funci�n para el manejo de errores
   * @return nothing
   */
  private function __construct($host, $user, $pass, $db, $prefix = null, $logger = null, $errors = null)
   {
    $this->data['host'] = $host;
    $this->data['user'] = $user;
    $this->data['pass'] = $pass;
    $this->data['name'] = $db;
    $this->prefix = $prefix;
    $this->errors = $errors;
    $this->logger = $logger;

    // Conectamos a la base de datos.
    $this->connect();
   }



  /**
   * Desconectar & Destruir la clase
   * @return boolean
   */
  public function __destruct()
   {
    return $this->disconect();
   }



  /**
   * No permitimos la clonaci�n de LittleDB
   * @return boolean
   */
  public function __clone()
   {
    return $this->error('', 'La clonaci�n de este objeto LittleDB no est� permitida.');
   }



  /**
   * Retornamos esta clase como una cadena.
   * @return string
   */
  public function __toString()
   {
    $cfg = array();
    foreach($this->data as $field => $value)
     {
      $cfg[] = $field.'='.$value;
     }
    return __CLASS__.'['.implode(';', $cfg).']';
   }



  /**
   * Si se invoca esta clase ser� con el fin de generar una consulta a la base
   * de datos a modo de Alias a la funci�n $this->select.
   */
  public function __invoke($table, $fields, $condition = null, $limit = 1)
   {
    return $this->select($query, $fields, $condition, $limit);
   }



  /**
   * Retornamos la configuraci�n de la clase cuando �sta es serializada.
   * @return array
   */
  public function __sleep()
   {
    return array(
     'data' => $this->data,
     'logger' => $this->logger,
     'errors' => $this->errors,
     'prefix' => $this->prefix
     );
   }



  /**
   * Conectamos a la base de datos cuando �sta es deserializada
   * @return nothing
   */
  public function __wakeup()
   {
    $this->connect();
   }



  /**
   * Obtenemos una instancia de la clase, la primera vez pasamos los par�metros
   * para conectar.
   * @return object $instance Instancia de la base de datos.
   */
  public static function get_instance()
   {
    if(self::$instance === null)
     {
      $config = get_config('database');
      self::$instance = new LittleDB($config['host'], $config['user'], $config['password'], $config['database'], $config['prefix'], $config['logs_handler'], $config['errors_handler']);
     }
    return self::$instance;
   }



  /**
   * Conectar al servidor MySQL
   * @return nothing
   */
  private function connect()
   {
    $this->conn = mysqli_connect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['name']) or $this->error('', 'No se pudo conectar al servidor MySQL');

   }



  /**
   * Desconectar del servidor MySQL
   * @return boolean
   */
  private function disconect()
   {
    //return ($this->conn !== null) ? mysqli_close($this->conn) : true;
   }



  /**
   * Procesar una consulta compleja en la base de datos
   * @param string $cons Consulta SQL
   * @param miexed $values Arreglo con los valores a reemplazar por Parse_vars
   * @param boolean $ret retornar Array de datos o no.
   * @return mixed
   */
  public function query($cons, $values = null, $ret = false)
   {
    $query = ($values != null) ? $this->parse_vars($cons, $values) : $cons;
    if($ret == true)
     {
      $res = $this->_query($query);
      if($res !== false)
       {
        $return = $res->fetch_assoc();
        $res->free();
       }
      else
       {
        $return = false;
        $this->error($query);
       }
     }
    else
     {
      $return = new Query($query, $this->errors, $this->conn);
      ++self::$count;
     }
    return $return;
   }



  /**
   * Seleccionamos campos de una tabla
   * @param string $table Nombre de la tabla objetivo
   * @param array|string $fields
   * @param array $condition Condicionante para la selecci�n
   * @param array|integer $limit L�mite de filas
   * @return array
   */
  public function select($table, $fields, $condition = null, $limit = null)
   {
    $cons = 'SELECT '.(is_array($fields) ? implode(', ', $fields) : $fields).' FROM '.$this->data['name'].'.'.$this->prefix.$table.' '.$this->parse_where($condition).' LIMIT '.(($limit !== null) ? ((is_array($limit)) ? $limit[0].', '.$limit[1] : '0, '.$limit) : '0, 1');
    $query = $this->_query($cons);
    if(!$query || $query == false)
     {
      $this->error($cons);
      return false;
     }
    else
     {
      if($limit !== null && $limit > 1)
       {
        return new \Framework\Query($cons, $this->errors, $this->conn);
        ++self::$count;
       }
      else
       {
        return $query->fetch_assoc();
       }
     }
   }



  /**
   * Insertar Datos en una tabla
   * @param string $table Nombre de la tabla
   * @param array $data Arreglo asosiativo con los datos
   * @return integer|boolean N�mero de filas afectadas o False.
   */
  public function insert($table, $data)
   {
    if(is_array($data) === true)
     {
      // Tenemos una inserci�n de m�ltiples filas
      if(isset($data[self::FIELDS]) && isset($data[self::VALUES]))
       {
        $fields = implode(', ', $data[self::FIELDS]);
        $values = array();
        foreach($data[self::VALUES] as $row)
         {
          $values[] = '( '.$this->parse_input($row).' )';
         }
        $values = implode(', ', $values).';';
       }
      else
       {
        $fields = implode(', ', array_keys($data));
        $values = '( '.$this->parse_input($data).' )';
       }
      $cons = 'INSERT INTO '.$this->data['name'].'.'.$this->prefix.$table.' ( '.$fields.' ) VALUES '.$values;
      $query = $this->_query($cons);
      // Seteamos el resultado,
      return (!$query || $query == false) ? $this->error($cons) : $this->conn->insert_id;
     }
    else { return false; }
   }




  /**
   * Borrar una fila
   * @param string $table nombre de la tabla
   * @param array $cond Condicionantes
   * @return integer|boolean N�mero de filas afectadas o False.
   */
  public function delete($table, $cond)
   {
    if(is_array($cond) === true)
     {
      $cons = 'DELETE FROM '.$this->data['name'].'.'.$this->prefix.$table.' '.$this->parse_where($cond);
      $query = $this->_query($cons);
      return (!$query || $query == false) ? $this->error($cons) : $this->conn->affected_rows;
     } else { return false; }
   }



  /**
   * Actualizar una fila
   * @param string $table nombre de la tabla
   * @param array $array Arreglo asosiativo con los datos
   * @param array $cond Condicionantes
   * @return integer|boolean N�mero de filas afectadas o False.
   */
  public function update($table, $array, $cond)
   {
    if(is_array($cond) === true)
     {
      $fields = array();
      foreach($array as $field => $value)
       {
        $fields[] = $field.' = '.((is_array($value)) ? $field.' '.$value[0].' '.(int) $value[1]: $this->parse_input($value));
       }
      $cons = 'UPDATE '.$this->data['name'].'.'.$this->prefix.$table.' SET '.implode(', ', $fields).' '.$this->parse_where($cond);
      $query = $this->_query($cons);
      return (!$query || $query == false) ? $this->error($cons) : $this->conn->affected_rows;
     } else { return false; }
   }



  /**
   * Ejecutamos una consulta
   * @param string $query Cosulta SQL
   * @return resource
   */
  private function _query($query)
   {
    ++self::$count;
    if($this->logger !== null)
     {
      call_user_func_array($this->logger, array($query));
     }
    return mysqli_query($this->conn, $query);
   }



  /**
   * Retornamos un error grave del servidor
   * @param string $query Consulta que origina el error
   * @return nothing
   */
  public function error($query, $error = null)
   {
    if($this->errors !== null)
     {
      call_user_func_array($this->errors, array($query, (($error !== null) ? $error : mysqli_error($this->conn)) ));
     }
   }



  /**
   * Preparamos un condicionante
   * @param array $conditions Arreglo de Condiciones
   * @return string Condiciones ya preparadas
   */
  protected function parse_where($conditions)
   {
    if(is_array($conditions))
     {
      $array = array();
      foreach($conditions as $field => $value)
       {
        if(is_array($value))
         {
          $other_values = array();
          foreach($value as $other_value)
           {
            $other_values[] = $field.' = '.$this->parse_input($other_value);
           }
          $array[] = '('.implode(' OR ', $other_values).')';
         }
        else
         {
          $array[] = $field.' = '.$this->parse_input($value);
         }
       }
      return 'WHERE '.implode(' AND ', $array);
     }
    else
     {
      return '';
     }
   }



  /**
   * Funcion encargada de realizar el parseo de la consulta SQL agregando las
   * variables de forma segura mediante la validacion de los datos.
   * En la consulta se reemplazan los ? por la variable en $params
   * manteniendo el orden correspondiente.
   * @param string $q Consulta SQL
   * @param array $params Arreglo con los parametros a insertar.
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
   */
  protected function parse_vars($q, $params)
   {
    // Si no es un arreglo lo convertimos
    if(!is_array($params)) { $params = array($params); }
    //Validamos que tengamos igual numero de parametros que de los necesarios.
    if(count($params) != preg_match_all("/\?/", $q, $aux))
     {
      throw new LittleDB_Exception('No coinciden la cantidad de parametros necesarios con los provistos en '.$q);
      return $q;
     }
    //Reemplazamos las etiquetas.
    foreach($params as $param)
     {
      $q = preg_replace("/\?/", $this->parse_input($param), $q, 1);
     }
    return $q;
   }


  /**
   * Funci�n que se encarga de determinar el tipo de datos para ver si debe
   * aplicar la prevenci�n de inyecciones SQL, si debe usar comillas o si es
   * un literal ( funcion SQL ).
   * @param mixed $objet Objeto a analizar.
   * @return string Cadena segura.
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
  */
  protected function parse_input($object)
   {
    if(is_object($object)) //Es un objeto?
     {
      return (string) $object;
     }
    elseif(is_int($object)) // Es un n�mero?
     {
      return (int) $object;
     }
    elseif($object === NULL) // Es nulo?
     {
      return 'NULL';
     }
    elseif(is_array($object)) //Es un arreglo?
     {
      return implode(', ', array_map(array($this, 'parse_input'), $object));
     }
    else //Suponemos una cadena
     {
      return '\''.mysqli_real_escape_string($this->conn, $object).'\'';
     }
   }
 }



/**
 * Clase para manipular resultados de consultas MySQL, esta clase no es
 * comunmente accesible y es creada por LittleDB
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class Query
 {
  /**
   * Recurso MySQL
   * @var resource
   */
  private $data = false;

  /**
   * Resultado de la consulta
   * @var array
   */
  private $result = array();

  /**
   * Posici�n
   * @var integer
   */
  private $position = 0;

  /**
   * Nro de filas
   * @var integer
   */
  public $rows = 0;



  /**
   * Inicializar los datos
   * @param string $query Consulta SQL
   * @param string $eh Nombre de la funci�n que manipula los errores
   * @param resource $conn Recurso de conecci�n SQL
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public function __construct($query, $eh, $conn)
   {
    $cons = mysqli_query($conn, $query);
    if(is_object($cons))
     {
      $this->data = $cons;
      $this->position = 0;
      $this->rows = $this->data->num_rows;
      return true;
     }
    else
     {
      if($eh !== null)
       {
        call_user_func_array($eh, array($query, mysqli_error($conn)));
       }
     }
   }



  /**
   * Cuando destruimos el objeto limpiamos la consulta.
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
   * @return boolean
   */
  public function __destruct()
   {
    return $this->free();
   }



  /**
   * Limpiamos la consulta
   * @author Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
   * @return boolean
   */
  private function free()
   {
    return (is_resource($this->data)) ? $this->data->free() : true;
   }



  /**
   * Devolvemos el array con los datos de la consulta
   * @param string $field Campo objetivo.
   * @param string $default Valor a retornar si el campo no existe o est� vac�o.
   * @return array|string Todos los campos o s�lo uno
   */
  public function fetch($field = null, $default = null)
   {
    $this->result = $this->data->fetch_assoc();
    if($field !== null) // Pedimos un campo en especial
     {
      return (isset($this->result[$field])) ? $this->result[$field] : $default;
     }
    else
     {
      return $this->result;
     }
   }

 }



/**
 * Excepci�n exclusiva de LittleDB.
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @access private
 */
class LittleDB_Exception Extends \Exception { }