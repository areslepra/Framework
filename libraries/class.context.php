<?php
/**
 * Control de Contextos. (wrapper adaptado)
 * @package class.component.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

final class Context Extends Component
 {
  /**
   * Cantidad de Contextos o Alias cargados
   * @var integer
   */
  public static $count = 0;

  /**
   * Llamamos a la función definitoria del contexto.
   * @param string $function Contexto objetivo
   * @return boolean|mixed
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public static function check($function, $forced_arguments = null)
   {
    if(is_array($function) === true)
     {
      $functions_count = count($function);
      if($functions_count === count($forced_arguments))
       {
        for($i = 0; $i < $functions_count; ++$i)
         {
          self::check($function[$i], $forced_arguments[$i]);
         }
       }
     }

    if(array_key_exists($function, self::$variables) === true)
     {
      return call_user_func_array((array) self::$variables[$function][0], (($forced_arguments !== null AND is_array($forced_arguments)) ? (array) $forced_arguments : (array) self::$variables[$function][1] ));
     }
    elseif($function === null)
     {
      return true;
     }
    else
     {
      throw new Context_Exception('Se intenta consultar un contexto inexistente ('.$function.').');
     }
   } // public static function check();



  /**
   * Agregamos una función de contexto
   * @param string $name Nombre del conexto
   * @param string $function Función objetivo
   * @return nothing
   * @author Cody Roodaka <roodakazo@gmail.com>
   */
  public static function add($name, $function, $arguments = null)
   {
    if(is_callable($function) === true)
     {
      self::$variables[$name] = array($function, $arguments);
      ++self::$count;
     }
    else
     {
      throw new Context_Exception('Se intenta agregar un contexto inv&aacute;lido '.json_encode($function).'.');
     }
   } // public static function add();
 } // final class Context();


/**
 * Excepción exclusiva del componente Context
 * @access private
 */
class Context_Exception Extends \Exception { } // class Context_Exception();