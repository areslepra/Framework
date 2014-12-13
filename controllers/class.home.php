<?php

namespace Framework\Controllers;

use \Framework as F;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Home Extends F\Controller
 {

  public function build_header()
   {
    F\View::add_key('title', 'asd');
    F\View::add_file('css', 'asd.js'); // Agregamos un archivo aleatorio
   }



  public function main()
   {
    $model = load_model('Example');

    $array = (array) $model->get_by_name();

    $result = F\Factory::create_from_array($array, 'Example', null, true, true);

    F\View::add_key('users', $result);

    F\View::add_template('home');
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

        return F\Core::redirect('home', 'main');
       }
      else
       {
        F\View::add_key('user', $user->get_array());
        F\View::add_template('edit');
       }
     }
    else
     {
      return F\Core::redirect('home', 'main');
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
        F\Core::redirect('home', 'main');
       }
     }
    else
     {
      F\View::add_template('create');
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
    return F\Core::redirect('home', 'main');
   }
 } // class Home Extends Controller