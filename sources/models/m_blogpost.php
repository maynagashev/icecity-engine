<?php

/*
 запись в блоге одиночный блог
 редактируется из админки
*/

class m_blogpost extends class_model {
  
  var $tables = array(
    'blogposts' => "
      `id` bigint( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `slug` varchar(255) not null default '',
      `title` varchar( 255 ) NULL ,
      `text` longtext NULL ,
      `tags` varchar(255) null,
      `date` datetime NULL ,
      `status_id` tinyint(1) not null default '0',
      `account_id` int(11) not null default '0',
      
      `views` int(11) not null default '0',
      `replycount` int(11) not null default '0',
      
      `last_reply_time` int(11) not null default '0',
      `last_reply_name` varchar(255) null,
      
      `ip` VARCHAR( 255 ) NOT NULL ,
            
      `created_at` DATETIME NOT NULL ,
      `created_by` INT( 11 ) NOT NULL ,
      `updated_at` DATETIME NOT NULL ,
      `updated_by` INT( 11 ) NOT NULL ,
      
      KEY (`account_id`),
      KEY (`date`),
      KEY (`last_reply_time`),
      KEY (`views`)

    "
  );

   
  var $per_page = 20;
  
  var $status_ar = array(
    0 => 'Черновик',
    1 => 'Опубликован',
    2 => 'Скрыт'  
  );
  
  var $blog_url = "";
  
  var $account_id = 0;   // inits in blog->init_bloguser
  
  /**
   * очищенный текущий чпу
   *
   * @var varchar
   */
  var $c_slug = '';
  
  
  var $load_attaches = 1;
  var $attaches_page = 'blogpost';
  var $attaches_top_margin = 100;
  var $attaches_resizes = "140x115xf,400x266xw";

  var $load_markitup = 1;
  var $markitup_use_tinymce = 1;
  var $markitup_use_emoticons = 0;
  var $markitup_width = '100%';
  var $markitup_selector = 'textarea'; 
  var $markitup_type = 'html';

  var $ext_ar = array('png', 'jpg', 'gif');  

  
  
function __construct() {
  global $sv, $std;  
  
  $this->t = $sv->t['blogposts'];
  
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
      
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'ЧПУ сообщения',
  'type' => 'varchar',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit'), 
  
  ));  
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',
  'len' => '80',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  

        
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit')
  ));  
  
  
  /*
  $this->init_field(array(
  'name' => 'account_id',
  'title' => 'Автор',
  'type' => 'int',
  'len' => '10',
  'input' => 'select',
  'show_in' => array('remove'),
  'write_in' => array('edit'), 
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login')
  ));  
    */  
  

  $this->init_field(array(
  'name' => 'status_id',
  'title' => "Статус",
  'type' => 'int',
  'input' => 'select',
  'default' => 0,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null' => 1)
  ));
  


   
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));  
  


   
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотры',
  'type' => 'int',  
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit')
  ));    

  $this->init_field(array(
  'name' => 'replycount',
  'title' => 'Ответы',
  'type' => 'int',  
  'show_in' => array( 'remove', 'default'),
  'write_in' => array('edit')
  ));      
   
}

function parse($d) {
  global $sv, $std;
  
  $d['url'] = "/blog/{$d['slug']}/";
    
  $sv->load_model('tag');
  $d['f_tags'] = $sv->m['tag']->format_str($d['tags'], "/blog/?tag=", "");
  
  $d['f_text'] = $d['text'];
  
  
  $cut_text = $this->get_cut_text($d['f_text']);  
  if ($cut_text!==false) {
    $d['need_cut'] = 1;
    $d['p_text'] = $cut_text;
  }
  else {
    $d['need_cut'] = 0;
    $d['p_text'] = $d['f_text'];    
  }
  
  $d['f_text'] = preg_replace("#\[cut\]\s?#si", "", $d['f_text']);
  $d['f_date'] = $std->time->format($d['date'], 0.5, 1);
  
  return $d;
}

function rss_parse($d) {
  global $sv;
  
  $site_url = preg_replace("#/$#si", '', $sv->vars['site_url']);
  $url = $site_url.$d['url'];
  
  $ret = array(
    'title'       => $d['title'],
    'link'        => $url,
    'description' => $d['f_text'],
    'copyright'   => $sv->cfg['short_title'],
    'pubdate'	    => $d['date'],  // datetime format
    'guid'        => $d['id']
  );  
  
  return $ret;
}
/**
 * Выборка для рсс ленты
 *
 * @param unknown_type $limit
 * @return unknown
 */
