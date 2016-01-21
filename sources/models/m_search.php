<?php

/*
поисковый запрос
*/
class m_search extends class_model {
  var $tables = array(
    'searches' => "
      `id` bigint(20) NOT NULL auto_increment,
      `hash` varchar(255) NOT NULL default '',      
      `query` varchar(255) not null default '',
      `words` text default NULL,
      `models` text default NULL,
      `time` int(11) not null default '0',
       
      
      `user` int(11) not null default '0',
      `ip` varchar(255) default NULL,
      `agent` varchar(255) default NULL,
      `other` text null,
            
      `calls` int(11) not null default '0',
      `size` int(11) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`hash`)
    "
  );
  
/**
 * Список всех моделей из директории
 *
 * @var array(model=>model,...)
 */
var $models_ar = array();

/**
 * Количество секунд в течении которых посик берется из кэша
 * + и сохраняются "РЕЗУЛЬТАТЫ"
 *
 * @var unknown_type
 */
var $cache_time = 3600;

var $min_len = 3;

/**
 * Массис используемых на данном сайте моделей для поиска с названием
 * задавать луче в модуле search
 *
 * @var unknown_type
 */
var $use_models = array(
  'page' => 'Статичные страницы сайты'
);


var $title = 'Поиск';
var $config_vars = array( 
  'search_cache_time' => array('title' => 'Количество секунд в течении которых поиcк берется из кэша и сохраняются результаты запросов', 'type' => 'int', 'value' => 10800, 'len' => 20), // 3 часа
  'search_expire_time' => array('title' => 'Время в секундах в течении которого история поисков сохраняется на сервере', 'type' => 'int', 'value' => 2678400, 'len' => 20),   // 31 день
);
  

function __construct() {
  global $sv;  
  
  $this->t = $sv->t['searches'];
  
  $this->cache_time = (isset($sv->cfg['search_cache_time'])) ? intval($sv->cfg['search_cache_time']) : $this->cache_time;

  // сканируем список моделей
  $files = $sv->file_list(MODELS_DIR);
  foreach($files as $fn) {
    if (preg_match("#^m_([a-z0-9\_]+)\.#si", $fn, $m)) {
      $ar[$m[1]] = $m[1];
    }
  }
  $this->models_ar = $ar;
  
  
  $this->init_field(array(
  'name' => 'created_at',
  'title' => 'Дата',
  'type' => 'datetime',
  'show_in' => array('default'),  
  'write_in' => array()
  )); 

   
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Время (unix)',
  'type' => 'int',
  'input' => 'time',
  'show_in' => array('remove'),  
  'write_in' => array('edit')
  )); 
  
  
    
  $this->init_field(array(
  'name' => 'hash',
  'title' => 'Хэш',
  'type' => 'varchar',
  'not_null' => 1,  
  'show_in' => array('remove'),  
  'write_in' => array('create', 'edit')
  ));      
  
  $this->init_field(array(
  'name' => 'query',
  'title' => 'Запрос (очищенный)',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'words',
  'title' => 'Слова (сериализованный массив)',
  'type' => 'text',  
  'len' => '70',
  'show_in' => array(),
  'write_in' => array('edit')
  ));    
      
  $this->init_field(array(
  'name' => 'models',
  'title' => 'Модели для поиска',
  'type' => 'text',  
  'len' => '70',
  'input' => 'multiselect',
  'belongs_to' => array('list' => $this->models_ar),
  'show_in' => array('default'),
  'write_in' => array('create', 'edit'),
  'selector' => 0
  ));   

      
  $this->init_field(array(
  'name' => 'user',
  'title' => 'Пользователь',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login', 'null' => 1),
  'show_in' => array(),
  'write_in' => array('edit'),
  'selector' => 1
  ));   
     
  $this->init_field(array(
  'name' => 'userview',
  'title' => 'Пользователь',
  'virtual' => 'user',  
  'show_in' => array('default'),
  ));   
    
      
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default'),
  'write_in' => array('edit')
  )); 
    
  $this->init_field(array(
  'name' => 'agent',
  'title' => 'User-agent',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array(),
  'write_in' => array('edit')
  ));          
  
  $this->init_field(array(
  'name' => 'other',
  'title' => 'Данные других пользователей',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array(),
  'write_in' => array('edit')
  ));   
    
  $this->init_field(array(
  'name' => 'calls',
  'title' => 'Вызовы',
  'description' => 'Если равен 0, то при повторном зпросе поиск будет осуществлен заново.', 
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));     
  
    
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Результаты',
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));   
    
}

