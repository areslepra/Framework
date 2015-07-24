<?php
/**
 * Control de sesiones y cookies
 * @package class.session.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class Session
 {
  /**
   * Instancia de LittleDB
   * @var object
   */
  protected static $db = null;

  /**
   * Configuración del componente
   * @var Array
   */
  protected static $configuration = array();

  /**
   * Objeto de usuario a gestionar
   * var Object
   */
  public static $user = null;


  const SUCCESS = 1;
  const ERROR_INVALID_ID = 1;
  const ERROR_UNRECOGNIZED_IP = 2;
  const ERROR_TIMEOUT = 4;
  const ERROR_SQL_INSERT = 5;
  const ERROR_SQL_UPDATE = 6;

  /**
   * Iniciamos la sesión
   * @return nothing
   */
  final public static function init()
   {
    // Configuramos...
    self::$configuration = get_config(str_replace('Framework\\', '', get_called_class()));
    // Obtenemos una instancia de LDB para utilizar...
    self::$db = LittleDB::get_instance();

    if(!isset($_SESSION) OR session_id() == '')
     {
      session_start();
     }

    // Iniciamos datos predeterminados para la sesión
    if(!isset($_SESSION['hash']))
     {
      if(isset($_COOKIE[self::$configuration['cookie_name']]))
       {
        $_SESSION['hash'] = $_COOKIE[self::$configuration['cookie_name']];
        $_SESSION['use_cookies'] = true;
       }
      else
       {
        $_SESSION['hash'] = null;
        $_SESSION['use_cookies'] = false;
       }
      $_SESSION['ip'] = ip2long($_SERVER['REMOTE_ADDR']);
     }
    $_SESSION['datetime'] = time();

    if($_SESSION['hash'] !== null)
     {
      self::set_id();
     }




    Context::add('is_logged', array('Framework\Session', 'is_session'));
   } // public static function start();



  /**
   * Configuramos la sesión
   * @param String $mode Modo (ID o Hash) a setear
   * @param Integer $value Valor a setear
   * @param Boolean $cookies Usamos cookies o no.
   * @return boolean
   */
  public static function set_id($value = null, $cookies = null)
   {
    $hash = ($value !== null) ? hash(self::$configuration['algorithm'], $value) : $_SESSION['hash'];
    $cookies = (boolean) ($cookies !== null) ? $cookies : $_SESSION['use_cookies'];

    // De existir la sesión en la base de datos, la actualizamos. Èsto sólo
    // sucedería si el usuario ingresa desde otra computadora.
    $query = self::$db->select(self::$configuration['session_table'], '*', array('hash' => $hash));
    if($query !== false AND $query !== null)
     {
      if($_SESSION['ip'] == $query['session_ip']) // Dirección de IP
       {
        if(self::$configuration['duration'] > ($_SESSION['datetime'] - $query['session_datetime'])) // Vida
         {
          if(self::$db->update(self::$configuration['session_table'], array('session_datetime' => $_SESSION['datetime'], 'session_use_cookies' => $cookies), array('hash' => $_SESSION['hash'])) !== false)
           {
            $result = self::SUCCESS;
           } else { $result = self::ERROR_SQL_UPDATE; }
         } else { $result = self::ERROR_TIMEOUT; }
       } else { $result  = self::ERROR_UNRECOGNIZED_IP; }
     }
    else
     {
      // Seteamos una nueva sesión
      if(self::$db->insert(self::$configuration['session_table'], array(
       'hash' => $hash,
       'user_id' => $value,
       'session_ip' => $_SESSION['ip'],
       'session_datetime' => $_SESSION['datetime'],
       'session_use_cookies' => $cookies)) !== false) { $result = self::SUCCESS; }
      else { $result = self::ERROR_SQL_INSERT; }
     }

    if($result === self::SUCCESS)
     {
      $_SESSION['hash'] = $hash;
      $_SESSION['user_id'] = (!isset($query['user_id'])) ? $value : $query['user_id'];
      $_SESSION['datetime'] = time();
      $_SESSION['use_cookies'] = $cookies;
      self::set_user_object();
      if($cookies === true)
       {
        setcookie(self::$configuration['cookie_name'], $_SESSION['hash'], (time() + $configuration['cookie_life']), self::$configuration['cookie_path'], self::$configuration['cookie_domain']);
       }

      return true;
     }
    else
     {
      if($result === self::ERROR_UNRECOGNIZED_IP OR $result === self::ERROR_TIMEOUT OR $result === self::ERROR_SQL_UPDATE)
       {
        self::end();
       }
     return false;
     }
   } // public static function set_id();



   /**
    * Seteamos el modelo de usuario
    * @return nothing
    */
  private static function set_user_object()
   {
    if(self::$configuration['user_object'] !== null)
     {
      self::$user = Factory::create(self::$configuration['user_object'], $_SESSION['user_id'], self::$configuration['user_fields'], true, true);
      self::$user = self::$user->get_array();
     }
   } // private static function set_user_object();



   /**
    * Chequeamos si la sesión es de un usuario válido
    * @return bool
    */
  public static function is_session()
   {
    return (isset($_SESSION) AND self::$user !== null);
   } // private static function is_user_id()



  /**
   * Terminamos la sesión.
   * @return Nothing
   */
  public static function end()
   {
    // Borramos la sesión por el lado de la base de datos.
    self::$db->delete(self::$configuration['session_table'], array('hash' => $_SESSION['hash']), false);
    // Si existe una cookie, la destruímos
    if(isset($_COOKIE))
     {
      setcookie(self::$configuration['cookie_name'], $_SESSION['hash'], (time() - self::$configuration['cookie_life']), self::$configuration['cookie_path'], self::$configuration['cookie_domain']);
      unset($_COOKIE);
     }
    // Destruímos la sesión por el lado del compilador.
    unset($_SESSION);
    session_regenerate_id(true);
   } // public static function end();
 } // final class Session();


/**
 * Excepción exclusiva del componente Session
 * @access private
 */
class Session_Exception Extends \Exception {} // class Session_Exception();