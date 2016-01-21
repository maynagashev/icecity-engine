<?php

class class_adv {

var $data_dir = "/uploads/";
var $fn_db = "db.ser";
var $fn_last_id = "last_id";

var $db_path = "";
var $last_id_path = "";

var $last_id = 0;
var $db = array();

var $err = 0;
var $errm = array();

var $t = "adv"; // mysql table
var $stat_views_to_mysql = 0; // 

function __construct() {
  
  $this->data_dir = (defined("ADV_DATA_DIR")) ? ADV_DATA_DIR : $this->data_dir;
  $this->db_path = $this->data_dir.$this->fn_db;
  $this->last_id_path = $this->data_dir.$this->fn_last_id;
}
  
/**
 * Основная функция вывода кода баннера
 *
 * @return unknown
 */
function show() {
  
  if (!$this->load_db()) {
    return false;
  }
  
  if (!$this->load_last_id()) {
    return false;
  }
  
  // выборка номера следующего баннера
  $j = 0; $show = -1; $first = 0;
  foreach($this->db as $i => $d) { $j++;   if ($j==1) { $first = $i; }  
    // если нет значения берем первый попорядку
    if ($show < 0) { $show = $i; }
    // если встретили предыдщуий, сбрасывем значение, чтобы взять следующий
    if ($d['id']==$this->last_id) { $show = -1; }
  }  
  // если до сих пор нет значения, берем первый
  if ($show<0) {  $show = $first;  }
  
  
  // фиксируем текущий баннер
  $banner = $this->db[$show];    
  $this->write_last_id($banner['id']);
  
  // записываем показ в стату
  $this->stat_view($banner);
  
  // выводим баннер
  echo $banner['html'];
    
  return true;
}


/**
 * Основная функция фиксирующая переход
 *
 * @param unknown_type $id
 */
function click($id) {
   
  if (!$this->load_db()) {
    return false;
  }


  // выборка баннера
  $j = 0; $first = 0; $show = -1;
  foreach($this->db as $i => $d) { $j++;  if ($j==1) { $first = $i; }

    // если встретили нужный
    if ($d['id']==$id) { 
      $show = $i;
      break;      
    }
  }  
  // если до сих пор нет значения (баннер ненайден по id), берем первый
  if ($show<0) {  $show = $first;  }
      
   
  // фиксируем текущий баннер
  $banner = $this->db[$show];      
  
  // записываем переход в стату
  $this->stat_click($banner);
  
  // редиректим
  header("Location: {$banner['url']}");
  exit();    
  
  return true;
}

// STUFF =================================
function load_db() {
  
  if (!file_exists($this->db_path)) { 
    $this->err = 1;
    $this->errm[] = "ADV->DB_PATH {$this->db_path} - not exists"; 
    return false;
  }
  
  $data = file_get_contents($this->db_path);
  
  $ar = unserialize($data);
  if ($ar === false) { 
    $this->err = 1;
    $this->errm[] = "can't unserialize ADV->DB_PATH {$this->db_path}"; 
    return false;
  }
  
  if (count($ar)<=0) { 
    $this->err = 1;
    $this->errm[] = "ADV->DB array is empty";
    return false;
  }
  
  $this->db = $ar;
  
  return true;
}

function load_last_id() {
  
  if (!file_exists($this->last_id_path)) { 
    file_put_contents($this->last_id_path, $this->last_id);
  }
  
  if (!file_exists($this->last_id_path)) { 
    $this->err = 1;
    $this->errm[] = "ADV->LAST_ID_PATH {$this->last_id_path} - not exists & can't create"; 
    return false;
  }
  
  $id = file_get_contents($this->last_id_path);
  $id = abs(intval($id));
    
  $this->last_id = $id;
  
  return true;
}

function write_last_id($id) {
   file_put_contents($this->last_id_path, $id);
}

/**
 * Запись показа в стату
 *
 * @param unknown_type $id
 */
function stat_view($d) {
    
  if ($this->stat_views_to_mysql) {
    require(ADV_LIB_DIR."class_mysql.php");
    $db = new mysql(ADV_DIR."mysql_cfg.php");	
    $db->connect();
    
    $id = intval($d['id']);
    $db->q("UPDATE {$this->t} SET `views`=`views`+'1' WHERE id='{$id}'", __FILE__, __LINE__);
    $db->close();
  }
  else {
    require(ADV_LIB_DIR."class_vpointer.php");
    $vp = new class_vpointer(VPOINTER_DIR);	
    $vp->stat($d['id']);
  }
}

/**
 * Запись перехода в стату
 *
 */
function stat_click($d) {
  
  require(ADV_LIB_DIR."class_mysql.php");
  $db = new mysql(ADV_DIR."mysql_cfg.php");	
  $db->connect();
  
  $id = intval($d['id']);
  $db->q("UPDATE {$this->t} SET `clicks`=`clicks`+'1' WHERE id='{$id}'", __FILE__, __LINE__);
  $db->close();
}

//eoc
}

?>