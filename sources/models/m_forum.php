<?php

/*
Модель отдельного форума

alter table forums add 
`cat_id` int(11) not null default '0' after `parent_id`

*/


class m_forum extends class_model {

  var $tables = array(
    'forums' => "    
      `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
      
      `title` varchar(255) not null default '',  
      `description` text null,  
      `slug` varchar(255) not null default '',      
      `status_id` tinyint(1) not null default '1',
      `parent_id` int(11) not null default '0',
      `cat_id` int(11) not null default '0',
      
      `posts`  int(11) not null default '0',
      `topics`  int(11) not null default '0',
      `last_post_id` int(11) not null default '0',
      `last_post_time` int(11) not null default '0',
      `last_poster_id` int(11) not null default '0', 	
      `last_poster_name` varchar(255) null,
      
      `last_topic_id` int(11) not null default '0',
      `last_topic_title` varchar(255) null,       
      
      `place`  int(11) not null default '0',
      `moderators`  varchar(255) not null default '',
      
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      
      PRIMARY KEY ( `id` ),
      KEY (`slug`)
    "
  );
  
  var $status_ar = array(
    0 => "Выключен", 
    1 => "Включен",    
  );
  
  var $module = "forum";
  var $forum_url = "/forum";
  var $c_forum_url = "";
  
  var $f_act = "default";
  var $f_code = "default";
  var $f_id = 0;
  
  /**
   * Информация о текущем форуме / категории
   *
   * @var unknown_type
   */
  var $c_forum = false;
  var $c_cat = false;
  
  var $moderators = array();
  var $moderators_row = "";
  
  var $f_acts = array(
    /* основные */
    'index', // индекс форума
    'forum',  // просмотр форума, список тем
    
    /* форумные */
    'topic',  // просмотр темы, список постов
    'newtopic', // новая тема, форма1
    'removetopic',  // удаление темы, подтверждение, удаление всх постов
    'answer', // новый пост, форма2 в теме
    'editpost',   // редактор поста, форма2 в теме
    'removepost',  // удаление поста, пожтверждение
    
    /* глобальные */
    'cat', // категория форумов
    'users',  // список пользователей форума
    'user', // информация о пользователе
    'userposts', // посты и темы пользователя
    'rules',  // првила форума
    'search',  // поиск по форуму,
    'blank', // список тем без ответа
    'active', // список тем по дате обновления DESC,
    'newposts' // список тем с новыми сообщениями
    
  );
  
  // права и обязанности
  var $allow_newtopic = 0;
  
  var $is_user = 0; // авторизован или нет?
  var $is_moderator = 0; // модератор в текщуем форуме?
  var $is_admin = 0;     // админ на форуме?
  var $is_banned = 0;    // пользователь забанен?
  
  
  var $nav = array();
  
  var $title = 'Форум';
  var $config_vars = array(
    'forum_rules' => array('title' => 'Правила форума', 'type' => 'text', 'value' => '', 'len' => 130),
    'forum_show_users' => array('title' => 'Показывать список пользователей?', 'type' => 'boolean', 'value' => 1),
    'forum_show_rules' => array('title' => 'Показывать правила форума?', 'type' => 'boolean', 'value' => 1),
    'news_use_ftopic' => array('title' => 'Использовать комментарии на форуме для новостей', 'type' => 'boolean', 'value' => 1),
    'forum_use_cats' => array('title' => 'Использовать категории форумов?', 'type' => 'boolean', 'value' => 1)
  );
  
  var $stats = array();
  
  /**
   * Время за какое показывать онлайн в сек
   *
   * @var unknown_type
   */
  var $online_time = 900;
  
  /**
   * Использовать категории форумов?
   * $sv->cfg['forum_use_cats']
   * @var unknown_type
   */
  var $use_cats = 1;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['forums'];  
  
  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Категория форума',
  'type' => 'int',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('table' => 'fcats', 'field' => 'id', 'return'=>'title', 'null'=>1)
  ));   
    
  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Категория форума',
  'virtual' => 'cat_id',
  'show_in' => array('default'),
  ));     
  
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Порядок',
  'type' => 'int',
  'default' => 0,  
  'len' => 6,
  'show_in' => array('default'),
  'write_in' => array('create', 'edit'),
  ));     
   
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название форума',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 0,
  'public_search' => 1
  ));    
  

  
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание форума',
  'type' => 'text',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'),
  'public_search' => 1
  ));    
   
    
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Идентификатор (англ.)',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 1
  ));    
    

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'default' => 1,
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit', 'create'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));   
    
   
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'virtual' => 'status_id'
  ));      

  $this->init_field(array(
  'name' => 'parent_id',
  'title' => 'Родительский форум',
  'type' => 'int',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('table' => 'forums', 'field' => 'id', 'return'=>'title', 'null'=>1),
  'selector' => 0
  ));   
    

  $this->init_field(array(
  'name' => 'moderators',
  'title' => 'Модераторы',
  'type' => 'varchar',
  'input' => 'multiselect',  
  'show_in' => array('default'),
  'write_in' => array('edit'),
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return'=>'login', 'null'=>0)
  ));   
      
   

 
  $this->init_field(array(
  'name' => 'topics',
  'title' => 'Тем',
  'type' => 'int',
  'default' => 0,  
  'len' => 6,
  'show_in' => array('default'),
  'write_in' => array('edit'),
  ));       
   
  $this->init_field(array(
  'name' => 'posts',
  'title' => 'Ответов',
  'type' => 'int',
  'default' => 0,  
  'len' => 6,
  'show_in' => array('default'),
  'write_in' => array('edit'),
  ));     
    
}

function parse($d) {
  global $std, $sv;
  
  $d['url'] = $this->forum_url."/".$d['slug']."/";

  // последняя тема
  if ($d['last_topic_id']) {
    $f_time = $std->time->format($d['last_post_time'], 0.5);
    $last_topic_url = "{$this->forum_url}/{$d['slug']}/?topic={$d['last_topic_id']}";
    $d['last_topic_url'] = "    
    <a href='{$last_topic_url}'>{$d['last_topic_title']}</a><br>
    {$f_time}, автор: <a href='{$this->forum_url}/?user={$d['last_poster_id']}'>{$d['last_poster_name']}</a>
    
    ";
  }
  else {
    $d['last_topic_url'] = "";
  }
  
   
  $last_visit =  ($sv->user['session']['account_id']>0) ? strtotime($sv->user['account']['last_visit']) : 0;
  $d['is_new'] = ($last_visit < $d['last_post_time']) ? 1 : 0;
  
  return $d;
}

function parse_search($d) {
  global $std;
  
  $title = $d['title'];
  $url = $this->forum_url."/".$d['slug']."/";
  $desc = $d['description'];  
    
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}

// validations
function v_cat_id($id) {
  $id = intval($id);
  if ($id<=0) {
    $this->errm("Не указана категория форума.");
  }  
  return $id;
}
function v_parent_id($id) {
  
  $id = intval($id);
  if ($id==$this->current_record) {
    $id = 0;
  }
  return $id;
}

function v_moderators($ar) {
  if (!is_array($ar)) {
    return $ar;
  }
  return implode(",", $ar);
}

// pre-post
function after_create($p, $err) {
  if (!$err) {
    $this->update_url($this->current_record);
  }
}

function after_update($d, $p, $err) {
  if (!$err) {
    $this->update_url($d['id']);
  }
}

function after_remove($d, $err) {
  global $sv, $db;
  
  if (!$err) {
    $sv->m['url']->remove_rows_wh("`module`='".$db->esc($this->module)."' AND `object`='".intval($d['id'])."'");
  }
  
}
/**
 * Инициализация текущего форума и контроллеров
 *
 */
function init_forum() {
  global $sv, $db, $std, $smarty; 
  
  $this->use_cats = $sv->cfg['forum_use_cats'];
  
  // инициализация названий контроллеров форума из текущего запроса, без назначения sv->act, sv->code
  $r = $sv->parse_input(0);
  $this->f_act = $r['act'];
  $this->f_code = $r['code'];
  $this->f_id = $r['id'];

  $this->url = $sv->view->safe_url;
  $this->root_url = $sv->view->root_url;
    
  // если находим форум с таким идентификатором то инициализируем форум
  $slug = $db->esc($sv->code);
  $d = $this->get_item_wh("`slug`='{$slug}'", 1);
  if ($d) {
    $this->c_forum = $this->parse($d);
    $this->c_forum_url = $this->forum_url."/".$d['slug']."/";
    if ($this->use_cats) {
      $sv->load_model('fcat');
      $this->c_cat = $sv->m['fcat']->get_item($d['cat_id'], 1);
    }
  }
  
  $this->f_act = (!in_array($this->f_act, $this->f_acts)) ? 'default' : $this->f_act;
  
  // если указано действие по умолчанию или незнакомое действие 
  if ($this->f_act=='default'){
    if ($this->c_forum) {
      $this->f_act = "forum";
    }
    else {
      $this->f_act = "index";
    }    
  }
  else {
    $this->url .= "?".$this->f_act;
  }
  
  $this->init_forum_access();
  $this->init_stats();
      
  $call = "c_public_{$this->f_act}";
  $ret = $this->call_controller($call);

  $smarty->assign('m', $this);
  $ret['err_box'] = $std->err_box($this->v_err, $this->v_errm);
  
  return $ret;
}

/**
 * Иницицилаизация прав посетителя для любой страницы форума
 *
 */
function init_forum_access() {
  global $sv, $db;
  
  if ($sv->user['session']['account_id']>0) {
    $this->is_user = 1;
    $this->allow_newtopic = 1;
  }
  else {
    $this->is_user = 0;
    $this->allow_newtopic = 0;
  }
  
  $this->is_admin = ($sv->user['session']['group_id']==3) ? 1 : 0;
  
  // определение статуса модератора
  $moderators = array();
  $m_ids = array();
  $m_logins = array();
  $moderators_row = "";
  if ($this->c_forum) {
    $in = $this->c_forum['moderators'];
    $in = preg_replace("#[^0-9\,]#si", "", $in);
    $in = (preg_match("#,#si", $in)) ? $in : "'{$in}'";
    $in = trim($in);
    if ($in!='') {
      $db->q("SELECT id, login FROM {$sv->t['accounts']} WHERE id IN ({$in})", __FILE__, __LINE__);
      while($d = $db->f()) {
        $moderators[$d['id']] = $d['login'];
        $m_ids[] = $d['id'];
        $m_logins[] = "{$d['login']}";
      }
    }
    $moderators_row = implode(", ", $m_logins);
    
    $this->is_moderator = ($this->is_user && in_array($sv->user['session']['account_id'], $m_ids)) ? 1 : 0;
  }  
  $this->moderators = $moderators;
  $this->moderators_row = $moderators_row;
  
  // админы по умолчанию модераторы
  $this->is_moderator = ($this->is_admin) ? 1 : $this->is_moderator;
}

/**
 * Инициализация статистических данных выводиым в футере
 * @todo кэширование некоторых парметров
 */
function init_stats() {
  global $sv, $db;
  
  $p = array('guests_count' => 0);

  // online
  $sv->load_model('session');
  $uid = intval($sv->user['session']['account_id']);
  $exp_time = $sv->post_time-$this->online_time;
  $time = "AND `time`>'{$exp_time}' AND `time`<='{$sv->post_time}'";
  $p['guests_count'] = $sv->m['session']->count_wh("`module`='{$this->module}' AND `account_id`='0' {$time}");
   $p['online_users'] = $sv->m['session']->item_list("`module`='{$this->module}' AND `account_id`<>'0' AND `account_id`<>'{$uid}'{$time}", "`time` ASC", 0, 0);
  if ($uid>0) {
    $p['online_users']['list'][] = $sv->user['session'];
    $p['online_users']['count']++;
  }
  else {
    $p['guests_count']++;
  }
  
  $p['online_count'] = $p['guests_count']+$p['online_users']['count'];
  $ar = array();
  foreach ($p['online_users']['list'] as $d) {
    $ar[] = "<a href='{$this->forum_url}/?user={$d['account_id']}'>{$d['login']}</a>";
  }
  $p['online_users']['row'] = implode(", ", $ar);
  
  // posts, users, topics
  $sv->load_model('account');
  $p['accounts_count'] = $sv->m['account']->count();
  $p['accounts_with_posts'] = $sv->m['account']->count_wh("`fposts`>'0'");
  
  $sv->load_model('ftopic');
  $p['topics_count'] = $sv->m['ftopic']->count();
  
  $sv->load_model('fpost');
  $p['posts_count'] = $sv->m['fpost']->count();
  
  $this->stats = $p;
  return $p;
}




   

//stuff
/**
 * Обновление записи URL
 *
 * @param unknown_type $forum_id
 * @param unknown_type $d
 * @return unknown
 */

function update_url($forum_id, $d = false) {
  global $sv, $db;
  
  $forum_id = intval($forum_id);
  
  if (!$d) {
    $d = $this->get_item($forum_id);
    if (!$d) {
      $this->log("Can't get forum data  by \$forum_id={$forum_id}, <b>update_url</b> canceled.");
      return false;
    }
  }
  
  $sv->load_model('url');
  
  // запись адреса форума
  $forum_url = $sv->m['url']->get_item_wh("`url`='{$this->forum_url}'");
  if (!$forum_url) {
    $this->log("Can't init \$forum_url, <b>update_url</b> canceled.");
    return false;
  }
  
  $c_url = $this->forum_url."/".$d['slug'];
  $c_page = $forum_url['page'];
  $c_title = "Форум \"".$d['title']."\"";
  
  // ищем существующую строку адреса для выбранного форума
  $url = $sv->m['url']->get_item_wh("`module`='{$this->module}' AND `object`='{$forum_id}'");
  if (!$url) {
    $sv->m['url']->insert_row(array(
      'url' => $c_url,
      'page' => $c_page,
      'title' => $c_title,
      'module' => $this->module,
      'object' => $forum_id
    ));
  }
  else {
    $sv->m['url']->update_row(array(
      'url' => $c_url,
      'page' => $c_page,
      'title' => $c_title,
    ), $url['id']);
  }
  
  //обновляем ссылки в топиках  
  $db->q("UPDATE {$sv->t['ftopics']} SET `forum_slug`='".$db->esc($d['slug'])."' WHERE `forum_id`='{$d['id']}'", __FILE__, __LINE__);
    
  return true;
}




// PUBLIC CONTROLLERS
/**
 * Главная страница форума
 *
 * @return unknown
 */
function c_public_index($cat_id = 0) {
  global $sv, $db;
  
  $wh = ($cat_id>0) ? " AND `cat_id`='".$db->esc($cat_id)."'" : '';
  $sv->load_model('fcat');
  $cats = $sv->m['fcat']->item_list("`status_id`='1'", "`place` ASC", 0, 1);
  
  $ar = array();
  $forums = $this->item_list("`status_id`='1'{$wh}", "`place` ASC", 0, 1);
  foreach ($forums['list'] as $d) {
  	$ar[$d['cat_id']][] = $d;
  }
  
  $ret = array();
  
  // добавляем все форумы без категории
  if (isset($ar[0])) {
    $ret['list'][0] = array('forums' => $ar[0]);
  }

  // форумы по категориям 
  foreach($cats['list'] as $k => $d) {
    // если есть форумы в этой категории то добавляем в список
    if (isset($ar[$d['id']])) {
      $ret['list'][$d['id']] = array('d' => $d, 'forums' => $ar[$d['id']]);
      $this->c_cat = $d;
    }   
  }
  
  return $ret;
}

/**
 * Просмотр отдельно выбранного форума
 * список тем
 * + список подфорумов
 *
 * @return unknown
 */
function c_public_forum() {
  global $sv;
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  if ($this->use_cats) {
    $this->nav[1] = array('title' => $this->c_cat['title'], 'url' => $this->c_cat['url']); 
  }
  $this->nav[2] = array('title' => $this->c_forum['title']);

  $sv->vars['p_title'] = $this->c_forum['title'];
 
  $sv->load_model('ftopic');
  
  $ret = $sv->m['ftopic']->topic_list($this->c_forum);
  $ret['d'] = $this->c_forum;

  $this->recount_forum($this->c_forum['id']);
  return $ret;
}

/**
 * список тем - без ответа
 * 
 * @return unknown
 */
function c_public_blank() {
  global $sv;
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => 'Темы без ответа');

  $sv->vars['p_title'] = "Темы без ответа";
 
  $sv->load_model('ftopic');
  
  $ret = $sv->m['ftopic']->topic_list_wh("`posts`='0'", "`last_post_time` DESC");

  return $ret;
}

/**
 * список тем - активных
 * 
 * @return unknown
 */
function c_public_active() {
  global $sv;
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => 'Активные темы');

  $sv->vars['p_title'] = "Список активных тем";
 
  $sv->load_model('ftopic');
  
  $ret = $sv->m['ftopic']->topic_list_wh("`status_id`='1'", "`last_post_time` DESC");

  return $ret;
}

/**
 * список тем - новых
 * 
 * @return unknown
 */
function c_public_newposts() {
  global $sv;
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => 'Новые сообщения в темах');

  $sv->vars['p_title'] = "Новые сообщения в темах";
 
  $sv->load_model('ftopic');
  
  $last_visit =  ($sv->user['session']['account_id']>0) ? strtotime($sv->user['account']['last_visit']) : 0;
  $ret = $sv->m['ftopic']->topic_list_wh("`status_id`='1' AND `last_post_time`>'{$last_visit}'", "`last_post_time` DESC");

  return $ret;
}


/**
 * Создание новой темы, форма
 *
 * @return unknown
 */
function c_public_newtopic() {
  global $sv, $std, $db;
  
  $sv->load_model('ftopic');
  $ret = $sv->m['ftopic']->call_controller("c_public_newtopic");
  $this->errm_push($sv->m['ftopic']->v_errm);
  
  $this->nav = array(
  0 => array('title' => "Форумы", 'url' => $this->root_url),
  1 => array('title' => $this->c_forum['title'], 'url' => $this->c_forum['url']),
  2 => array('title' => "Создание новой темы")
  );
      
    // markitup
  $ret['markitup'] = $std->markitup->js("textarea");
  
  return $ret;
}

/**
 * Просмотр темы
 *
 * @return unknown
 */
function c_public_topic() {
  global $sv, $std, $db;
  
  $sv->load_model('ftopic');
  $ret = $sv->m['ftopic']->call_controller("c_public_view");
  $this->errm_push($sv->m['ftopic']->v_errm);
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  if ($this->use_cats) {
    $this->nav[1] = array('title' => $this->c_cat['title'], 'url' => $this->c_cat['url']); 
  }
  $this->nav[2] = array('title' => $this->c_forum['title'], 'url' => $this->c_forum['url']);
  $this->nav[3] = array('title' => $ret['d']['title']);
  
  $sv->vars['location'] = "topic={$ret['d']['id']}";
  $ret['online'] = $this->online_here($sv->vars['location']);
  
  //$ret['s'] = $sv->m['ftopic']->init_submit_create(1);
  
  return $ret;
}

/**
 * Ответ в тему
 *
 * @return unknown
 */
function c_public_answer() {
  global $sv, $std, $db;
  
 
  $sv->load_model('fpost');
  $ret = $sv->m['fpost']->call_controller("c_public_create"); 
  $this->errm_push($sv->m['fpost']->v_errm);
  
  $use_quote = (isset($sv->_get['quote'])) ? 1 : 0;
  $t = ($use_quote) ? "Новое сообщение с цитатой" : "Новое сообщение";
  
 $this->nav = array(
  0 => array('title' => "Форумы", 'url' => $this->root_url),
  1 => array('title' => $this->c_forum['title'], 'url' => $this->c_forum['url']),
  2 => array('title' => $ret['topic']['title'], 'url' => $ret['topic']['url']),
  3 => array('title' => $t)
  );
    
  $q = ($use_quote) ? "&quote" : "";
  $p = ($ret['parent']) ? "&parent_id=".$ret['parent']['id'] : "";
  $ret['action_url'] = $this->c_forum_url."?answer&topic={$ret['topic']['id']}{$p}{$q}";
  
  // markitup
  $ret['markitup'] = $std->markitup->js("textarea");
  
  return $ret;
}

/**
 * Реадктирование поста в теме
 *
 * @return unknown
 */
function c_public_editpost() {
  global $sv, $std, $db;

  $sv->load_model('fpost');
  $ret = $sv->m['fpost']->call_controller("c_public_edit");
  $this->errm_push($sv->m['fpost']->v_errm);
 
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  if ($this->use_cats) {
    $this->nav[1] = array('title' => $this->c_cat['title'], 'url' => $this->c_cat['url']);  
  }
  $this->nav[2] = array('title' => $this->c_forum['title'], 'url' => $this->c_forum['url']);
  $this->nav[3] = array('title' => $ret['topic']['title'], 'url' => $ret['topic']['url']);
  $this->nav[4] = array('title' => "Редактирование сообщения");
    
  $ret['action_url'] = $this->c_forum_url."?editpost&id={$ret['d']['id']}&topic={$ret['topic']['id']}";
  
  // markitup
  $ret['markitup'] = $std->markitup->js("textarea");
  
  return $ret;
}

/**
 * Выводит список пользователей форума
 *
 * @return unknown
 */
function c_public_users() {
  global $sv;
  
  $sv->load_model('account');
  $ret = $sv->m['account']->item_list_pl("", "`fposts` DESC, `login` ASC", 50, $sv->_get['page'], 1, "{$this->url}&page=" );

  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => "Пользователи");

  $sv->vars['p_title'] = "Список пользователей";
   
  return $ret;
}

/**
 * Выводит информацию о польззователе
 *
 * @return unknown
 */
function c_public_user() {
  global $sv;
    
  $sv->load_model('account');
  $id = (isset($sv->_get['user'])) ? intval($sv->_get['user']) : 0;
  $user = $sv->m['account']->get_item($id, 1);
  if (!$user) {
    $sv->view->show_err_page('notfound');
  }
  
  $sv->load_model('fpost');
  $user['last_fpost_url'] = ($user['last_fpost']>0) ? $sv->m['fpost']->get_url($user['last_fpost']) : '';
  $user['topics_count'] = $sv->m['fpost']->count_wh("`author_id`='{$user['id']}' AND `new_topic`='1'");
  
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => "Пользователи", 'url' => $this->root_url."?users");
  $this->nav[2] = array('title' => $user['login']);
  
  $who = ($user['sex']==2) ? "такая" : 'такой';
  $sv->vars['p_title'] = "Кто {$who} {$user['login']}";
    
  $ret['d'] = $user;
  
  $fields = array(
    'surname', 'name', 'fathername',
    'birthday', 'age', 'sign', 'sex', 'city', 'country', 'work', 'workplace', 'interests', 'text'
  );
  
  $ret['table'] = $sv->m['account']->compile_public_table($fields, $user, 0);
   
  return $ret;
}

/**
 * СПисок сообщений пользователя с двумя режимами: mode=all|topics
 * изначально - список постов, либо постов с new_topic=1
 * @return unknown
 */
function c_public_userposts() {
  global $sv;
  
  // пользователь
  $sv->load_model('account');
  $id = (isset($sv->_get['userposts'])) ? intval($sv->_get['userposts']) : 0;
  $user = $sv->m['account']->get_item($id, 1);
  if (!$user) {
    $sv->view->show_err_page('notfound');
  } 
  
  // режим
  $mode = (isset($sv->_get['mode']) && $sv->_get['mode']=='topics') ? 'topics' : 'all';

  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => "Пользователи", 'url' => $this->root_url."?users");
  $this->nav[2] = array('title' => $user['login'], 'url' => $this->root_url."?user={$user['id']}"); 
  if ($mode=='all') {
    $sv->vars['p_title'] = "Все сообщения {$user['login']}";
    $this->nav[3] = array('title' => 'Сообщения пользователя'); 
  }
  else {
    $sv->vars['p_title'] = "Темы {$user['login']}";
    $this->nav[3] = array('title' => 'Темы пользователя'); 
  }
  
  // список сообщений
  $ret = $sv->m['fpost']->list_for_userposts($user, $mode);
  
  $ret['title'] = ($mode=='all')? 'Сообщения' : 'Темы';
  return $ret;
}

/**
 * Правила форума из $sv->cfg['forum_rules']
 *
 * @return unknown
 */
function c_public_rules() {
  global $sv;
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  $this->nav[1] = array('title' => 'Правила форума');
  $sv->vars['p_title'] = 'Правила форума';
  
  return true;
}

/**
 * Список форумов только выбранной категории
 *
 */
function c_public_cat() {
  global $sv;
  
  $cat_id = (isset($sv->_get['cat'])) ? intval($sv->_get['cat']) : 0;
  
  $ret = $this->c_public_index($cat_id);
 
  $this->nav[0] = array('title' => "Форумы", 'url' => $this->root_url);
  if ($this->use_cats) {
    $this->nav[1] = array('title' => $this->c_cat['title']); 
  }
  $sv->vars['p_title'] = $this->c_cat['title'];
  
  return $ret;
}

// stuff 
function forum_selector($c_val = 0, $all = 1, $allow_null = 1) {
  global $sv;
  
  $wh = ($all) ? "" : "`status_id`='1'";
  
  $ar = $this->item_list($wh, "`place` ASC, `title` ASC", 0, 0);
  $opts = array();
  if ($allow_null) {
    $opts[] = "<option value='0'>-- выберите форум --</option>";
  }
  foreach($ar['list'] as $d) {
    $s = ($d['status_id']==1) ? "" : "* ";
    $opts[] = "<option value='{$d['id']}'>{$s}{$d['title']}</option>";
  }
  
  $ret = "<select name='new[forum_id]'>".implode("\n", $opts)."</select>";
  
  return $ret;
}

/**
 * Пересчитать топики и посты в форуме
 * + последнюю теу в форуме и последний пост
 * 
 * все считаем с таблицы ftopics - в которой все счетчики должны быть уже заранее проверены и достоверны, fposts не используется
 *
 */
function recount_forum($id = 0) {
  global $sv, $std, $db;
  
  $sv->load_model('ftopic');
  
  $id = intval($id);
  
  // posts & topics
  $db->q("SELECT sum(posts) as posts, count(*) as topics FROM {$sv->t['ftopics']} WHERE forum_id='{$id}'", __FILE__, __LINE__);
  $p = $db->f();
    
  // last_topic
  $d = $sv->m['ftopic']->get_item_wh("`forum_id`='{$id}' ORDER BY last_post_time DESC", 1);
  if ($d) {
    $p['last_topic_id'] = $d['id'];
    $p['last_topic_title'] = $d['title'];
    $p['last_post_id'] = $d['last_post_id'];
    $p['last_post_time'] = $d['last_post_time'];
    $p['last_poster_id'] = $d['last_poster_id'];
    $p['last_poster_name'] = $d['last_poster_name'];
  }
  else {
    $p['last_topic_id'] =0;
    $p['last_topic_title'] = '';
    $p['last_post_id'] = 0;
    $p['last_post_time'] = 0;
    $p['last_poster_id'] = 0;
    $p['last_poster_name'] = '';    
  }
   
  // обновляем счетчики у форума        
  $this->update_row($p, $id);
  
}

/**
 * Список тех кто находится сейчас в теме.
 *
 * @param unknown_type $location
 * @return unknown
 */
function online_here($location = '') {
  global $sv, $db;
   
  $location = ($location=='') ? $sv->vars['location'] : $location;
  
  $exp_time = $sv->post_time-$this->online_time;
  $time = "AND `time`>'{$exp_time}' AND `time`<='{$sv->post_time}'";
  
  $ar = $sv->m['session']->item_list("`module`='{$this->module}' AND `location`='".$db->esc($location)."'{$time}", "`time` DESC", 0, 1);
  $users = array();
  $guests_count = 0;
  
  if ($sv->user['session']['account_id']>0) {    
    $users[] = $sv->user['session'];
  }
  
  foreach ($ar['list'] as $d) {
    if ($d['account_id']>0 && $sv->user['session']['account_id']<>$d['account_id']) {
      $users[] = $d;
    }
    else {
      $guests_count++;
    }
  }
  $users_count = count($users);
  $count = $guests_count + $users_count;
  
  $ar = array();
  foreach($users as $d) {
    $ar[] = "<a href='{$this->forum_url}/?user={$d['account_id']}'>".$d['login']."</a>";
  }
  $users_row = implode(", ", $ar);

  return array('users' => $users, 'guests_count' => $guests_count, 'users_count' => $users_count, 'count' => $count, 'users_row' => $users_row);
}
//eoc
}
?>