function parse($d) {
  
  $d['url_results'] = "/search/results/?id={$d['id']}";
  $d['min_len'] = $this->min_len;
  return $d;
}
/**
 * input: _get[q], _get[where]
 *
 */
function parse_input_query() {
  global $sv, $std;

  $n['query'] = (isset($sv->_get['q'])) ? trim($std->text->cut($sv->_get['q'], 'allow', 'mstrip')) :  '';
  $n['models'] = (isset($sv->_get['where'])) ? trim($std->text->cut($sv->_get['where'], 'allow', 'mstrip')) :  '';
    
  $query = $this->escape_query($n['query']);
  $models = $this->escape_models($n['models']);
  $words = $this->parse_words($query);
  $hash = md5($models.$query);
  $ret = array(
    'query' => $query,
    'words' => $words,
    'hash'  => $hash,
    'models' => $models
  );
  
  return $ret;
}

/**
 * вырезаем все лишнее из запроса
 *
 * @param unknown_type $t
 * @return unknown
 */
function escape_query($t) {

  // заменяем нможественные пробелы на одиночные
  $t = preg_replace("#[\s]+#si", " ", $t);  
  
  // оставляем только разрешенные символы
  $t = preg_replace("#[^а-яА-ЯЁёa-z0-9\.\,\?\:\;\!\$\(\)\[\]\_\*\/\-\+\=\&\#\№\@\%\"\'\ ]#si", "", $t);
  // удаляем возможные пробелы по краям
  $t = trim($t);
  
  return $t;
}

/**
 * разбирает строку моделей и выкидывает несуществующие
 *
 * @param string $t
 * @return string
 */
function escape_models($t) {
  $t = preg_replace("#[^a-z0-9\_\,]#si", "", $t);
  $ar = explode(",", $t);
  $ret_ar = array();
  
  $keys = array_keys($this->use_models);
  foreach($ar as $m) {
    if (in_array($m, $keys)) {
      $ret_ar[] = $m;
    }
  }
  
  if(count($ret_ar)<=0) {
    foreach($keys as $k) {
      $ret_ar[] = $k;
    }
  }
  $ret = implode(",", $ret_ar);
  return $ret;
}

/**
 * выделяем слова из запроса
 * @param string $t - escaped query
 * @return array - words
 */
function parse_words($t) {

  $words = array();
  $tsrc_find = array();
  $tsrc_replace = array();
  
  // обрабатываем фразы кавычках
  if (preg_match_all("#\"([^\"]*)\"#si", $t, $m)) {
    foreach($m[0] as $i => $src) {
      //для сохранения порядка пока заменяем временными слитными фразами
      $tsrc = str_replace(" ", '', $src);
      $t = str_replace($src, $tsrc, $t);      
      $tsrc_find[] = $tsrc; 
      $tsrc_replace[] = $m[1][$i];
    }
  }
  
  $ar = explode(" ", $t) ;
  
  foreach ($ar as $w) {
    $w = str_replace($tsrc_find, $tsrc_replace, $w);
    $w = trim($w);
    if ($w!='') {
      $words[] = $w;
    }    
  }

  /*
  $s = count($words);
  //deprecated - if 1 слово и короткое <3 around spaces
  if ($s == 1 && strlen($words[0])<2) {    
    $words[0] = " {$words[0]} ";   
  }
  */
  
  return $words;
}

