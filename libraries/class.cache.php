<?php

namespace Framework;

defined('ROOT') or exit('No tienes Permitido el acceso.');

class Cache
 {

  /**
   * Configuración del componente
   * @var Array
   */
  protected static $configuration = array();

  final public static function init()
   {
    self::$configuration = get_config(str_replace('Framework\\', '', get_called_class()));
   } // final public static function init();




  public static function set($key, $value, $expires)
   {
    // Definimos variables
    $key = strtolower($key);

    $cache = serialize(array(
     'cache_data' => $value,
     'cache_expires' => (int) (($expires === null) ? self::$configuration['life'] : $expires),
     'cache_time' => time()));

    // Abrimos el archivo
    $file = fopen(CACHE_DIR.'data'.DS.$key.EXT, 'w');

    if(!$file)
     {
      throw new Cache_Exception('No se pudo abrir/crear el archivo "'.CACHE_DIR.'data'.DS.$key.EXT.'"');
     }
    elseif(!fwrite($file, '<?php defined(\'ROOT\') or exit(\'No tienes Permitido el acceso.\'); return \''.$cache.'\';'))
     {
      throw new Cache_Exception('No se pudo escribir el archivo "'.CACHE_DIR.'data'.DS.$key.EXT.'"');
     }
    else
     {
      fclose($file);
      return true;
     }
   } // public static function set();



  public static function get($key)
   {
    $key = strtolower($key);
    // Incluimos y retornamos el archivo
    if(self::is_set($key))
     {
      $data = unserialize(require(CACHE_DIR.'data'.DS.$key.EXT));
      // Si todavía le queda 'vida' lo cargamos
      if($data['cache_time'] > (time() - $data['cache_expires']))
       {
        return $data['cache_data'];
       }
      else
       {
        self::delete($key);
        return false;
       }
     }
    else
     {
      return false;
     }
   } // public static function get();



  private static function is_set($key)
   {
    return (is_file(CACHE_DIR.'data'.DS.$key.EXT)) ? true : false;
   } // private static function is_set();



  public static function delete($key)
   {
    if(self::is_set($key) === true)
     {
      return unlink(CACHE_DIR.'data'.DS.$key.EXT);
     }
    else { return true; }
   } // public function delete($id)



  // Tamaño de la Cache
  public static function size()
   {
    $size = 0;
    $dir = opendir(CACHE_DIR.'data'.DS);
    if(!$dir)
     {
      throw new Cache_Exception('No se pudo abrir el directorio '.CACHE_DIR.'data'.DS);
     }
    else
     {
      while(($file = readdir($dir)) !== false)
       {
        if($file !== '.' || $file !== '..')
         {
          $size += filesize(CACHE_DIR.'data'.DS.$file);
         }
       }
      closedir($dir);
      return $size;
     }
    return 0;
   } // public static function size();



  // Limpiar Cache
  public static function clear()
   {
    $dir = opendir(CACHE_DIR.'data'.DS);
    if(!$dir)
     {
      throw new Cache_Exception('No se pudo abrir el directorio '.CACHE_DIR.'data'.DS);
     }
    else
     {
      while(($file = readdir($dir)) !== false)
       {
        if($file !== '.' || $file !== '..')
         {
          unlink(CACHE_DIR.'data'.DS.$file);
         }
       }
      closedir($dir);
     }
   } // public static function clear();
 } // class Cache();

class Cache_Exception Extends \Exception { }