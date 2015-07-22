<?php

define('DEV', true); // Modo Desarrollo

// Mostramos los errores sólo si el modo desarrollo está activo.
if(DEV === true)
 {
  error_reporting(E_ALL);
  ini_set('display_errors', true);
  $start = array(
   'time' => microtime(true),
   'ram' => memory_get_usage());
 }
else
 {
  $start = null;
  error_reporting(0);
  ini_set('display_errors', false);
 }

// Directorios
define('DS', DIRECTORY_SEPARATOR); // Un mero alias
define('EXT', '.php');
define('ROOT', dirname(__FILE__).DS);
define('CACHE_DIR', ROOT.'cache'.DS);
define('CONFIGURATIONS_DIR', ROOT.'configurations'.DS);
define('CONTROLLERS_DIR', ROOT.'controllers'.DS);
define('FUNCTIONS_DIR', ROOT.'functions'.DS);
define('LIBRARIES_DIR', ROOT.'libraries'.DS);
define('MODELS_DIR', ROOT.'models'.DS);
define('THIRD_PARTY_LIBS_DIR', LIBRARIES_DIR.DS.'third_party'.DS);
define('VIEWS_DIR', ROOT.'views'.DS);

// Cargamos cargador de composer
require(ROOT.'vendor'.DS.'autoload.php');

// Cargamos las funciones básicas del núcleo
require(FUNCTIONS_DIR.'core'.EXT);

set_exception_handler('exception_handler');

require(FUNCTIONS_DIR.'friendly'.EXT);

// Cargamos e iniciamos el núcleo.
load_component('Core');
\Framework\Core::init($start);
