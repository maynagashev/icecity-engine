<?php

/**
 * Встроенный шаблонизатор 
 *
 */
class class_tpl {
  
  var $tpl_dir = "sources/tpl/";
  
/**
 * Компилирует выбранный файл шаблона в папке tpl и возвращает результат
 * 
 * @param unknown_type $fn
 * @return unknown
 */
function fetch($fn='') {
  global $sv;
  
  $path = $this->check_tpl_file($fn);
  
  $name = $this->path2name($path);
    
  // подключаем файл и вызываем соотв. функцию
  require($path);  
  return $name();
}
  
/**
 * компилирует указанный файл шаблона и выводит результат
 *
 * @param unknown_type $fn
 */
function display($fn = '') {  
  print($this->fetch($fn));  
}

/**
 * 
 */
function include_file($fn='', $vars = false) {
  $path = $this->check_tpl_file($fn);
  $name = $this->path2name($path);
  include_once($path);
  if ($vars!==false) {
    return $name($vars);
  }
  else {
    return $name();
  }
}

//stuff 
/**
 * функция генерирующая название функции из пути к шаблону
 * @example /sources/tpl/modules/main.php -> tpl_modules_main
 *
 * @param unknown_type $path
 * @return unknown
 */

function path2name($path) {
  
  // используем путь от папки tpl
  $path = preg_replace("#^.*tpl/#si", "", $path);
   
  $ar = explode("/", $path);  

  $name_ar = array();
  $name_ar[] = "tpl";

  foreach ($ar as $k) {
    
    // берем только название до точки
    $k = (preg_match("#^([^\.]*)\..*$#si", $k, $m)) ? $m[1] : $k; 

    // очищаем
    $k = preg_replace("#[^a-z0-9\_]#si", "", $k);
    
    if($k!='') {
      $name_ar[] = $k;
    }
  }
  
  $ret = implode("_", $name_ar);
  return $ret;
}

/**
 * Проверяет существование шаблона, возвращает корректный путь до файла для инклуда
 *
 * @param unknown_type $fn
 * @return unknown
 */
function check_tpl_file($fn='') {
  $fn = trim($fn);
  if (!$fn) {
    die("\$fn - not defined in some TPL functions");    
  }
  $path = $this->tpl_dir.$fn;
  if (!file_exists($path)) {
    die("TPL file not found: <b>{$path}</b>");
  }
  
  return $path;
}

function is_exists($fn='') {
  $fn = trim($fn);
  if ($fn=='') {
    return false;
  }
  
  $path = $this->tpl_dir.$fn;
  if (!file_exists($path)) {
    return false;
  }
  else {
    return true;
  }
  
}
//eoc
}

?>