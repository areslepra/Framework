<?php
namespace Roodaka\Framework\Controller;

use \Roodaka\Framework\Controller;
use \Roodaka\Framework\Core;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Error Extends Controller
 {
 public function build_header()
   {

   }

  public function main()
   {
    echo Core::$error;
   }
 }