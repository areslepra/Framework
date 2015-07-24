<?php
namespace Framework\Controllers;

use \Framework as F;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Error Extends F\Controller
 {
 public function build_header()
   {

   }

  public function main()
   {
    echo F\Core::$error;
   }
 }