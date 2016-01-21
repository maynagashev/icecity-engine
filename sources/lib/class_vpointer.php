<?php

/**
 * Класс для фиксации колчиества просмотров в высоконагруженных проектах
 *
 */

class class_vpointer {

/**
 * Рабочая папка со статистич. данными берется из VPOINTER_DIR
 *
 * @var unknown_type
 */
var $work_dir = "work/";

/**
 * Название файла с текущим указателем
 *
 * @var unknown_type
 */
var $pointer_file = "vpointer";

/**
 * Префикс для имен файлов со статистикой
 *
 * @var unknown_type
 */
var $pointer_prefix = "views";

/**
 * Название таблицы mysql и названия полей для обновления 
 *
 * @var unknown_type
 */
var $table = "adv";
var $field_id = "id";
var $field_views = "views";

//vars
var $pointer_file_path;
var $c_stat_file;
var $n_stat_file;
var $time;
var $min;
var $err = 0;
var $errm = array();

var $obect_id = 0;
var $matched = 0;

function __construct($work_dir = "") {
    
  $this->time = date("YmdHis");
  $this->min = date("YmdHi");
   
  $this->init_dir($work_dir);
  
}

/**
 * Инициализация путей и файлов
 *
 * @param unknown_type $work_dir
 */
function init_dir($work_dir = "") {
  
  if ($work_dir!='') {
    $this->work_dir = $work_dir;
  }
  if (!file_exists($this->work_dir) || !is_dir($this->work_dir)) {
    $this->err = 1;
    $this->errm[] = "Vpointer work_dir {$this->work_dir} not exists";
  }
  $this->pointer_file_path = $this->work_dir.$this->pointer_file;
  $this->n_stat_file = $this->work_dir.$this->pointer_prefix.$this->time;   
  
}
  
function stat($object_id = 0) {
  
  $this->object_id = $object_id = intval($object_id);
  
  //chmod($this->pointer_file_path, 0777);
  
  if ($object_id<=0)   {
    $this->err = 1;
    $this->v_errm[] = "Invalid OBJECT_ID for STAT";    
  }
  
  if (!$this->err) {
    $stat_file = $this->get_stat_file();
  }
  
  if (!$this->err) {
    
    //write views to file
    if (!file_exists($stat_file)) {
      $row = "{$object_id}=1\n";
      $row .= "m{$this->min}=1\n";
      file_put_contents($stat_file, $row);
      chmod($stat_file, 0777);
    }
    else {
      /*
      $h = fopen($stat_file, "a");
      fwrite($h, "\n".$object_id);
      fclose($h);
      */
      
      $t = file_get_contents($stat_file);
      
      // update views
      $this->matched = 0;
      $t = preg_replace_callback("#(\b{$object_id}=)([0-9]+)(\b)#si", array($this, "replace_views"), $t);
      if (!$this->matched) {
        $t .= "{$object_id}=1\n";
      }
      
      //update minutes 
      $this->matched = 0;
      $t = preg_replace_callback("#(\bm{$this->min}=)([0-9]+)(\b)#si", array($this, "replace_minutes"), $t);
      if (!$this->matched) {
        $t .= "m{$this->min}=1\n";
      }
            
      file_put_contents($stat_file, $t);
    }
    
    //
  }
  
  
  if ($this->err) {
    echo $this->err_box();
  }
}

function replace_views($m) {
  $this->matched = 1;
  $j = $m[2]+1;
  $ret = $m[1].$j.$m[3];
  return $ret;
}

function replace_minutes($m) {
  $this->matched = 1;
  $j = $m[2]+1;
  $ret = $m[1].$j.$m[3];
  return $ret;
}

/**
 * Получение имени и полного пути к текщуему файлу со статой
 *
 * @return unknown
 */
function get_stat_file() {
  
  if (!file_exists($this->pointer_file_path)) {
    file_put_contents($this->pointer_file_path, $this->n_stat_file);
    chmod($this->pointer_file_path, 0777);
    if (!file_exists($this->pointer_file_path)) {
      $this->err = 1;
      $this->errm[] = "can't create pointer file <b>{$this->pointer_file_path}</b>";
    }
    $stat_file = $this->n_stat_file;
  }
  else {
    $stat_file = trim(file_get_contents($this->pointer_file_path));
    if ($stat_file == '') {
      $this->err = 1;
      $this->errm[] = "pointer file <b>{$this->pointer_file_path}</b> is EMPTY";
    }
  }
  
  // check work dir
  if (!$this->err) {
    $wd = preg_quote($this->work_dir);
    if (!preg_match("#^{$wd}#si", $stat_file)) {
      $this->err = 1;
      $this->errm[] = "stat_file \"{$stat_file}\" from <b>{$this->pointer_file_path}</b> is not matched to dir: <b>{$this->work_dir}</b>";
    }
  }
  
  return $stat_file;
}

function err_box() {
  if (count($this->errm)<=0) {
    $this->errm[] = "No err messages.";
  }
  $t = implode("<br>", $this->errm);
  $ret = "<div style='background-color:red;padding: 10px 20px;color:white;margin-bottom:20px'>{$t}</div>";
  return $ret;
}

/**
 * Функция для крона для смены текущего файла и считывания статы в базу
 *
 * 
 */
function update_db() {
  global $sv, $std, $db;
  
  $this->c_stat_file = $this->get_stat_file();
  
  if ($this->c_stat_file==$this->n_stat_file) {
    $this->err = 1;
    $this->errm[] = "c_stat_file == n_stat_file";
  }
  
  if (!$this->err) {
    
    // 1. point new pointer to n_stat_file in pointer_file    
    $this->log("Pointing to new file: {$this->n_stat_file}");
    if (!file_put_contents($this->pointer_file_path, $this->n_stat_file)) {
      $this->err = 1;
      $this->errm[] = "Can't point new file, pointer file {$this->pointer_file_path} not writed.";
    }
    
    if (!$this->err) {
      if (!file_exists($this->c_stat_file)) {
        $this->err = 1;
        $this->errm[] = "current stat file {$this->c_stat_file} - not exists, exit";
      }
    }
    if (!$this->err) {
      // 2. read data & parse date from c_stat_file
      $r = $this->read_and_parse_stats($this->c_stat_file);
      
      // 3. remove old file
      $this->log("Removing old file: {$this->c_stat_file}");
      unlink($this->c_stat_file);
    }
  }
  
  if($this->err) {
    $this->log("Errors: \n".implode("\n", $this->errm));
  }
  
}

function read_and_parse_stats($fn) {
  global $sv, $std, $db; 
  
  $this->log("Reading and parsing stats from: {$fn}");
  
  $this->start_timer();
  if (!file_exists($fn)) {
    $this->err = 1;
    $this->errm[] = "stats file for parsing {$fn} - not exists";
    return false;
  }
  
  $rows = file($fn);
  $mins = array(); $sum = 0; $size = 0;
  $qs = array();
  $lim = 0;  $i = 0;
  foreach($rows as $row) { $i++;
    if(preg_match("#\b([0-9]+)=([0-9]+)\b#si", $row, $m)) {
       $q =  "UPDATE {$this->table}   SET `{$this->field_views}`=`{$this->field_views}`+'{$m[2]}' WHERE {$this->field_id}='{$m[1]}'";
       $db->q($q, __FILE__, __LINE__);
    }
    elseif(preg_match("#\bm([0-9]+)=([0-9]+)\b#si", $row, $m)) {
      $mins[$m[1]] = $m[2];
      $sum += $m[2];
      $size++;
    }
    else {
      $this->log("Row: \"{$row}\" - not matched");
    }    
    
    if ($lim>0 && $i>$lim) break;
  }

  unset($rows);
  $this->log("Average: ".round($sum/$size, 2)." hits per minute.");
  $this->log("\n--finish-- ".$this->stop_timer()." queries: ".$db->query_count);
}

function start_timer() {
  $this->timer = microtime();
}

function stop_timer() {
     
  if (!isset($this->timer) || $this->time <= 0) {
    return -1;
  }
  
	$t1  = $this->timer;
	$t2  = microtime();
	list($m1,$s1)  = explode(" ",$t1);
	list($m2,$s2)  = explode(" ",$t2);
	$time  = ($s2-$s1)+($m2-$m1);
	$time  = round($time,3);
	$ret   = $time." sec";
    
  return $ret;
}

function log($t) {
  
  echo $t."\n";
}

//eocs  
}


?>