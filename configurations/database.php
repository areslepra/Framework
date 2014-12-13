<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 // Dominio del servidor
 'host' => 'localhost',
 // Puerto del servidor
 //'port' => null,
 // Usuario
 'user' => 'root',
 // Contraseña
 'password' => '',
 // Base de Datos
 'database' => 'framework',
 // Prefijo de la base de datos
 'prefix' => '',
 // Tipo de base de datos
 'dbm_type' => 'mysql', // Unused -for now-
 // Función para el manejo de errores
 'errors_handler' => 'ldb_handle_error',
 // Función para el registro
 'logs_handler' => null,
 );