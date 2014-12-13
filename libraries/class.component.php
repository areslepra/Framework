<?php
/**
 * Abstracción de componentes
 * @package class.component.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Component
 {
  /**
   * Instancia de LittleDB
   * @var object
   */
  protected static $db = null;

  /**
   * Indicamos si se utiliza una base de datos o no.
   * @var boolean
   */
  protected static $use_database = false;

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
   * Abstracción del inicializador común de los componentes
   * @return nothing
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  final public static function init()
   {
    $config = get_config(str_replace('Framework\\', '', get_called_class()));
    if($config !== null)
     {
      self::configure($config);
     }

    if(self::$use_database === true)
     {
      self::init_database();
     }
   }



  /**
   * Reconfiguramos el componente
   * @param array $configuration Nueva configuración
   * @return nothing
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public static function configure($configuration = array())
   {
    self::$configuration = $configuration;
   } // final public static function configure();



  /**
   * Inicializador interno de la base de datos
   * @return nothing
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  final protected static function init_database()
   {
    self::$db = Framework\LittleDB::get_instance();
   } // final protected static function init_database();



  /**
   * No permitimos la clonación de este objeto
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  final public function __clone()
   {
    throw new Component_exception('La clonación de este objeto '.get_called_class().' no está permitida.');
   }  // public function __clone();
 } // abstract class Component();


/**
 * Excepción exclusiva del Componente maestro.
 * @access private
 */
class Component_Exception Extends \Exception {}