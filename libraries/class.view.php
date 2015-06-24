<?php
/**
 * Manejo de Vistas
 * @package class.view.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class View
 {
  /**
   * Configuración del componente
   * @var Array
   */
  protected static $configuration = array();

  /**
   * Variables internas del componente
   * @var Array
   */
  protected static $variables = array();

  /**
   * Arreglo con plantillas
   * @var array
   */
  protected static $templates = array();

  /**
   * Archivos Extra
   * @var Array
   */
  protected static $files = array(
   'js' => array(),
   'css' => array(),
   'lang' => array());



  final public static function init()
   {
    self::$configuration = get_config(str_replace('Framework\\', '', get_called_class()));
   }



  /**
   * Agregar una clave con su respectivo valor al arreglo de claves
   * @param string $key Clave a asignar
   * @param mixed $value Valor de la clave
   * @return nothing
   */
  public static function add_key($key, $value = null)
   {
    if(is_array($key) AND $value === null) { self::$variables += $key; }
    else { self::$variables[$key] = $value; }
   } // public static function add_template();



  /**
   * Agregamos un archivo para que sea cargado en el header
   * @param string $type Tipo de archivo a agregar.
   * @param string $name Ruta del archivo
   * @return nothing
   */
  public static function add_file($type, $name)
   {
    self::$files[$type][] = $name;
   } // public static function add_file();




  /**
   * Agregar una nueva plantilla para mostrar
   * @param string $template Plantilla a asignar
   * @return nothing
   */
  public static function add_template($template)
   {
    self::$templates[] = $template;
   } // public static function add_template();



  /**
   * Cargamos todos los archivos de idioma solicitados
   * @param string $lang Idioma forzado
   * @return array
   */
  private static function load_language($lang = null)
   {

    $directory = VIEWS_DIR.'languages'.DS.strtolower($lang).DS;
    if(is_dir($directory) === true)
     {
      if(is_file($directory.'main.json'))
       {
        $result = array();
        $result += json_decode(file_get_contents($directory.'main.json'), true);

        if(count(self::$files['lang']) >= 1)
         {
          foreach(self::$files['lang'] as $file)
           {
            if(is_file($directory.$file.'.json'))
             {
              $result += json_decode(file_get_contents($directory.$file.'.json'), true);
             }
            else
             {
              throw new View_Exception('El archivo de idiomas ('.$directory.$file.'.json) no existe.');
             }
           }
         }

        return $result;
       }
      else
       {
        throw new View_Exception('El archivo principal de idiomas ('.$directory.'main.json) no existe.');
       }
     }
    else
     {
      throw new View_Exception('El directorio de idiomas \''.$directory.'\' no existe.');
     }
   }



  /**
   * Reiniciamos la clase borrando las variables y plantillas asignadas
   * @return nothing
   */
  public static function clear()
   {
    self::$variables = array();
    self::$templates = array();
    self::$files = array('js' => array(), 'css' => array(), 'lang'=> array());
   } // public static function clear();



  /**
   * Mostramos todas las plantillas cargadas
   * @param boolean $return Indica si retornar o no el HTML generado
   * @return nothing
   */
  public static function show()
   {
    if(count(self::$templates) >= 1)
     {
      $dir_theme = 'views'.DS.'html'.DS;
      $dir_base = explode('/', $_SERVER['REQUEST_URI']);
      array_shift($dir_base);
      array_pop($dir_base);
      $dir_base = '/'.implode('/', $dir_base).'/';

      self::add_key('site', get_config('site'));
      self::add_key('core_files', self::$files);
      self::add_key('core_paths', array(
       'theme' => $dir_theme,
       'base' => $dir_base));
      self::add_key('site', get_config('site'));

      // Instanciamos RainTPL
      require(THIRD_PARTY_LIBS_DIR.'class.raintpl.php');
      $rain = new Third_Party\RainTPL();

      // Configuramos Rain para trabajar
      Third_Party\raintpl::configure('base_url', $_SERVER['SERVER_NAME'].$dir_base);
      Third_Party\raintpl::configure('tpl_dir', 'views'.DS.'html'.DS);
      Third_Party\raintpl::configure('cache_dir', CACHE_DIR.'html'.DS);

      $rain->assign('lang', self::load_language(self::$configuration['lang']));
      $rain->assign(self::$variables);


      if(self::$configuration['start_with'] !== null)
       {
        $rain->draw(self::$configuration['start_with'], false);
       }

      // Recorremos el arreglo de plantillas y las vamos mostrando
      foreach(self::$templates as $template)
       {
        $rain->draw($template, false);
       }
      if(self::$configuration['end_with'] !== null)
       {
        $rain->draw(self::$configuration['end_with'], false);
       }
     }
    else // Consideramos una respuesta AJAX.
     {
      //TODO: Considerar idiomas
      if(count(self::$variables) >= 1)
       {
        echo json_encode(self::$variables);
       }
     }
   } // public function show();
 } // class View();


/**
 * Excepción exclusiva del componente View
 * @access private
 */
class View_Exception Extends \Exception { } // class View_Exception();

// Iniciamos el componente.
View::init();