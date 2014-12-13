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

final class Session Extends Component
 {
  /**
   * Instancia de LittleDB
   * @var Object
   */
  protected static $use_database = true;

  /**
   * Datos de la sesión actual
   * @var Array
   */
  protected static $variables = array(
   'hash' => null,
   'user_id' => null,
   'ip' => null,
   'datetime' => null,
   'use_cookies' => null);

  /**
   * Configuración de las sesiones
   * @var Array
   */
  protected static $configuration = array(
   'duration' => null,
   'algorithm' => null,
   'user_object' => null,
   'user_fields' => null,
   'cookie_life' => null,
   'cookie_name' => null,
   'cookie_path' => null,
   'cookie_domain' => null);


  /**
   * Objeto de usuario a gestionar
   * var Object
   */
  protected $user = null;

  /**
   * Iniciamos la sesión
   * @return nothing
   */
  public static function start()
   {
    if(!isset($_SESSION) || session_id() == '')
     {
      // Iniciamos datos predeterminados para la sesión
      session_start();
      $_SESSION['hash'] = null;
      $_SESSION['user_id'] = null;
      $_SESSION['use_cookies'] = false;
      $_SESSION['datetime'] = time();
      $_SESSION['ip'] = ip2long($_SERVER['REMOTE_ADDR']);
     }

    if(isset($_COOKIE[self::$configuration['cookie_name']]))
     {
      self::$variables['hash'] = $_COOKIE[self::$configuration['cookie_name']];
      self::$variables['use_cookies'] = true;
     }
    elseif($_SESSION['hash'] !== null)
     {
      self::$variables['hash'] = $_SESSION['hash'];
      self::$variables['use_cookies'] = false;
     }

    self::set_id();

    Context::add('is_logged', array('Framework\Session', 'is_user_id'));
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
    if(self::$variables['hash'] !== null && $_SESSION['hash'] = self::$variables['hash'] && $value == self::$variables['hash'])
     {
      $hash = ($value !== null) ? hash(self::$configuration['algorithm'], $value) : self::$variables['hash'];
      $cookies = (boolean) ($cookies !== null) ? $cookies : self::$variables['use_cookies'];

      // De existir la sesión en la base de datos, la actualizamos. Èsto sólo
      // sucedería si el usuario ingresa desde otra computadora.
      $query = self::$db->select('user_sessions', '*', array('hash' => $hash));
      if($query !== false)
       {
        if($_SESSION['ip'] == $query['ip']) // Dirección de IP
         {
          if(self::$configuration['duration'] > ($_SESSION['datetime'] - $query['datetime'])) // Vida
           {
            $_SESSION['hash'] = $hash;
            $_SESSION['user_id'] = $query['user_id'];
            $_SESSION['datetime'] = time();
            $_SESSION['use_cookies'] = $cookies;
            return self::$db->update('user_sessions', array(
             'datetime' => $_SESSION['datetime'],
             'use_cookies' => $cookies
             ), array('hash' => $_SESSION['hash']));
           }
         }
        return self::end();
       }
      else
       {
        // Seteamos una nueva sesión
        return self::$db->insert('user_sessions', array(
         'hash' => $hash,
         'user_id' => $id,
         'ip' => $_SESSION['ip'],
         'datetime' => $_SESSION['datetime'],
         'use_cookies' => $cookies));
       }
     }
   } // public static function set_id();



   /**
    * Seteamos el modelo de usuario
    * @return nothing
    */
  private static function set_user_object()
   {
    if(self::is_user_id)
     {
      self::$user = Factory::create(self::$configuration['user_object'], self::$variables['user_id'], self::$configuration['user_fields'], true, true, false);
     }
   } // private static function set_user_object();



   /**
    * Chequeamos si la sesión es de un usuario válido
    * @return bool
    */
  public static function is_user_id()
   {
    return (self::$variables['user_id'] !== null);
   } // private static function is_user_id()



  /**
   * Terminamos la sesión.
   * @return Nothing
   */
  public static function end()
   {
    // Borramos la sesión por el lado de la base de datos.
    $this->db->delete('sessions', array('hash' => $_SESSION['hash']), false);
    // Si existe una cookie, la destruímos
    if(isset($_COOKIE))
     {
      setcookie(self::$configuration['cookie_name'], $_SESSION['id'], (time() - $configuration['cookie_life']), self::$configuration['cookie_path'], self::$configuration['cookie_domain']);
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

// Iniciamos el componente
Session::init();
Session::start();