function rss_list_all($limit = 20) {
  global $sv;
  
  $ret = $this->item_list("`status_id`='1'", "`date` DESC", $limit, 1);
  
  return $ret;
}

// VALIDATIONS
function last_v($p) {
  global $sv, $std, $db;
  
  // time
  if ($this->code=='create') {
    $p['date'] = $sv->post_time;
  }
  
   // slug
  $id = ($this->code=='edit') ? $this->current_record : 0; 
  $p['slug'] = (isset($p['slug'])) ? $p['slug'] : '';
  if (!$std->text->is_valid_slug($p['slug'], $this->t, 'slug', $id)) {
    $p['slug'] = $std->text->gen_slug($p['title'], $this->t, 'slug');
  }

  $p['ip'] = $sv->ip;
  
  return $p;
}

function v_account_id($id) {
  $id = intval($id);
  if ($id<=0) {
    $this->v_err = 1;
    $this->v_errm[] = "Неверный идентификатор автора: {$id}.";
  }
  return $id;
}



//custom validations (user)
function cv_title($t) {
  global $sv, $std, $db;
 
  $t = strip_tags($t);
  $t = trim($t);
  
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан заголовок публикации.";
  }
  
  if (!$this->v_err) {
    $et = addslashes($t);
    $uid = intval($sv->m['blog']->bloguser['id']);
    $d = $this->get_item_wh("`title`='{$et}' AND uid='{$uid}' AND `id`<>'{$this->d['id']}'", 1);
  
    if ($d) {
      $this->v_err = 1;
      $this->v_errm[] = "Публикация с названием <a href='{$d['url']}' target=_blank>{$t}</a> 
      уже существует в вашем блоге, придумайте другое название.";
    }
  }
  
  return $t;
}

function cv_text($t) {
  global $sv, $std, $db;
 
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Не указан текст публикации.";
  }
  
  return $t;
}

function cv_tags($val) {
  global $sv; 
  
  $sv->load_model('tag');  
  $val = $sv->m['tag']->parse_str($val); 
  
  if ($val=='') {    
    $this->v_errm[] = "Не указаны теги.";
  }
  return $val;
}

function cv_status_id($val) {
  
  $val = intval($val);
  $keys = array_keys($this->status_ar);
  if (!in_array($val, $keys)) {
    $this->v_err = 1;
    $this->v_errm[] = "Неверный идентификатор статуса.";
  }
  
  return $val;
}

function cv_date($val) {
  
  $ret = $this->validate_field('date', $val);

  return $ret;
}


// CONTROLLERS 

/**
 * Вызов корневого контроллера инициализация блога из modules/blog.php
 *
 */
function init_blog_module() {
  global $sv, $std;
  
  $act = 'default';
  $slug = '';
  
  if (isset($sv->view->route_matches[1]) && $sv->view->route_matches[1]) {
    $slug = $std->text->clean_slug($sv->view->route_matches[1]);
    if ($slug!='') {      
      $act = 'details';
      $this->c_slug = $slug;
    }
    else {
      $sv->view->show_err_page('notfound');
    }
  }
  
  $ret = $this->scaffold('public_'.$act);
  
  
  return $ret;
}

/**
 * Детальный просмотр сообщения
 *
 * @return unknown
 */
function c_public_details() {
  global $sv, $db;
   
  $slug = $db->esc($this->c_slug);
  $d = $this->get_item_wh("`slug`='{$slug}'", 1);

  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  else {
    switch ($d['status_id']) {    
      case 1:  
        // ok public
      break;
      case 2: case 0: default:
        // restricted, admin only
        if ($sv->user['session']['group_id']<>3) {
          $sv->view->show_err_page('forbidden');
        }
    }
  }
  
  $this->update_row(array('views' => $d['views']+1), 0);
  
  $sv->vars['p_title'] = $d['title'];
  $ret['d'] = $d;
  
  $sv->load_model('comment');  
  $sv->m['comment']->comments_mode = 'guest';
  $ret['comments'] = $sv->m['comment']->init_system($this->name, $d['id']);
  return $ret;
}

/**
 * Главный дефолтный список сообщений
 *
 * @return unknown
 */
function c_public_default($limit = 0) {
  global $sv;
  
  
  $wh = array();
  
  
  
  $where = implode(" AND ", $wh);
  
  $ret = $this->item_list_pls($where, "`date` DESC", 10, 1, $sv->_get['page'], "/blog/?page=");

  return $ret;
}

/**
 * dblock copy of c_public_default
 *
 * @param unknown_type $limit
 * @return unknown
 */
