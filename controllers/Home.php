<?php

namespace Roodaka\Framework\Controller;

use \Roodaka\Framework\Controller;
use \Roodaka\Framework\Core;
use \Roodaka\Framework\Factory;
use \Roodaka\Framework\View;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Home Extends Controller
 {

  public function build_header()
   {
    View::add_key('title', 'asd');
    View::add_file('css', 'asd.js'); // Agregamos un archivo aleatorio
   }



  public function main()
   {
    $model = \Roodaka\Framework\load_model('Example');

    $array = (array) $model->get_by_name();

    $result = Factory::create_from_array($array, 'Example', null, true, true);

    View::add_key('users', $result);

    View::add_template('home');
    //return Framework\Core::redirect('Other');
   }



  public function edit()
   {
    $id = get_routing_value();
    if($id !== null)
     {
      $user = load_model('Example', (int) $id, null, true);

      if($this->post_count >= 2)
       {
        $user->name = $this->post['name'];
        $user->lastname = $this->post['lastname'];

        return Core::redirect('home', 'main');
       }
      else
       {
        View::add_key('user', $user->get_array());
        View::add_template('edit');
       }
     }
    else
     {
      return Core::redirect('home', 'main');
     }
   }



  public function create()
   {
    if($this->post_count >= 2)
     {
      $new_user = load_model('Example');
      $new_user->name = $this->post['name'];
      $new_user->lastname = $this->post['lastname'];
      $new_user->datetime = time();

      if($new_user->save() === true)
       {
        Core::redirect('home', 'main');
       }
     }
    else
     {
      View::add_template('create');
     }
   }



  public function delete()
   {
    $id = get_routing_value();
    if($id !== null)
     {
      $user = load_model('Example', (int) $id, null, true);
      $user->set_to_delete();
     }
    return Core::redirect('home', 'main');
   }
 } // class Home Extends Controller