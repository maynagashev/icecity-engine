<?php

class class_sv {

/**
 * Дефолтный список таблиц (наиболее полный)
 * Текущий список таблиц прописывается в mysql_cfg.php -> $db_init_vars['tables']
 *
 * @var unknown_type
 */
var $tn = array(
  'accounts',   // !учетные записи
  'antispam',   // сессии капчей
  'attaches',   // !стандартные аттачи для струтуры сайта и новостей
  'book',       // записи в гостевой
  'bcats',      // категории доски объявлений
  'bposts',     // объявления
  'breports',   // жалобы в объявлениях
  'bsubscribes', 
  'cache',      // !кэш
  'channels',   // какналы тюба
  'config',     // !настройки
  'forums',     // форумы
  'ftopics',    // темы на форуме
  'fposts',     // сообщения на форуме
  'events',     // события, мероприятия в афише
  'halls',      // кино-концертные площадки для мероприятий  
  'infoblocks', // !инофоблоки - сниппеты
  'logs',       // !логи авторизаций
  'news',       // !новостная лента
  'orders',     // заказы в магазине
  'pages',      // !структура сайта
  'page_parts', // !контент структуры сайта
  'polls',      // опросы
  'press',      // пресс-релизы и СМИ
  'products',   // товары в магазине
  'restore',    // !сессии восстановления пароля
  'sessions',   // !сессии пользователей
  'sms',        // смски со шлюза
  'sites',      // каталог сайтов
  'subscribers',// подписчики
  'uploads',    // @deprecated
  'urls',       // !адреса страниц сайта
  'wmresults',  // логи wm платежей
  'voters',     // проголосовавшие в голосовании,
  'video'       // видеоролики
    );
    
    
var $t = array();
    
// search bots    
var $is_bot = false;
var $bot_name = "undefined";

// enviroment
var $exec_time;
var $ip;
var $user_agent;
var $refer;
var $post_time;
var $date_time;
var $year;
var $pwd;
var $request_uri;
var $host;
var $request_url;

// action
var $act;
var $code;
var $id;

// modes
var $debug = 0;
var $use_rewrite = 0;  
var $is_admin = 0;
var $use_query_string = 0;

// submit
var $_c = array();
var $_r = array();
var $_post;
var $_get;
var $_files = array();

// vars
var $user = false;
var $cfg = array();
var $u = "/";
var $vars = array(
  'site_url' => "/",
  'site_title' => "SV->vars['site_title']",
  'p_title' => "",
  'styles' => array(),
  'js' => array()
);

var $parsed = array(
'wait_div' => "<div class='wait' style='display:none;color: gray;'><img src='/i/wait16.gif' width='16' height='16' 
                border='0' style='vertical-align:-3px;'> загрузка...</div>",
'admin_sidebar' => ''
);


/**
 * Различные меню
 * 1) admin_menu - главное админское меню
 */
var $menu = array(
      'admin_main' => array(
          'pages' => array('title' => 'Структура сайта', 'act' => 'pages'),
          'editors' =>  array('title' => 'Редакторы', 'act' => 'bposts'),
          'users' =>  array('title' => 'Пользователи', 'act' => 'admin'),
          'options' => array('title' => 'Настройки', 'act' => 'config'),
          //'afisha' => array('title' => 'Афиша', 'act' => 'halls'),
          //'blogs' => array('title' => "Блоги", 'act' => 'blogs'),
          //'victorina' => array('title' => "Викторина", 'act' => 'tquestions'),    
          //'shop'    => array('title' => 'Магазин', 'act' => 'products'),
          //'forum'   => array('title' => 'Форум', 'act' => 'forums')
      )
  
    );
  
/**
 * подразделы главного меню в админке, указанные модули должны быть прописаны modules_list
 * 
 */
var $admin_menu = array(
      'pages'   => array( 'pages',  'urls', 'advstreams', 'advblocks', 'advshow' ),
      'editors' => array(  'bposts', 'bcats', 'breports', 'bsubscribes', 'attaches' ),
      'users'   => array( 'admin', 'accounts',  'restores'),
      'options' => array( 'config', 'cache', 'antispam' ),
      'afisha'  => array( 'halls', 'events' ),
      'blogs'   => array( 'blogs', 'posts', 'history', 'restores'),
      'victorina' => array( 'tquestions', 'tcats' ),      
      'shop'    => array( 'products', 'orders'),      
      'forum'   => array( 'forums', 'ftopics', 'fposts'),
    );

var $active_module; // object link    

/**
 * Конструкторы
 *
 * @return class_sv
 */
function class_sv() {
  $this->__construct();
}

function __construct() {  
    global $db, $trace_ips;
    
    
    $this->pwd = getcwd();       
		$this->post_time = time();		
		$this->date_time = date("Y-m-d H:i:s", $this->post_time);		
    $this->year = date("Y", $this->post_time);
   
		$this->_post = $_POST;
    $this->_get = $_GET;
    $this->_c = $_COOKIE;
    $this->_r = $_REQUEST;
    $this->_files = $_FILES;
    
    $this->exec_time    = (defined('EXEC_TIME')) ? EXEC_TIME : microtime();    
    $this->ip           = getenv('REMOTE_ADDR');		
    $this->ip           = (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match("#^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)#si", $_SERVER["HTTP_X_FORWARDED_FOR"], $m)) ? $m[1] : $this->ip;
    
    $this->user_agent   = getenv("HTTP_USER_AGENT");
    $this->host         = getenv("HTTP_HOST");
    $this->request_uri  = getenv("REQUEST_URI");
    $this->request_url  = "http://".$this->host.$this->request_uri;
    $this->debug = (is_array($trace_ips) && in_array($this->ip, $trace_ips)) ? 1 : $this->debug;
    
    /*
   print_r($trace_ips); 
   print_r($this->ip);
   print_r($this->debug);
   */
    
    $this->refer        = $this->parse_ref();   
    $this->is_bot       = $this->is_bot();
    
    if (isset($db->vars['prefix'])) {
      $this->db_prefix = $db->vars['prefix'];
    }
    
    // заменяем дефолтный список таблиц на указанный в mysql_cfg.php
    if (isset($db->vars['tables']) && is_array($db->vars['tables'])) {
      $this->tn = $db->vars['tables'];
    }
    
    // иницируем таблицы по списку
    foreach($this->tn as $t) {
      $this->t[$t] = $this->db_prefix.$t;
    }
        
   
    if (defined("SITE_URL")) {
      $this->vars['site_url'] = SITE_URL;
      $url = parse_url($this->vars['site_url']);      
      $this->vars['site_domain'] = $url['host'];
      
    }
    if (defined("SITE_TITLE")) {
      $this->vars['site_title'] = SITE_TITLE;
    }
    if (!isset($this->_get['page'])) {
      $this->_get['page'] = 1;
    }
    if (defined("USE_QUERY_STRING") && USE_QUERY_STRING==1) {
      $this->use_query_string = 1;
    }    
   
    $this->parse_input();      
}

/**
 * Инициализация встроенных библиотек
 *
 * @param unknown_type $name
 * @return unknown
 */
function init_class($name) {
  global $sv, $db;
    
  $name = preg_replace("#[^a-z0-9\-\_]#msi", "", $name);
  $fn = LIB_DIR."class_{$name}.php";
  if (!file_exists($fn)) {
    die("Cant find {$fn}");
  }
  if (!isset($sv->$name) || !is_object($sv->$name)) {
    include($fn);
    eval ("\$sv->\$name = new class_{$name};");   
    $sv->c[$name]  = &$sv->$name;
    return 1;
  }
  else {
    //  die("Can't load lib: {$name}");
    return 2;
  }
}

/**
 * Загрузка выбранной модели
 *
 * @param unknown_type $name
 */
function load_model($name) {
  global $sv, $db;  
  
  $name = preg_replace("#[^a-z0-9\_\-]#msi", "", $name);
  
  if (!isset($sv->m[$name]) || !is_object($sv->m[$name])) {
    $fn = MODELS_DIR."m_{$name}.php";
    if (!file_exists($fn)) {
      die("{$fn} not exists");
    }   
    include($fn);
  
    eval ("\$sv->m[\$name] = new m_{$name};");  
    $sv->m[$name]->name = $name;
  }
  else {
   // die("Cant load model: {$name}");
  }
}

/**
 * Создание таблиц по списку из параметров моделей
 * Загружаются все модели по очереди и если в описании найдена нужная таблица 
 * выполняется запрос if not exist create
 *
 */
function install_tables() {
  global $db, $sv;
  
  $create_ar = array();  
  $keys = array_keys($this->t);
  
  $sv->init_class('model');
  
  // загружаем по очереди все модели
  $files = $this->file_list(MODELS_DIR);
  sort($files);
  $i = 0;
  foreach ($files as $fn) { 
    if (preg_match("#^m_([a-z][a-z0-9\_]*)\.php$#si", $fn, $m)) {
      $model = $m[1];
      $i++;
      echo "{$i}) Модель <b>{$model}</b><br>";
      $sv->load_model($model);
      $tables = $sv->m[$model]->tables;      
      
      $c_keys = array_keys($tables);
      foreach($c_keys as $tn) {
        echo "<b>{$tn}</b>";
        // если таблица найдена в текущем списке добавляем в очередь
        if (in_array($tn, $keys)) {
          $create_ar[$tn] = $tables[$tn];          
          echo "<pre>"; print_r($tables[$tn]); echo "</pre>";
        }
        else {
          echo " - не требуется";
        }
      }
      echo "<hr>";
    }
  }
  
  $create_keys = array_keys($create_ar);
  $no_def = array_diff($keys, $create_keys);
  if (count($no_def)>0) {
    echo "<p>Требуемые таблицы без описаний: <b>".implode(", ", $no_def)."</b></p>";
  }
  
  
  foreach($create_ar as $tn => $sql) {
    $db->create_table($sv->t[$tn], $sql);
  }
  
  if (1==1 || isset($sv->_get['admin'])) {
    $sv->load_model('account');
    $ar = $sv->m['account']->item_list("", "", 5, 0);

    if ($ar['count']<=0) {
      echo "Учетных записей не найдено, создаем админа.<br>";
      $p = array(
      'login' => 'admin',
      'password' => $sv->m['account']->password_hash('admin'),
      'active' => 1,
      'group_id' => 3
      );
      $sv->m['account']->insert_row($p);
    }
    else {
      $users = array();
      foreach($ar['list'] as $u) {
        $users[] = $u['login'];
      }
      
      echo "<b>Таблица с пользователями непуста (".implode(", ", $users)."), пропускаем создание админа.<br></b>";    
      
    }
  }
  
  exit("--конец установки--");
}

/**
 * Парсинг рефера
 *
 * @return unknown
 */
function parse_ref()   {  
  $ref = getenv("HTTP_REFERER");  
  $ref = addslashes(strip_tags($ref));  
  return $ref;
}

/**
 * Встроенный парсинг урл
 *
 */
function parse_input($update_vars = 1) { 
  
  $uri = getenv("REQUEST_URI");
  $ar = parse_url($uri);
  $str = (!isset($ar['query']) || $this->use_query_string) ? getenv('QUERY_STRING') :  $ar['query'];
    
  preg_match("|^([A-Za-z0-9\_]*)|msi", $str, $m);
  $fstr = $m[1];
  $ar = explode("_", $fstr);
  
  $act = @$ar[0];
  $code = @$ar[1];
  $id = (isset($this->_get[@$ar[0]."_".@$ar[1]])) ? $this->_get[@$ar[0]."_".@$ar[1]] : 0;
  $id = ($id==0 && isset($this->_get[@$ar[0]]) ) ? $this->_get[@$ar[0]] : $id;
  $id = intval($id);
  
  $code = ($code=='') ? "default" : $code;
  
  if ($update_vars) {
    $this->act = $act;
    $this->code = $code;
    $this->id = $id;
  }
  
  return array('act' => $act, 'code' => $code, 'id' => $id);
}

/**
 * Определение ботов по агенту
 *
 * @return unknown
 */
function is_bot() {
    
  $agent = $this->user_agent;  
  
  $bots = array(
  "GoogleBot" => "Googlebot",
  "Yandex.Bot" => "Yandex",
  "MSN Bot" => "msn",
  "Yahoo Bot" => "Yahoo",
  "Rambler Crawler" => "Rambler",
  "CLX Bot" => "CLX.ru Bot",
  "CLX PostBot" => "CLX.ru PostBot",
  "CLX" => "CLX.ru",
  "Nor24 Bot" => "norilsk24.ru search engine",
  "Mail.ru Bot" => "Mail.Ru"
  );
  
  $ret = false;
  
  foreach($bots as $name => $p) {
    $p = preg_quote($p);
    if (preg_match("#{$p}#msi", $agent)) {
      $ret = true; 
      $this->bot_name = $name;
      break;
    }
  }
  
  return $ret;  
}

/**
 * Список файлов в текущей директории
 *
 * @param unknown_type $path
 * @param unknown_type $dirs
 * @param unknown_type $files
 * @return unknown
 */
function file_list($path, $dirs = 1, $files = 1) {
  
  $ar = array();
  
  $dh = opendir($path);
  while ($fn = readdir($dh)) {  	
  	if (($fn!=".") && ($fn!="..")) {  	  
  	  $c_path = $path."/".$fn;     
  		if (is_dir($c_path) && $dirs) {
  		  $ar[] = $fn;
  	  }
  		elseif ($files)	{
  			$ar[] = $fn;
  		}
  	}
  }
  
  closedir($dh);  
   
  return $ar;
}


/**
 * inits admin main menu
 * after load modules
 */
function init_admin_menu() {

  $cur_menu = "";
  foreach($this->admin_menu as $k=>$ar) {
    if (in_array($this->act, $ar)) {
      $cur_menu = $k;
      break;
    }  
  }
  
  $this->parsed['main_menu_id'] = $cur_menu;
  
  $ar = array();
  if (isset($this->admin_menu[$cur_menu])) {
    foreach ($this->admin_menu[$cur_menu] as $k) {
      $ar[$k] = $this->modules->list[$k];  
    }
  }
  $this->parsed['admin_menu'] = $ar;
  
  return true;  
}



 
}

?>