<?php

/**
 * Класс управления публичным представлением,
 * как правило не испольузется только впубличной части
 *
 */
class class_view {
  
  var $use_urls = 1;
  
  var $use_routes_before = 0;
  var $use_routes_after = 0;
    
  var $default_module = "page";  
  
  /**
   * урл из urls + слеш в конца ИЛИ если страница не задана полное соответсвие шабону из routes (возможны любые символы указанные в шаблоне)
   *
   * @var string
   */
  var $safe_url = "./";  // относительный урл текущей страницы включая sv->code и слеш в конце
  var $full_url = "";    // абсолютный урл текущей страницы
  var $root_url = "";    // относительный урл текщей страницы $sv->code

  var $url = "";  // значение из $_GET['url'] 
  var $uri = "";  // значение из REQUEST_URI
    
  var $d = false;   // current record from URLS
  var $page = false;  // current record from PAGES
  var $content = array();
  var $layout_id = "";
  
  var $classname = "";
  
  // @deprecated with func url_vars_for_page, parse_url
  var $url_vars = array();
  var $url_data = array();
  
  var $act = "";
  var $code = "";
  
  
  var $c_module = false;

  var $return_url = ""; //  @deprecated 
  var $return_url_success = "./";
  var $return_url_err = "./?auth";
  
 
  var $routes_fn = "sources/routes.php";    
  
  /**
   * Прмиенять шаблону из routes к url (результат в htaccess) или uri (исходный запрос)?
   *
   * @var string
   */
  var $routes_src = 'uri'; // url | uri 
  
  /**
   * массив $routes  из файла   
   * 
   * @var array
   */
  var $routes_list = array();
  
  
  /**
   * Раньше было название модуля, сейчас айди страницы
   *
   * @var int
   */
  var $route_act = '';
    
  /**
   * Совпадения подшаблонов в найденном route
   * 
   * @var array
   */
  var $route_matches = array();   
  var $purl = array(); // @deprecated
    
  var $url_parsed = 0;

  
  /**
   * временный массив динамических блоков по названию функции
   *
   * @var unknown_type
   */
  var $dblocks;
  
  /**
   * временный массив инфоблоков по названию блока
   *
   * @var unknown_type
   */  
  var $infoblocks;
  
  /**
   * включить подсчет статы по разделам?
   *
   * @var boolean
   */
  var $daystats_on = 0;
  