function dblock_blogposts($limit) {
  global $sv, $smarty;
  
  $tr = array();
  
  $ar = $this->c_public_default($limit);
  foreach($ar['list'] as $d) {
    $smarty->assign('d', $d); 
    $tr[] = $smarty->fetch("blog/post_preview.tpl");
  }   
 
  $ret = implode("\n\n", $tr);
    
  return $ret;
}

// PRE/POST ACTIONS
function comments_update($code, $comment) {
  global $sv;
  
  //t($code, 1);
  //t($comment);
  
  $x = 0;
  // получаем текущие значения
  $d = $this->get_item($comment['object']);
  
  if ($code=='moderation') {
    switch($comment['action']) {
      case 'approve':
        $x = 1;
      break;
      case 'deprove': case 'remove':
        $x = -1;
      break;
    }    
  }
  elseif($code=='approve') {
    $x = 1;
  }
  elseif($code=='deprove') {
    $x = -1;
  }
  elseif ($code=='public_create') {
    if ($comment['approved']) {
      $x = 1;
    }
  }
  elseif ($code=='remove') {
    if ($comment['approved']) {
      $x = -1;
    }
  } 
  elseif ($code=='default')  {
    // тут обычно групповое удаление
    if ($comment['approved']) {
      $x = -1;
    }
  }
  
  if ($x!=0) {
    $this->update_row(array('replycount' => $d['replycount']+$x), $d['id']);
  }
  
}

//stuff


function get_cut_text($t) {
  global $sv, $std, $db;
  
  $ret = false;  
  $max = 10000;
  
  if (preg_match("#^(.*)\[cut\]#si", $t, $m)) {
    return $m[1];
  }
  
  if (strlen($t)>$max) {
    return substr($t, 0, $max)."...";
  }
  
  return $ret;
}

function get_next_slug($uid) {
  global $sv, $db;
  
  $uid = intval($uid);
   
  $db->q("SELECT max(`slug`) as max FROM {$this->t} WHERE `uid`='{$uid}'", __FILE__, __LINE__);  
  $d = $db->f();
  $max = intval($d['max']);
  $ret = $max + 1;
  
  return $ret;
}


/**
 * Item list with pagelist
 *
 * @param unknown_type $wh
 * @param unknown_type $ord
 * @param unknown_type $lim
 * @param unknown_type $page
 * @param unknown_type $parse
 * @param unknown_type $url
 * @param unknown_type $addon
 * @return unknown
 */
function post_list_pl($wh = "", $ord = "", $lim = 10, $page = 1, $parse=0, $url="", $addon="") {
  global $db, $std, $sv;
  
  $sv->load_model('blog');
  
  $where = ($wh=='') ? "" : "WHERE {$wh}";
  $orderby = ($ord=='') ? "" : "ORDER BY {$ord}";
  
  $lim = intval($lim);
  $page = intval(abs($page));
  $url = ($url=='') ? u($sv->act, $sv->code, $sv->id) : $url;
  
  $limit = ($lim>0) ? " LIMIT 0, {$lim}" : "";
  
  $db->q("SELECT 0 FROM {$this->t} p {$where} ", __FILE__, __LINE__);
  $ret['pl'] = $pl = $std->pl($db->nr(), $lim, $page, $url, $addon);
  
  $ar = array();
  $db->q("
    SELECT p.*, b.username, b.photo, b.title as blog_title FROM {$this->t} p 
    LEFT JOIN {$sv->t['blogs']} b ON (b.id=p.uid)
    {$where} {$orderby} 
    {$pl['ql']}", __FILE__, __LINE__);
  $this->last_rows_count = $db->nr();  
  while($d = $db->f()) {
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
      $d = $sv->m['blog']->parse_photo($d);
    }
    $ar[] = $d;
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  
  return $ret;
}



//deprecated
function posts_by_tag($tag, $page="blog", $url="") {
  global $sv, $std, $db;
  
  $tag = $std->text->cut($tag, 'cut', 'mstrip');
  
  $sv->load_model('tag');
  $p = (isset($sv->_get['p'])) ? intval($sv->_get['p']) : 1;
  $ret = $sv->m['tag']->item_list_pl("`page`='".addslashes($page)."' AND `tag`='".addslashes($tag)."'", "`object` DESC", 10, $p, 1, "/blog/?tag={$tag}&p=");
  
  t($ret);
  
  return $ret;
}

function filter_bbcode($t) {  
  $bb = new bbcode($t);
  return $bb->get_html();  
}

//eoc
}

?>