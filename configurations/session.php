<?php defined('ROOT') or exit('No tienes Permitido el acceso.');
return array(
 'session_table' => '',
 'duration' => 604800,
 'algorithm' => 'md5',
 'user_object' => null,
 'user_fields' => null,
 'cookie_life' => 300, // cinco minutos
 'cookie_name' => 'micookiepiola',
 'cookie_path' => DS,
 'cookie_domain' => $_SERVER['SERVER_NAME']
 );