  var $display_order = array('infoblocks', 'dblocks');
  
  
function __construct() {
  $this->return_url = $this->return_url_success; // @$return_url - deprecated
}
// INITS 
  
/**
 * Главная функция для вывода публичного предствления
 *
 * @return unknown
 */
function init_page($show_err_page = 1) {
  global $sv, $db;
  
  $err = false;
  $errm = "";  
  $page_id = 0;
  $page = false;
  $act = false;
  
  $sv->load_model('page');  
  $this->init_url();

  if ($this->use_routes_before) {
    // проверяем урл по шаблонам, если находим то заканчиваем проверку
     $page_id = $this->routes2act();          
     $page = $sv->m['page']->get_item($page_id, 1);
  }  
  
  if ($this->use_urls && !$page) {   
    $db->q("SELECT * FROM {$sv->t['urls']} WHERE url='".addslashes($this->url)."'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $this->d = $d = $db->f();
      $page_id = $d['page'];
      $page = $sv->m['page']->get_item($page_id, 1);
    }   
  }
  
  if ($this->use_routes_after && !$page) {
     // если включена проверка по шаблонам после проверки по базе урлов, то ищем по шаблонам
     $page_id = $this->routes2act();      
     $page = $sv->m['page']->get_item($page_id, 1);
  }
  
  if ($page) {    
    $act = $page['classname'];
    
    // сбор статы по разделам
    if ($this->daystats_on) {
      $sv->load_model('daystat');
      $r = $sv->m['daystat']->update_stats("views_page", $this->id);
    }

    // set system vars
    
    // если получен урл из страницы он является основным иначе берем из шаблона
    $this->safe_url = (isset($d['url'])) ? (($d['url']=='/') ? $d['url'] : $d['url']."/") : $this->route_matches[0];
    $this->full_url = $sv->vars['site_url'].preg_replace("#^/+#si", "", $this->safe_url);
        
    // пытаемся выделить sv->code из адреса, он должен идти после slug текущей страницы
    $pat = ($page['slug']=='/') ? "^/([a-z\_\-0-9]+)(/.*)?$" : "/".preg_quote($page['slug'])."/([a-z\_\-0-9]+)(/.*)?$";
    $sv->code = (preg_match("#{$pat}#si", $this->safe_url, $m)) ? $m[1] : $sv->code;
    
    // выделяем урл страницы без кода
    if ($page['slug']=='/') { 
      // спец обработка корневой страницы
      $this->root_url = "/";
    }
    else {
      $this->root_url = (preg_match("#(^.*/".preg_quote($page['slug']).")(/.*)?$#si", $this->safe_url, $m)) ? $m[1]."/" : $this->safe_url;
    }
    
    $this->page = $page;
    if ($page['status_id']==0) {
      $err = 1;
      $errm = "draft";
    }
    elseif ($page['status_id']==2) {
      $err = 1;
      $errm = "hidden";
    }      
  }
  // иначе (если не найдена страница) и был идентификатор, то страница перемещена (удалена)
  elseif ($page_id) {
    $err = 1;
    $errm = 'removed';
  }
        
 
  // если не инициирован модуль до сих пор (не найден по шаблону и по базе), то страница не найдена
  if (!$act && !$page) {
    $err = 1;
    $errm = "notfound";
    $this->url = "";        
  }  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  if ($err && $show_err_page) {  
      $this->show_err_page($errm);
  }
  return $ret;
}

/**
 * Альтернативная функция инициализации модуля только через routes
 * без испольхования таблицы pages
 *
 * @param unknown_type $routes_fn
 * @return unknown
 */
function init_routes_page($routes_fn = "") {
  global $sv;
  
  $act = $this->routes2act($routes_fn);

  if (is_null($act)) {
    $act = "notfound";
  }
  elseif (!$sv->modules->can_access($act)) {
    $act = "forbidden";
  }

  return $act; 
}

/**
 * Инициализация и парсинг 
 * $this->uri & $this->url
 * из $sv->_get['url']
 *
 */
function init_url() {
  global $sv;  
  
  $url = "/".((isset($sv->_get['url']))? $sv->_get['url'] : "");
  $this->uri = getenv("REQUEST_URI");
  $this->url = ($url!='/') ? preg_replace("#[/]+$#msi", "", $url) : $url;

  $this->uri2get($this->uri);  
  $this->url_parsed = 1;
}



// ACTIONS
/**
 * Запуск выбранного модуля
 *
 * @param unknown_type $act
 * @return unknown
 */
function load_content($act='', $only_public = 1) {
  global $sv, $std, $db;
  
  if ($act=='') {
    $module = $this->get_module_name($only_public); 
  }
  else {
    $module = $act;
  }
  $sv->act =  $module;
  
  // проверяем доступ
  if ($sv->act!='page' && !$sv->modules->can_access($sv->act)) {
    $this->show_err_page("forbidden");
  }
  
  $file = MODULES_DIR.$module.".php";  
  if (!file_exists($file)) die("Module file not exists {$file}");    
  require($file);
  
  
  $runme = new $module;   
  $this->content  = $runme->auto_run();
 
  return $this->content;
}

function load_layout($layout_id='') {
  global $sv;
  
  if ($layout_id) {
    $this->page['layout_id'] = $layout_id;
    $this->layout_id = $layout_id;
  }
  elseif ($this->page && is_object($sv->m['page'])) {
    $this->page['layout_id'] = $sv->m['page']->get_layout_id($this->page['id'], $this->page);
    $this->layout_id = $this->page['layout_id'];
  }
  
  
}
  
/**
 * Фцнкция компиляции шаблонов
 *
 * @param unknown_type $use_smarty
 */
function display($use_smarty = 1, $print = 1) {
  global $smarty, $sv, $std;
  
  if ($use_smarty) {
    $smarty->assign('ar', $this->content);    
    $smarty->assign('sv', $sv);    
    $smarty->assign('page', $this->page);
  
    $html = $smarty->fetch("body.tpl");   
  }
  else {
    //  используем встроенный php шаблонизатор
    $sv->init_class('tpl');
    $html = $sv->tpl->fetch("body.php");
  }
   
  // вызов стандартных пост-обработчиков страницы (infoblocks, dblocks, advstreams)
  $html = $this->render($html);
  
  if ($sv->debug)   {
    $d = $std->debug_info();
    $html .= "<div style='position:absolute; top: 10px; right: 10px; font-size: 10px; font-family: Verdana;'>{$d}</div>";
  }  
  
  if ($print)   {
    print($html);
  }
  else {
    return $html;
  }
}

/**
 * вызов стандартных пост-обработчиков страницы (infoblocks, dblocks, advstreams)
 *
 * @param unknown_type $html
 * @return unknown
 */
function render($html) {  
  
  foreach($this->display_order as $replace_func) {
    $method = "replace_{$replace_func}";
    if (method_exists($this, $method)) {
      $html = $this->$method($html);
    }
    else {
      t("view->display replace_method not exists: <b>{$method}</b>", 1);
    }
  }
  return $html;
}


function fetch($use_smarty = 1, $replace_infoblocks = 1, $replace_dblocks = 1) {
  return $this->display($use_smarty, $replace_infoblocks, $replace_dblocks, 0);
}

/**
 * Получение имени модуля для запуска
 *
 * @return unknown
 */
function get_module_name($only_public = 1) {
  global $sv;
  
  // если используются урлы то проверяем в зписи страницы
  if ($this->use_urls && $this->page && $this->page['classname']) {
    $act = $this->page['classname'];    
  }
  // иначе если используются routes то берем текущий route_act
  elseif(($this->use_routes_before || $this->use_routes_after) && $this->route_act) {
    $act = $this->route_act;
  }
  // иначе модуль  не найден
  else {
    $this->show_err_page('notfound');
  }
  
  // модуль должен быть объявлен в публичных модулях
  if ($only_public) {
    $ar = array_keys($sv->modules->public_classes);  
    if (in_array($act, $ar)) {
      $ret = $act;
    }
    else {
      // exit
      $this->show_err_page("Not public module: {$act}");
    }
  }
  
  if (isset($sv->modules->list[$ret])) {
    $this->c_module = $sv->modules->list[$ret];
  }
  
  
  return $ret;
}

// ПРЕОБРАЗОВАНИЯ

/**
 * Получение названия модуля на основе routes из текущего url/uri
 *
 * @param unknown_type $routes_fn
 * @return unknown
 */
function routes2act($routes_fn="") {
  global $sv, $db;
    
  if (!$this->url_parsed) {
    $this->init_url();
  }
  $url = ($this->routes_src=='uri') ? $this->uri : $this->url;  
  
  $routes = $this->load_routes_list($routes_fn);

  foreach($routes as $pat => $act) {    
    if (preg_match("#^{$pat}$#si", $url, $m)) {
      $this->purl = $m; // deprecated use route_matches
      $this->code = (isset($m[1])) ? $m[1] : "default";      
      $sv->code = $this->code;      
      
      $this->act = $act;      
      $this->route_matches = $m;
      $this->route_act = $act;
      
      return $act;
    }
  }
  
  return null;  
}

/**
 * Инициализация GET переменных из URI запроса
 *
 * @param unknown_type $url
 */
function uri2get($url) {
  global $sv;  
  $ar = @parse_url($url);
  if (!$ar) return false;
  if (isset($ar['query'])) {
    parse_str($ar['query'], $vars);
    foreach($vars as $k=>$v) {
      if (!isset($sv->_get[$k])) {
        $sv->_get[$k] = $v;
      }
    }    
  }  
}


  

/**
 * Загрузка routes_list из файла
 *
 * @param unknown_type $fn
 * @return unknown
 */
function load_routes_list($fn) {
  
  $fn = ($fn=='') ? $this->routes_fn : $fn;
  if (!file_exists($fn)) {
    $this->show_err_page("Cant load <b>routes_list</b>, file not exists <b>{$fn}</b>");    
  }
  include($fn);
  
  if (isset($routes) && is_array($routes)) {
    $this->routes_list = $routes;
    return $routes;
  }
  else {
    $this->show_err_page("Not set <b>\$routes</b> array in <b>{$fn}</b>.");
  }
}


/**
 * id init_page return err = true
 * this help to msg 
 *
 * @param unknown_type $errm
 */
function show_err_page($errm) {
  global $sv;
      
  $sv->vars['errm'] = $errm;
  
  // если модуль для отображения ошибок есть то через него
  if (file_exists(MODULES_DIR."errpage.php")) {     
    
    $this->load_content('errpage');
    $this->load_layout('errpage');
    $sv->view->display();  
    
  }
  else {
    $this->raw_err_page($errm);
  }
  
  exit();
}

function raw_err_page($errm) {
  
     switch ($errm) {
      case 'draft':
        header("HTTP/1.0 307");
        echo "<strong>Запрашиваемая страница временно не доступна, 
        возможно она находится в стадии редактирования.</strong><br><br>
        <a href='{$this->return_url_success}'>Перейти на главную.</a>";
     
      break;
      case 'hidden':
        header("HTTP/1.0 303");
        echo "<strong>Запрашиваемая страница отключена.</strong><br><br>
        <a href='{$this->return_url_success}'>Перейти на главную.</a>";
      break;
      case 'removed':
        header("HTTP/1.0 303");
        echo "<strong>Запрашиваемая страница не найдена, возможно она была перемещена.</strong><br><br>
        <a href='{$this->return_url_success}'>Перейти на главную.</a>";      
        //header("Location: /",TRUE,303);
      break;
      case 'forbidden':
        header("HTTP/1.0 403 Forbidden");
        echo "<strong>Ошибка 403 - доступ запрещен.</strong>
        <br><br><a href='{$this->return_url_success}'>Вернуться на главную.</a>";
      break;      
      case 'notfound':
        header("HTTP/1.0 404 Not Found");
        echo "<strong>Ошибка 404 - Запрашиваемая страница не найдена.</strong>
        <br><br><a href='{$this->return_url_success}'>Вернуться на главную.</a>";
      break;
      default:
        header("HTTP/1.0 302");
        echo "<strong>{$errm}</strong><br><br>
        <a href='{$this->return_url_success}'>Перейти на главную.</a>";
    }
    echo "<hr noshade size='1'>
    <div align=right style='padding: 10px;'><small>Сделано в <a href='http://icecity.ru'>Icecity</a>.</i></small></div>";
    die();
    
}

/**
 * Поиск, генерация и замена динамических блоков в тексте
 */
function replace_dblocks($text) {
  
  if (preg_match_all("|#dblock\(([^\(\)]+)\)#|siU", $text, $keys, PREG_SET_ORDER) 
      && is_array($keys) 
      && count($keys)>0) {
  
    
    foreach ($keys as $k) {
      $str = $k[1];
      
      $ch = explode(",", $str);
      $i = 0;  
      $vars = array();
      foreach($ch as $k => $v){ $i++;
        if ($i>1) {
          $vars[] = trim($v);
        }
      }      
      
      $func = $ch[0];
      
      // проверям не вызывлся ли ранее данный блок с ткими же параметрами, если да то берем из кэша
      if (isset($this->dblocks[$func]) && $this->dblocks[$func]['vars'] === $vars) {
        $res = $this->dblocks[$func]['res'];
      }
      else { 
        $res = $this->dblock_from_file($func, $vars);     
        $this->dblocks[$func] = array('res' => $res, 'vars' => $vars);        
      }
      $text = preg_replace("|#dblock\({$str}\)#|siU", $res, $text);         
    }
    
    unset($this->dblocks);
        
  } 
  
  return $text;
}

/**
 * Поиск, чтение и замена инфоблоков (сниппетов) в тексте
 */
function replace_infoblocks($text) {
  global $sv;
  
  if (preg_match_all("|#infoblock\(([^\(\)]+)\)#|siU", $text, $keys, PREG_SET_ORDER) 
      && is_array($keys) 
      && count($keys)>0) {
    
    $sv->load_model('infoblock');
    
    foreach ($keys as $k) {
      $str = $k[1];
      
      $ch = explode(",", $str);
      $i = 0;  
      $vars = array();
      foreach($ch as $k => $v){ $i++;
        if ($i>1) {
          $vars[] = trim($v);
        }
      }      
      
      $func = $ch[0];
      
      // проверям не вызывался ли ранее данный инфоблок с такими же параметрами, если да то берем из кэша
      if (isset($this->infoblocks[$func]) && $this->infoblocks[$func]['vars'] === $vars) {
        $res = $this->infoblocks[$func]['res'];
      }
      else { 
        $res = $sv->m['infoblock']->read_content($func, $vars);     
        $this->infoblocks[$func] = array('res' => $res, 'vars' => $vars);        
      }
      $text = preg_replace("|#infoblock\({$str}\)#|siU", $res, $text);         
    }
    
    unset($this->infodblocks);
        
  } 
  
  return $text;
}

/**
 * Поиск и замена реклмных потоков, например [stream=1]
 *
 * @param unknown_type $text
 */
function replace_advstreams($text) {
  global $sv, $std, $db;
  
  $sv->load_model('advstream');
  $text = $sv->m['advstream']->replace($text);
      
  return $text;
}

/**
 * Загрузка кода динамического блока из файла
 *
 * @param unknown_type $act
 * @param unknown_type $vars
 * @return unknown
 */
function dblock_from_file($act, $vars = array()) {
  global $sv, $std, $db;
  
  $act = str_replace("#[^a-z0-9\_\-]#si", "", $act);  
  $path = DBLOCKS_DIR.$act.".php";
  $bn = basename($path);
  
  if (!file_exists($path)) {
    return "File not found: <b>dblocks/{$bn}</b>.";
  }
  
  $dblock_act = $act;
  $dblock_vars = $vars;
  
  include($path);
    
  $dblock_content = (!isset($dblock_content)) 
    ? "<b>\$dblock_content</b> not defined in <b>{$bn}</b>" : $dblock_content;
  
  return $dblock_content;
}



// DEPRECATED
/**
 * @deprecated 
 *
 * @return unknown
 */
function parse_url() {
  global $sv;
  
  // разбираем по частям
  $ar = explode("/", $url);  
  $i=0;
  foreach ($ar as $t) { $i++;       
    $this->url_data[] = $t;
  }
  
  return $r;
}

    
/**
 * @deprecated ?
 *
 * @param unknown_type $module
 * @return unknown
 */
function url_vars_for_page($module) {
  global $sv;
  
  $slug = preg_quote($this->page['slug']);
  
  // удалем неиспользуемую часть строки.
  $url =(preg_match("#{$slug}/(.*)#msi", $this->url, $m)) ? $m[1] : "";
  
  // разбираем по частям
  $ar = explode("/", $url);  
  $r = array(); $start = false; $i=0;
  foreach ($ar as $t) { $i++;
    $r[] = $t;   
    if ($i>1) {
      $this->url_data[] = $t;
    }  
  }
  
  $this->url_vars = $r;
  
  
  $sv->code = (isset($r[0])) ? $r[0] : "default";
  $sv->id = (isset($r[1])) ? $r[1] : 0;
  $sv->vars['page'] =  (isset($r[2])) ? $r[2] : 0;
  return $r;
}

function cut_get_query() {
  
  $uri = getenv('REQUEST_URI'); 
  $url = preg_replace("#^([^\?]+).*$#msi", "\\1", $uri); 
  if ($url!=$uri) { 
    header("Location: {$url}", TRUE, 301); 
    exit(); 
  }    
  
}
//eoc
}

?>