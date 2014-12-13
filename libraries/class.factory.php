<?php
/**
 * Implementación del patrón de diseño Factory
 * @package class.factory.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

use \Framework\Models;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class Factory Extends Component
 {
  /**
   * Referencias de los modelos.
   * @var array
   */
  protected static $handlers = array();



  /**
   * Intentamos recrear el patrón de diseño ObjectPool con unos ligeros cambios
   * @param string $model Modelo a Cargar
   * @param integer $id Identificador
   * @param array $specified_fields Campos específicos a cargar por el modelo
   * @param bool $autoload Marcamos la autocarga de datos del modelo.
   * @param bool $protected Lo marcamos para no ser limpiado en la redirección
   * @return reference
   */
  final public static function &create($model, $id = null, $specified_fields = null, $autoload = true, $protected = false)
   {
    if(class_exists($model) === false)
     {
      if(file_exists(MODELS_DIR.'class.'.strtolower($model).EXT) === true)
       {
        require_once(MODELS_DIR.'class.'.strtolower($model).EXT);
       }
      else
       {
        throw new Factory_Exception('No se ha podido cargar el modelo '.$model.'.');
       }
     }

    $modelname = '\Framework\Models\\'.$model;
    if($protected === false && $id !== null)
     {
      if(isset(self::$variables[$model.'-'.$id]) === false)
       {
        self::$variables[$model.'-'.$id] = new $modelname($id, $specified_fields, $autoload);
       }
      return self::$variables[$model.'-'.$id];
     }
    else
     {
      if(isset(self::$handlers[$model]) === false)
       {
        self::$handlers[$model] = new $modelname($id, $specified_fields, $autoload);
       }
      return self::$handlers[$model];
     }
   } // final public static function create();



  /**
   * Borramos todas las instancias
   * @return nothing
   */
  final public static function clear()
   {
    self::$models = array();
   } // final public static function clear()



  /**
   * Procesamos un arreglo de ID's llevándolos a ser Modelos.
   * @param array $target_ids ID's Objetivo
   * @param string $object Modelo a retornar
   * @param null|array $fields Campos específicos a ser cargados por cada Modelo
   * @param boolean $autoload Autocargamos los datos
   * @param boolean $return_array Solicitamos los datos como un arreglo
   * @return nothing
   */
  final public static function create_from_array($target_ids = array(), $model, $fields = null, $autoload = true, $return_array = false)
   {
    $classes = array();
    foreach($target_ids as $id)
     {
      $object = self::create($model, (int) $id, $fields, $autoload);
      if($autoload === true AND $return_array === true)
       {
        $classes[] = $object->get_array();
       }
      else
       {
        $classes[] = $object;
       }
     }

    return $classes;
   } // final public static function create_from_array();
 } // final class Factory Extends Component();

/**
 * Excepción única del componente Factory
 * @access private
 */
class Factory_Exception Extends \Exception {}