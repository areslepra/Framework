<?php
namespace Roodaka\Framework;
/**
 * libraries/functions.friendly.php
 * Cody Roodaka 2011
 * Creado el 03/04/2011 01:17 a.m.
 */

defined('ROOT') or exit('No tienes Permitido el acceso.');

/**
 * Cortar un texto si es necesario
 * @param string $text Texto a cortar
 * @param int $max Cantidad máxima de caracteres
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function resizetext($text, $max = 100)
 {
  return (isset($text{$max})) ? substr($text, 0, $max).'&hellip;' : $text;
 } // function resizetext();



/**
 * Devuelve el valor redondeado para mejor entendimiento (?
 * @param int $size Tamaño del archivo
 * @return string Tamaño redondeado
 * @author Cody Roodaka <roodakazo@gmail.com>
 */
function roundsize($size)
 {
  $ext = array('b', 'kb', 'mb', 'gb');
  $i = 0;
  while(($size / 1024) > 1)
   {
    $size = $size / 1024;
    $i++;
   }
  return round($size, 2).' '.$ext[$i];
 } // function roundsize();


function generate_pagination($actual_page, $total_rows, $rows_per_page = 10, $buttons_to_show = 10)
 {
    // Calculamos la cantidad de páginas y lo seteamos
    $div = ceil($total_rows / $rows_per_page);

    if(($total_rows - $rows_per_page) >= 1 && $div == 1 )
     {
      $total_pages = 2;
     }
    elseif($div > 1)
     {
      $total_pages = $div;
     }
    else
     {
      $total_pages = 1;
     }

  // Inicializamos el arreglo principal
  $result = array();
  // Seteamos los botones de previo e inicio
  if($actual_page == 1) { $result['first'] = 0; }
  else { $result['first'] = 1; }

  if($actual_page > 1) { $result['prev'] = ($actual_page - 1); }
  else { $result['prev'] = 0; }

  // Calculamos el punto de partida para el conteo
  $start = floor($buttons_to_show / 2);
  // Nos aseguramos de que si es posible siempre arranque desde el medio
  if($start < $total_pages && $start > 0)
   {
    // indicamos que la actual estará (o lo intentará) estar en el medio.
    $calc = ($actual_page - $start);
    // chequeamos que no sea ni negativo ni cero.
    if($calc < 1) { $c = 1; }
    else { $c = $calc; }
   }
  else
   {
    // iniciamos desde 1
    $c = 1;
   }
  // Bucle! Corremos el paginado.
  // $l indica la cantidad de páginas que se están mostrando
  // $c indica el número de página que se está mostrando
  $l = 1;
  while($l <= $buttons_to_show)
   {
    if($c <= $total_pages)
     {
      $result['pages'][] = $c;
     }
    ++$l;
    ++$c;
   }

  if($actual_page == $total_pages)
   {
    $result['next'] = 0;
    $result['last'] = 0;
   }
  else
   {
    $result['next'] = ($page + 1);
    $result['last'] = $total_pages;
   }

  $result['self'] = $page;
  return $result;
 } // function generate_pagination