<?php
/**
 * Класс для определения текущего модуля на основе group_id (default: 0)
 *
 * Важные натсроечные параметры должны быть установлены до вызова get_current:
 *  1. default_ar = массив с данными о том какой группе соотвествует какой модуль по умолчанию array(0 => 'auth', 1 => 'main', 2 => 'moders', 3 => 'admin )
 *  2. default = если не задан или не подходит default_ar используется данный параметр независимо от групп
 * 
 */
class class_modules {
  
/**
 * Запрошенный модуль
 *
 * @var unknown_type
 */
var $request = '';

/**
 * Текущий отфильтрованный модуль
 *
 * @var unknown_type
 */
var $current = '';

/**
 * Объект текущего модуля
 *
 * @var unknown_type
 */
var $current_object = false;


var $default = 'main';
var $default_ar = array();

var $list = array();    // <-- modules_list | список модулей с навзаниями и доп параметрами если есть
var $keys = array();    // <-- array_keys(modules_list)
var $access = array();  // <-- modules_access
  
var $err = 0;
var $file_loaded = 0;

var $public_classes = array();

/**
 * Выбор текущего модуля на основе группы
 *
 * @param unknown_type $null_group
 * @return act
 * 
 * notfound = модуль не найден в списке
 * forbidden = нет доступа к модулю для этой группы
 */
function get_current($null_group = 0, $check_init = 1) {
  global $sv;   
  
  if ($check_init && !$this->file_loaded) {
    die("can't run <b>module->get_current</b>, modiles_list.php not loaded, use <b>modules->load(file)</b> first.");    
  }
  
  
  $ret = "";
  $gid = (isset($sv->user['session']['group_id']) && !$null_group) ? intval($sv->user['session']['group_id']) : 0;
  $request = $sv->act;

  if ($request=='') {
    //set default val
    if (isset($this->default_ar) && is_array($this->default_ar) && isset($this->default_ar[$gid])) {
      $request = $this->default_ar[$gid];
    }
    elseif($this->default!==false) {
      $request = $this->default;
    }    
  }   
  
  $this->request = $request;
  $this->current = $request;
    
  // search current act in access list 
  foreach($this->access[$gid] as $id) {        
    // if found - just return 
    if ($request===$id) {      
      return $id;
    }
  }
  
  
// if not found:  
  $this->err = 1;
  
  // 1. search from all list if found = noaccess
  if (in_array($request, $this->keys)) {
     $ret = "forbidden";
  }
  else {
     $ret = "notfound";
  }
  
  $this->current = $ret;
  
  return $ret;
}

/**
 * Загрузка списка модулей из файла
 *
 * @param unknown_type $file
 */
function load($file='') {
  
  if (trim($file)=='') die("modules list file not specified");  
  if (!file_exists($file)) die("modules list file not exists");
  
  require($file);
  
  $this->default = (isset($modules_default)) ? $modules_default : false;  
  $this->default_ar = (isset($modules_default_ar)) ? $modules_default_ar : false; 
   
  $this->list = (is_array($modules_list)) ? $modules_list : array();  
  $this->keys = array_keys($this->list);
  $this->access = (is_array($modules_access)) ?  $modules_access : array();
  
  $this->public_classes = array();
  foreach($this->list as $k => $d) {
    
    $d['id'] = $k;
    $d['public'] = (isset($d['public']) && $d['public']==1) ? 1 : 0;
    $d['use'] = (isset($d['use'])) ? $d['use'] : '';
    $d['model'] = (isset($d['model'])) ? $d['model'] : '';
    $this->list[$k] = $d;
    
    if ($d['public']) {
      $this->public_classes[$k] = $d['title'];
    }
  }
  
  $this->file_loaded = 1;
}


function can_access($act) {
  global $sv;
 
  $gid = (isset($sv->user['session']['group_id'])) ? intval($sv->user['session']['group_id']) : 0;

  if (!isset($this->access[$gid])) return false;
  
  foreach($this->access[$gid] as $id) {    
    if ($act===$id) {
      return true;
    }
  }
 
  return false;
}

function auto_run() {
  global $sv;
  
  if (!isset($this->list[$this->current])) {
    $sv->view->show_err_page("Act <b>{$this->current}</b> not found in modules list.");
  }
  
  $m = $this->list[$this->current];

  
  // спец обработка USE
  $run = ($m['use']!='') ? $m['use'] : $m['id'];
  
  $path = MODULES_DIR."{$run}.php"; 
  
  if (!file_exists($path)) {
    $sv->view->show_err_page("Module file ".basename($path)." - not found.");
  }
  
  require($path);
  
  $this->current_object = new $run;
  $this->current_object->name = $run;
  $this->current_object->vars = $m;
  
  $ret = $this->current_object->auto_run();
  
  return $ret;
}
  

//end of class
}


?>