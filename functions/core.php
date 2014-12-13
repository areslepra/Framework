<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
/**
 * libs/functions.php
 * Cody Roodaka
 * Creado el 03/04/2011 01:17 a.m.
 */


/**
 * Armamos una URL
 * @param string $mod Módulo objetivo
 * @param string $val Valor
 * @param string $sec Submódulo
 * @param int $page Número de página
 * @param string $title Título (mero SEO)
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function url($controller, $method = null, $value = null, $page = null, $title = null)
 {
  return 'index.php?'.Framework\Core::ROUTING_CONTROLLER_VARIABLE.'='.$controller
  .(($value !== null) ? '&'.Framework\Core::ROUTING_VALUE_VARIABLE.'='.$value : '')
  .(($title !== null) ? '-'.$title : '')
  .(($method !== null) ? '&'.Framework\Core::ROUTING_METHOD_VARIABLE.'='.$method : '')
  .(((int) $page >= 1) ? '&'.Framework\Core::ROUTING_PAGENUMBER_VARIABLE.'='.$page : '');
 } // function url();



function load_component($target)
 {
  if(!class_exists($target))
   {
    require_once(LIBRARIES_DIR.'class.'.strtolower($target).EXT);
   }
  else
   {
    throw new Exception('');
   }
 }



function load_model($model, $id = null, $specified_fields = null, $autoload = true, $protected = false)
 {
  return \Framework\Factory::create($model, $id, $specified_fields, $autoload, $protected);
 }



/**
 * Calcular el paginado para las consultas MySQL
 * @param int $page Número de página
 * @param int $limit Límite de resultados por página
 * @return array
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function paginate($page, $limit)
 {
  return array((($page - 1) * $limit), ($page * $limit));
 } // function paginate();



/**
 * Agregamos el manejo personalizado de las excepciones
 * @param object $exception Excepción entregada por el sistema
 * @return nothing
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function exception_handler($exception)
 {
  echo '<div>
   <h4>Fallo grave del sistema</h4>
   <p><b>Tipo</b>: '.str_replace('_Exception', '', get_class($exception)).'</p>
   <p><b>Mensaje</b>: '.$exception->getMessage().'</p>
   <p><b>C&oacute;digo</b>: '.$exception->getCode().'</p>
   <p><b>Archivo</b>: '.$exception->getFile().'</p>
   <p><b>L&iacute;nea</b>: '.$exception->getLine().'</p>
  </div>';
 } // function exception_handler();



/**
 * LDB: Improvisación para el manejo de errores
 * @param string $query Consulta que origina el error
 * @param string $error Mensaje de error provisto por el servidor MySQL
 * @return nothing
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function ldb_handle_error($query, $error)
 {
  exit('<h2>Database Error</h2>'.(($query !== '') ? '<span>Error en la consulta <i>'.$query.'</i></br>' : '').'<b>'.$error.'</b></span>');
 } // function ldb_error();



function get_config($file)
 {
  return require_once(CONFIGURATIONS_DIR.$file.EXT);
 }



function get_routing_controller() { return Framework\Core::$target_routing['controller']; }
function get_routing_method() { return Framework\Core::$target_routing['method']; }
function get_routing_value() { return Framework\Core::$target_routing['value']; }
function get_routing_page() { return Framework\Core::$target_routing['page']; }