<?php

namespace Framework\Models;

use Framework as F;

class Example extends F\Model
 {
  /**
   * Tabla objetivo
   * @var string
   */
  protected $table = 'example';
  /**
   * Nombre del campo primario, generalmente ID
   * @var string
   */
  protected $primary_key = 'id';

  /**
   * Lista de campos pertenecientes a este objeto
   * @var array
   */
  protected $fields = array(
   'name',
   'lastname',
   'datetime');

  public function get_by_name($order = 'ASC', $page = 1, $limit = 10)
   {
    $limits = paginate($page, $limit);
    $query = $this->db->query('SELECT '.$this->primary_key.' FROM '.$this->table.' ORDER BY name '.$order.' LIMIT '.$limits[0].', '.$limits[1]);
    if($query !== false)
     {
      $ids = array();
      while($id = $query->fetch())
       {
        $ids[] = (int) $id[$this->primary_key];
       }
      return $ids;
     }
   }


  protected function delete()
   {
    return $this->db->delete($this->table, array($this->primary_key => $this->id));
   }
 } // class User();