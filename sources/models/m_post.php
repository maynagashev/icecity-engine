<?php

/*
 запись в блоге
*/

class m_post extends class_model {
  
  var $tables = array(
    'posts' => "
      `id` bigint( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `uid` int(11) not null default '0',
      `user_slug` varchar(255) null,
      `slug` bigint(20) not null default '0',
      `title` VARCHAR( 255 ) NULL ,
      `text` longtext NULL ,
      `tags` varchar(255) null,
      `date` datetime NULL ,
      `status_id` tinyint(1) not null default '0',
      
      `views` int(11) not null default '0',
      `replycount` int(11) not null default '0',
      `ftopic` int(11) not null default '0',
      
      `last_reply_time` int(11) not null default '0',
      `last_reply_name` varchar(255) null,
      
      `ip` VARCHAR( 255 ) NOT NULL ,
      `agent` VARCHAR( 255 ) NOT NULL ,
      `refer` VARCHAR( 255 ) NOT NULL ,
            
      `created_at` DATETIME NOT NULL ,
      `created_by` INT( 11 ) NOT NULL ,
      `updated_at` DATETIME NOT NULL ,
      `updated_by` INT( 11 ) NOT NULL ,
      KEY (`uid`),
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
  
  var $uid = 0;   // inits in blog->init_bloguser
function __construct() {
  global $sv, $std;  
  
  $sv->load_model('blog');
  $sv->load_model('article');
  $this->blog_url = $sv->m['blog']->url;
  $this->t = $sv->t['posts'];
  
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
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
  'name' => 'user_slug',
  'title' => 'User',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove', 'default'),
  'write_in' => array()
  ));  
    
    
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Номер в блоге',
  'type' => 'bigint',
  'len' => '20',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit'), 
  
  ));  
        
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',
  'setcurrent' => 1,
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit')
  ));  
  
  
  $this->init_field(array(
  'name' => 'uid',
  'title' => 'Blog ID',
  'type' => 'int',
  'len' => '10',
  'input' => 'select',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'), 
  'belongs_to' => array('table' => 'blogs', 'field' => 'id', 'return' => 'title')
  ));  
      
  

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
  'show_in' => array('edit', 'remove', 'default'),
  'write_in' => array()
  ));  

  $this->init_field(array(
  'name' => 'agent',
  'title' => 'Agent',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));    
  
  $this->init_field(array(
  'name' => 'refer',
  'title' => 'Refer',
  'type' => 'varchar',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));    
   
}

function v_uid($id) {
  $id = intval($id);
  if ($id<=0) {
    $this->v_err = 1;
    $this->v_errm[] = "Неверный идентификатор блога: {$id}.";
  }
  return $id;
}

function v_slug($val) {
  global $db;
  
  $val = intval($val);
  $uid = intval($this->d['uid']);
  
  
  if ($this->get_item_wh("`slug`='{$val}' AND `id`<>'{$this->d['id']}' AND `uid`='{$uid}'", 0, 0)) {  
    $this->err = 1;
    $next = $this->get_next_slug($uid);
    $this->v_errm[] = "Такой номер публикации <b>{$val}</b> уже существует в этом блоге, значение автоматически изменено на <b>{$next}</b>.";
    $val = $next;
  }
  
  return $val;
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
// parsers 
function parse($d) {
  global $sv, $std;
  
  
  $d['blog_url']    =  $this->blog_url.$d['user_slug']."/";
  $d['url']         =  $d['blog_url']."?view={$d['slug']}";
  $d['edit_url']    =  $d['blog_url']."?post_edit={$d['id']}";
  $d['remove_url']  =  $d['blog_url']."?post_remove={$d['id']}";
  $d['view_url']    =  $d['blog_url']."?view={$d['slug']}";
  $d['f_date'] = $std->time->format($d['date'], 0.5, 1);
  
  $sv->load_model('tag');
  $d['f_tags'] = $sv->m['tag']->format_str($d['tags'], $d['blog_url']."?default&tag=", "");
  
  $d['f_text'] = $this->filter_bbcode($d['text']);
  
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
  
  return $d;
}

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

//stuff
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