// validations
function v_models($v) {
  
  $v = (!is_array($v)) ? array($v) : $v;
  $ret = implode(",", $v);
  return $ret;
}

function last_v($p) {
  global $sv;
  
  $p['ip'] = $sv->ip;
  $p['agent'] = $sv->user_agent;
  $p['user'] = $sv->user['session']['account_id'];
  
  if ($this->code=='create') {
    $p['time'] = $sv->post_time;
    
  }
}

// public controllers

function c_public_default() {
  global $sv;
  
  $ret['models'] = $this->use_models;
  
  return $ret;
}

/**
 * обработка запроса и сбор результатов
 *
 * @return null  - в любом случае перенаправление на результаты или ошибка
 */
function c_public_query() {
  global $sv, $db;

  $n = $this->parse_input_query();
  
  // checks
  if($n['query']=='') {
    $sv->view->show_err_page('Строка для поиска пуста.');
  }
  $long = 0;
  foreach($n['words'] as $w) {
    if (strlen($w)>=$this->min_len) {
      $long = 1;
    }
  }
  if (!$long) {
    $sv->view->show_err_page("Слова для поиска в поисковом запросе слишком коротки, минимум для любого из слов: {$this->min_len} симв.");
  }
  
  $hash = $n['hash'];
  
  // проверяем возможность выдачи кэшированных результатов
  $exp_time = $sv->post_time - $this->cache_time;
  $d = $this->get_item_wh("`hash`='".$db->esc($hash)."' AND `time`>'{$exp_time}'", 1, 1);
  
  // если из кэша то перенаправляем на результаты и завершаем скрипт
  if ($d && $d['calls']>0) {
    $this->update_row(array('calls'=>$d['calls']+1), $d['id']);
    header("Location: {$d['url_results']}");
    exit();
  }
  
  $sv->load_model('searchresult');
  // создаем запись (при некоторых обст она может уже существовать)
  if (!$d) {
    $p = array(
      'query' => $n['query'],
      'words' => serialize($n['words']),
      'hash'  => $n['hash'],
      'models' => $n['models'],
      'time'  => $sv->post_time,
      'user' => $sv->user['session']['account_id'],
      'ip' => $sv->ip,
      'agent' => $sv->user_agent, 
      'calls' => 0,
      'size' => 0
    );
    
    $this->insert_row($p);
    $insert_id = $this->last_insert_id;    
    $d = $this->get_item($insert_id, 1);
  }
  
  
  $size = $sv->m['searchresult']->process_search($d);
  
  // если без ошибок обнвляем вызовы и количество результатов
  if (!$sv->m['searchresult']->err('process_search')) {
    // обновляем количество результатов и вызовы
    $this->update_row(array('calls'=>1, 'size' => $size), $d['id']);
    header("Location: {$d['url_results']}");
    exit();
  }
  else {
    $sv->view->show_err_page('badrequest');
  }
  
}

function c_public_results() {
  global $sv, $std;
  
  $this->remove_expired_searches();
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $search = $this->get_item($id, 1);
  if (!$search) {
    $sv->view->show_err_page("notfound");
  }
  
  $sv->load_model('searchresult');
  $sv->m['searchresult']->c_search = $search;
  $ret = $sv->m['searchresult']->call_controller('c_public_list');

  $ret['query'] = $std->text->cut($search['query'], 'replace', 'replace');
  return $ret;
}

// STD 
function remove_expired_searches() {
  global $sv;
  
  // удаляем устаревшие поиски
  $exp = $sv->post_time - $sv->cfg['search_expire_time'];
  $this->remove_rows_wh("`time`<'{$exp}'");
  
  // удаляем ненужные уже результаты
  $exp_cache = $sv->post_time - $this->cache_time;
  $sv->load_model('searchresult');
  $sv->m['searchresult']->remove_rows_wh("`time`<'{$exp_cache}'"); 
  
}
//eoc
}

?>