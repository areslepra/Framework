<?php
/**
 * Abstracción de Controladores
 * @package class.controller.php
 * @author Cody Roodaka <roodakazo@gmail.com>
 * @version  $Revision: 0.0.1
 * @access public
 */

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

abstract class Controller
 {
  protected $permisson_required = null;

  protected $is_post = false;
  protected $post = array();
  protected $post_count = 0;

  protected $has_files = false;
  protected $files = array();
  protected $files_count = 0;



  public function __construct()
   {
    if($_SERVER['REQUEST_METHOD'] === 'POST')
     {
      $this->post = (array) $_POST;
      $this->post = $this->filterEntities($this->post);
      $this->post_count = count($_POST);
     }

    if(isset($_FILES) === true)
     {
      $this->has_files = true;
      $this->files = (array) $_FILES;
      $this->files_count = count($_FILES);
     }

    $this->build_header();
   } // protected function __construct();
   
   private function filterEntities($array)
   {
    
     foreach($array as $key => $valor)
     {
       $array[$key] = str_replace('<br />','[br]',nl2br(htmlentities($valor)));
     }
     return $array;
    
   }



  /**
   * Convertimos la clase una cadena
   * @return string
   */
  public function __toString()
   {
    return 'Controlador '.get_called_class();
   } // protected function __toString();



  /**
   * Invocamos al controlador
   * @return boolean
   */
  public function __invoke()
   {
    return $this->main();
   } // protected function __invoke();



  /**
   * Evitamos la clonación de controladores.
   * @return nothing
   */
  public function __clone()
   {
    throw new Controller_Exception('No se permite clonar este objeto '.$this->name.'.');
   } // protected function __clone();



  /**
   * Procesar los datos hasta ahora obtenidos, preparando el contexto del
   * controlador. Debe llamarse antes del método Main();
   * @return nothing
   */
  abstract protected function build_header(); // abstract protected function build_header();



  /**
   * Método predeterminado, es llamado cuando no hay una función especificada.
   * @return boolean
   */
  abstract protected function main(); // abstract protected function main()
 } // class Controller


/**
 * Excepción única de la clase Controller
 * @access private
 */
class Controller_Exception Extends \Exception { }