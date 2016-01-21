<?php
/*
структура сайта
  У корневой записи pid is NULL
  
  ALTER TABLE `pages` ADD `description` varchar(255) NOT NULL default '' AFTER `title`;
  ALTER TABLE `pages` ADD `keywords` varchar(255) NOT NULL default '' AFTER `description`;
  ALTER TABLE `pages` ADD `page_title` varchar(255) NOT NULL default '' AFTER `title`;
  
 ALTER TABLE `pages` ADD `comments_on` tinyint(1) NOT NULL default '0' AFTER `count`;
 ALTER TABLE `pages` ADD `replycount` int(11) NOT NULL default '0' AFTER `comments_on`;
 
*/

class m_page extends class_model {

  var $tables = array(
    'pages' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `page_title` varchar(255) NOT NULL default '',
      `description` varchar(255) NOT NULL default '',
      `keywords` varchar(255) NOT NULL default '',
      
      `slug` varchar(255) NOT NULL default '',
      `breadcrumb` varchar(255) NOT NULL default '',
      `classname` varchar(255) default NULL,
      `parent_id` bigint(20) default '0',
      `layout_id` varchar(255) NOT NULL,
      `status_id` tinyint(3) NOT NULL default '0',
      `sitemap` tinyint(1) not null default '1',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,      
      
      `count` int(11) NOT NULL default '0',
      
      `comments_on` tinyint(1) not null default '0',
      `replycount` int(11) not null default '0',
      
      PRIMARY KEY  (`id`),
      KEY `slug` (`slug`)    
        ",
    
    'page_parts' => "
      `id` int(20) NOT NULL auto_increment,
      `page_id` bigint(20) default NULL,
      `name` varchar(255) default NULL,
      `content` longtext,
      `filter_id` varchar(50) default NULL,
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `page` (`page_id`)
        "
  );
    
  var $root_id = null;
  
  /**
   * Результат инициализации корневой страницы
   *
   * @var unknown_type
   */
  var $root = array(
    'd' => false,
    'err' => 0, 
    'errm' => array(),
    'err_box' => ''
  );
  
  var $status_ar = array(
    0 => 'черновик',
    1 => 'опубликована',
    2 => 'отключена'
  );
  
  var $filters = array(
  'raw' => "обычный HTML",
  'nl2br' => "автоперенос на новую строку",
  );
  
  /**
   * Список возможных компоновок, берется из папки templates/layouts/(\w).tpl название из контента файла: {* layout_title="Главная страница" *}
   *
   * @var unknown_type
   */
  var $layouts = array();
  
  /**
   * неотфильтрованные части текущей страницы
   *
   * @var unknown_type
   */
  var $parts = array(); 
  
  var $body_id = null;
  
  /**
   * перменные для построения деревеа
   *
   */
  var $tree_step = 0;
  var $tree_childs = array();
  var $tree_items = array();
  var $tree = array();
  var $tree_root = false;
  
function __construct() {
  global $sv;  

  $this->t = $sv->t['pages'];
  $this->per_page = 10;    
  
  if (!defined("PUBLIC_MODULES_DIR")) {
    define('PUBLIC_MODULES_DIR', MODULES_DIR);
  }
  
  $this->layouts = $this->scan_layouts();
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'len' => '40',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'makeroot', 'newchild'),
  'public_search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Путь к странице (англ)',
  'type' => 'varchar',
  'len' => '25',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit', 'newchild')
  
  ));  
  
  
  $this->init_field(array(
  'name' => 'sitemap',
  'title' => 'Показывать в карте сайта',
  'type' => 'boolean',
  'input' => 'checkbox',
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  
  ));  
  
  
  $this->init_field(array(
  'name' => 'comments_on',
  'title' => 'Комментарии включены?',
  'type' => 'boolean',
  'input' => 'checkbox',
  'default' => 0,
  'show_in' => array(),
  'write_in' => array('edit')  
  ));  
    
  $this->init_field(array(
  'name' => 'replycount',
  'title' => 'Кол-во комментариев',
  'type' => 'int',
  'len' => 15,
  'default' => 0,
  'show_in' => array(),
  'write_in' => array('edit')  
  ));      
    
    
  $this->init_field(array(
  'name' => 'page_title',
  'title' => 'Заголовок страницы',
  'type' => 'varchar',  
  'len' => '40',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit'),
  'public_search' => 1
  ));    

 $this->init_field(array(
  'name' => 'description',
  'title' => 'DESCRIPTION страницы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'public_search' => 1
  ));      
  
  
 $this->init_field(array(
  'name' => 'keywords',
  'title' => 'KEYWORDS страницы',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'public_search' => 1
  ));      
      
    
  $this->init_field(array(
  'name' => 'breadcrumb',
  'title' => 'Название в строке навигации',
  'type' => 'varchar',
  'size' => '255',
  'len' => '20',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'parent_id',
  'title' => 'Родительский раздел',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '',
  'show_in' => array(),
  'write_in' => array()
  ));  
  
  
  $this->init_field(array(
  'name' => 'classname',
  'title' => 'Тип страницы',
  'type' => 'varchar',
  'size' => '255',
  'input' => 'select',
  'default' => 'page',
  'show_in' => array(),
  'write_in' => array('edit'),
//  'belongs_to' => array('list' => $sv->modules->public_classes, 'not_null'=>1) // класс modules должен быть загружен раньше
  ));  
  
 
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '',
  'show_in' => array(),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));  
  
 
  $this->init_field(array(
  'name' => 'layout_id',
  'title' => 'Компоновка блоков',
  'type' => 'varchar',
  'len' => '20',
  'default' => '',
  'input' => 'select',
  'show_in' => array('default'),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->layouts, 'not_null'=>1)
  ));  

}

function parse($d) {
  return $d;
}

function parse_search($d) {
  global $std, $sv;
  
  $sv->load_model('url');
  
  $title = $d['title'];
  $desc = '';  
  $u = $sv->m['url']->get_item_wh("`page`='{$d['id']}'");
  $url = ($u) ? $u['url']."/" : '';
  
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}

//CONTROLLERS ===========================
/**
 * Default view - TREE
 *
 * @return unknown
 */
function c_default() {
  global $sv, $db;

  $ret['root'] = $this->init_root();
 
  // пересчитываем подразделы
  $this->recount_childs();
  
  
  $ar = array(); $in_ar = array();
  $db->q("  SELECT p.*, u.url 
            FROM {$this->t} p
            LEFT JOIN {$sv->t['urls']} u ON (p.id=u.page AND u.primary='1')
            WHERE p.parent_id='{$this->root_id}' AND p.parent_id IS NOT NULL ORDER BY p.sitemap desc, p.title asc", __FILE__, __LINE__);
  while($d = $db->f()) {   
    $ar[$d['id']] = $d;
    $in_ar[] = $d['id'];
  }
  $c = count($ar);
  
  // deprecated counting
  /*
  if ($c>0) {
    $in = implode(", ", $in_ar); 
    $db->q("SELECT id, count(*) as count FROM {$this->t} WHERE parent_id IN ({$in}) GROUP BY id", __FILE__, __LINE__);
    while ($d = $db->f()) {
      $ar[$d['id']]['count'] = $d['count'];
    }
  }
  */
  
  $ret['list'] = $ar;
  if ($ret['root']!==false) {
    $ret['root']['count'] = count($ar);
  }
 
  return $ret;
  
}

function c_makeroot() {
  return $this->ec_create();  
}

function sc_makeroot($n) {
  global $sv, $std, $db;

  $err = 0;
  $f = 'title';   
  $v[$f] = $p[$f] = $this->validate_field($f, $n[$f]);     
  
  // validation errors
  $err = ($this->v_err) ? true : $err;
  
  $p = array();
  $p['title'] = $n['title'];
  $p['slug'] = "/";
  $p['breadcrumb'] = "Главная";
  $p['parent_id'] = null;
  $p['classname'] = 'main';
  
  if (!$err) {
    $r = $this->get_root();
    if ($r['d']!==false) {
      $err = 1;
    }    
  }
  
  if (!$err) {  
    $af = $this->insert_row($p);
    if ($af <= 0) {
      $err = 1;
      $this->errm("DB error, can't insert data.", $err);
    }
    else {
      $this->errm("Главная страница успешно создана.");
      $this->current_record = $db->insert_id();
    } 
  }
  
  $this->after_create($p, $err);
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  $ret['err'] = $err;
  $ret['v'] = $v;  
  
  return $ret;
}

/**
 * Создание подраздела
 *
 * @return unknown
 */
function c_newchild() {    
  global $sv;
  
  return $this->ec_create();  
}

function sc_newchild($n) {
  global $sv;
  
  $this->add2roaster('parent_id', $sv->id);  
  return $this->esc_create($n);
}

/**
 * Редактирование страницы
 *
 * @return unknown
 */
function c_edit() {
  global $sv, $std, $db;
  
  $this->d = $d = $this->get_current_record();
   
  //parts
  $this->init_content($d['id']);     
  $s = $this->init_submit();  
  if ($s['submited'] && !$s['err']) {
    //reinit
    $this->init_content($d['id']);
  }
  $ret['err_box'] = $s['err_box'];

  if ($s['submited'] && !$s['err']) {
    $d = $this->get_current_record();
  }
  $ret['d'] = $d;
     
  // data for inputs
  $v = array();
  foreach($d as $k=>$val) { 
    if ($s['submited'] && isset($s['v'][$k])) {
      $val = $s['v'][$k];
    }
    $v[$k] = $std->text->cut($val, 'replace', 'replace');    
  }  
  $ret['v'] = $v;
  
  
  $opts = array();
  foreach($sv->modules->public_classes as $k=>$v) {
    $s = ($d['classname']==$k) ? " selected" : "";
    $opts['classname'][] = "<option value='{$k}'{$s}>{$v}</option>";
  } 
  foreach($this->status_ar as $k=>$v) {
    $s = ($d['status_id']==$k) ? " selected" : "";
    $opts['status_id'][] = "<option value='{$k}'{$s}>{$v}</option>";
  }
    
  // layouts
  $opts['layout_id'][] = "<option value=''>&lt;родительского раздела&gt;</option>";
  foreach($this->layouts as $k=>$v) {
    $s = ($d['layout_id']==$k) ? " selected" : "";
    $opts['layout_id'][] = "<option value='{$k}'{$s}>{$v}</option>";
  }

  // compiling opts  
  foreach($opts as $k=>$ar) {
    $ret['opts'][$k] = implode("\n", $ar);
  }
  $ret['last_update'] = $std->time->format(strtotime($d['updated_at']), 3);
  
  $db->q("SELECT login FROM {$sv->t['accounts']} WHERE id='{$d['updated_by']}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $ret['last_login'] = $d['login'];
  }
   
  $ret['parts'] = $this->parts;  
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $this->current_record);
  $ret['attach'] = $sv->m['attach']->init_object($this->name, $this->current_record, $sv->user['session']['account_id']);


  // markitup
  $std->markitup->use_tinymce = 1;
  $std->markitup->use_emoticons = 0;  
  $std->markitup->width = '100%';
  
  $std->markitup->compile('textarea', 'html');
  $ret['markitup'] = &$std->markitup;    
  
 
  
  // текущий шаблон
  $c_layout = $this->get_layout_id($ret['d']['id']);
  
  // связанные инфоблоки
  $sv->load_model('infoblock');
  $related_blocks = $sv->m['infoblock']->related_blocks($c_layout);
  $rb = ($related_blocks!='') ? "<div style='margin-top: 30px; padding: 0px;'>
      <div style='padding-left: 7px;color: gray;'><small>Дополнительные блоки</small></div>{$related_blocks}</div>" : "";
  
  // sidebar
  $sv->parsed['admin_sidebar'] = "
    {$rb}
    <div style='margin-top: 190px; border: 1px solid #dddddd;'>
      <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
      {$ret['attach']['form']}
    </div>";
    
  return $ret;
}


//VALIDATIONS ====================

function v_title($val) {  
  $val = trim($val);
  $val = strip_tags($val);

  if ($val=='') {
    $this->v_err = 1;
    $this->errm("Заголовок не указан.");     
  } 
  
  return $val;
}

function v_slug($val) {  
  global $sv, $db;
  
  if ($this->code=='edit' && $this->d['id']==$this->root_id) {
    return "/";
  }
  $val = preg_replace("#[^a-z0-9\_\-\.\/]#msi", "", $val);
  
  if ($val=='') {
    $this->errm("Адрес для ссылки не указан.", 1); 
    $this->v_err = 1;
  }  
  else {
    
    // check existance    
    $sv->load_model('url');
    $parent_id = (isset($this->d['parent_id'])) ? $this->d['parent_id'] : 0;
    $parent_id = ($parent_id==0 && isset($this->n['parent_id'])) ? $this->n['parent_id'] : $parent_id;
    $parent_url = $this->build_url($parent_id);
    $url = $parent_url."/".$val;
    $current_page_fix = ($this->code=='edit' && isset($this->d['id'])) ? " AND `page`<>'".$db->esc($this->d['id'])."'" : "";
    $d = $sv->m['url']->get_item_wh("`url`='".$db->esc($url)."'{$current_page_fix}");
    if ($d) { 
      $this->v_err = 1;
      $this->errm("Такой путь к странице уже существует, придумайте альтернативный вариант.", 1);
    }
   
  }
  return $val;
}

function v_before_remove($d) {
  global $sv, $std, $db;
  
  
  $ar = array();
  $db->q("SELECT * FROM {$this->t} WHERE parent_id='{$this->current_record}'", __FILE__, __LINE__);
  while ( $d = $db->f()) {
    $ar[] = "<a href='".u('pages', 'edit', $d['id'])."'>{$d['title']}</a>";
  }
  
  if (count($ar)>0) {
    $this->v_err = 1;
    $this->errm("Раздел не пуст и имеет поздразделы, сначало удалите их: ".implode(", ", $ar), 1);
  }
    
  return $d;
}

function last_v($p) {
  
  if ($this->code=='create' || $this->code=='newchild')  {
    $p['classname'] = 'page';
  }
  
  if (!isset($p['page_title']) || $p['page_title']=='') {
    $p['page_title'] = $p['title'];
  }
  return $p;
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

function after_update($d, $p, $err) {
global $db, $sv;
  
  $this->update_content();
  
  if(!$err) {    
    
    $this->update_url($this->current_record, $d['slug']);
    
    if (isset($sv->_post['commit'])) {
      header("Location: ".u($sv->act));
    }
  }
}


function after_create($p, $err) {
global $db, $sv;
 
  
  if(!$err) {    
    
    $this->update_url($this->current_record);
    
    if (isset($sv->_post['commit'])) {
      header("Location: ".u($sv->act));
      exit();
    }
  }
}




function garbage_collector($d) {
  global $sv, $std, $db;

  $db->q("DELETE FROM {$sv->t['page_parts']} WHERE page_id='{$d['id']}'", __FILE__, __LINE__);
  $db->q("DELETE FROM {$sv->t['urls']} WHERE page='{$d['id']}'", __FILE__, __LINE__);
  
  /*
  //removing files
  $sv->load_model('upload');
  $uploads = array();
  $db->q("SELECT * FROM {$sv->t['uploads']} WHERE object='{$d['id']}'", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $uploads[] = $d;
  }
  foreach($uploads as $d) {
    $r = $sv->m['upload']->garbage_collector($d);
  }
*/
  
}

//func
function recount_childs() {
  global $sv, $db;
  
  $count = array();
  $current = array();
  $db->q("SELECT id, parent_id, `count` FROM {$this->t}", __FILE__, __LINE__);
  while($d = $db->f()) {
    $count[$d['parent_id']] = (isset($count[$d['parent_id']])) ? $count[$d['parent_id']]+1 : 1;
    $current[$d['id']] = $d['count'];
    
  }
  
  $ret = array();
  $update = array();
  
  foreach($current as $id => $c) {
    if (isset($count[$id]) && $count[$id]!=$c) {
      $current[$id] = $count[$id];
      $update[$id] = $count[$id];
    }
    elseif (!isset($count[$id]) && $c!=0) {
      $current[$id] = 0;
      $update[$id] = 0;
    }
    else {
      // isset($count) AND count==$c or !isset(count) AND $c==0
      // continue;
    }
  }
  
  foreach($update as $id => $c) {
    $id = intval($id);
    $c = intval($c);
    $db->q("UPDATE {$this->t} SET `count`='{$c}' WHERE id='{$id}'", __FILE__, __LINE__);    
  }
 
}


// stuff
/**
 * Инициализация корневой страницы
 *
 * @return unknown
 */
function init_root() {
  
  if (is_null($this->root_id)) {
    $this->root = $this->get_root();
  }
  
  return $this->root;
}

function get_root() {
  global $db, $std, $sv;

  $err = false;
  $d = false;
  
  $db->q("SELECT * FROM {$this->t} WHERE parent_id IS NULL ORDER BY id", __FILE__, __LINE__);
  $s = $db->nr();
  
  if ($s > 0) {    
    $d = $db->f();    
    $d['expanded'] = 1; // root по умолчанию раскрыт
    if ($s>1)  {
      $err = 1;
      $this->errm("Найдено более одного раздела с параметрами главной станицы. Сообщите об ошибке администратору.", $err);
    }
    else {
      $this->root_id = $d['id'];
    }
  }
  else {    
    $err = 1;
    if ($this->code!='makeroot') {
      $this->errm("Главная страница не найдена. <A href='".u($sv->act, 'makeroot')."'>Создать главную страницу.</a>", $err);
    }
  }
  
  $r['err']  = $err;
  $r['d'] = $d;
  
  return $r;
}



function replace_changed_slug($find, $replace, $t) {
  
  $find = preg_quote($find);
    
  $t = preg_replace("#/{$find}/#msi", "/".$replace."/", $t);
  $t = preg_replace("#/{$find}$#msi", "/".$replace, $t);
          
  return $t;       
}
/**
 * Обновляем пути для страницы
 *
 * @param unknown_type $id
 * @return unknown
 */
function update_url($id, $old_slug="") {
  global $sv, $std, $db;
  
  $id = intval($id);  
  // получаем начальные данные
  $page = $this->get_item($id);
  if (!$page) {
    return false;
  }
  $pid = $page['parent_id'];
  
  // Автозамена всех ссылок в случае изменения родителя.
  // проверяем изменился ли идентификатор
  if ($old_slug!='') {
    if ($page['slug']!=$old_slug) {
      
      // изменился меняем все вхождения старого идентификатора на новый
      t("Updating {$old_slug} to {$page['slug']}", 1);
      
      $replace_ar = array();
      $like = $db->esc($old_slug);      
      $db->q("SELECT id, url FROM {$sv->t['urls']} WHERE url LIKE \"%{$like}%\"", __FILE__, __LINE__);
      while($d = $db->f()) {
        $replace_ar[$d['id']] = $this->replace_changed_slug($old_slug, $page['slug'], $d['url']);
      }
      foreach ($replace_ar as $id => $url) {
        $id = intval($id);
        $url = addslashes($url);
        $db->q("UPDATE {$sv->t['urls']} SET `url`='{$url}' WHERE id='{$id}'", __FILE__, __LINE__);
      }
      t($replace_ar);
    }
  }


  $sv->load_model('url');
    
  // обновляем основной урл
  $url = $this->build_url($page['id'], $page);
  $p = array('page' => $page['id'], 'url'=>$url, 'title' => $page['title'], 'primary' => 1);
  $sv->m['url']->sync_url_wh("`page`='{$page['id']}' AND `primary`='1' AND `module` IS NULL", $p);   
  
  
  //getting sub urls from module  (ex. search -> query | results)
  $urls = array();
  $fn = PUBLIC_MODULES_DIR.basename($page['classname'].".php");
  if (!file_exists($fn)) {
    echo "Warning: module file <b>{$fn}</b> not found.";
  }
  else {
    include($fn);
    $module = new $page['classname'];   
    if (isset($module->codes) && is_array($module->codes))  {
      $t_url = ($url == '/') ? "" : $url;
      foreach($module->codes as $code) {
        $urls[] = array('page' => $page['id'], 'url' => $t_url."/".$code, 'title' => $page['title']." / {$code}", 'primary' => 0, 'module' => '#');
      }
    }
  } 
  $db->q("DELETE FROM {$sv->t['urls']} WHERE `page`='{$page['id']}' AND `primary`='0' AND (`module` IS NULL OR `module`='#')", __FILE__, __LINE__);  
  foreach($urls as $p) {   
    if (!$sv->m['url']->get_item_wh("`url`='".$db->esc($p['url'])."'")) {
      $sv->m['url']->insert_row($p);
    }
  }
  
  
}

function build_url($page_id = 0, $page = false) {
  global $sv;  
  
  if (!$page) {
    $page = $this->get_item($page_id, 0, 0);
  }
  if (!$page) {
    return false;
  }
  
  //рекурсивно получаем список родительских строк и формируем текущий урл
  $ar = array();
  $f = $page;
  while($f!==false) {
    $ar[$f['id']] = $f['slug'];
    $f = $this->slug_by_id($f['parent_id']);
  }
  $ar = array_reverse($ar);  
  if ($ar[0]=='/') unset($ar[0]);
  $url = "/".implode("/", $ar);
  
  return $url;
}
function update_content() {
  global $sv, $std, $db;
  
  $n = (isset($this->n['parts'])) ? $this->n['parts'] : array();
  $ar = $this->parts;
 
  $fk = array_keys($this->filters);
  $up = array();
  foreach ($n as $k => $new_part) {
    
    $v = $new_part['content'];
    $v = $std->text->cut($v, 'allow', 'mstrip');
    
    //if exists part with that num
    if (isset($ar[$k]['content'])) {
      $new['content'] = $v;
      $new['filter_id'] = (in_array($new_part['filter_id'], $fk)) ? $new_part['filter_id'] : "";
      $up[$ar[$k]['id']] = $new;
      
      $this->parts[$k]['content'] = $v;
      $this->parts[$k] = $this->parse_content_part($this->parts[$k]);
    }
  }
  
  foreach($up as $id=>$d) {
    $id = intval($id);   
    $q = "UPDATE {$sv->t['page_parts']} SET `content`='".addslashes($d['content'])."', `filter_id`='".addslashes($d['filter_id'])."' WHERE id='{$id}'";
    $db->q($q, __FILE__, __LINE__);    
  }
 
}

function slug_by_id($id) {
  global $db;

  
  $db->q("SELECT id, slug, parent_id FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()<=0) {
    return false;
  }
  
  $d = $db->f();
  
  return $d;
}

function ajax_page_childs($id) {
  global $sv, $db, $smarty;
  
  $id = intval($id);
  $page = $this->get_item($id);
  if (!$page) { return "page {$id} not found."; }

   

  $ar = array();
  $db->q("  SELECT p.*, u.url 
            FROM {$this->t} p
            LEFT JOIN {$sv->t['urls']} u ON (p.id=u.page AND u.primary='1')
            WHERE p.parent_id='{$id}' ORDER BY p.sitemap desc, p.title asc", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[] = $d;
  }
  //$ar = $this->item_list("parent_id='{$id}'", "title ASC", 0, 0);
  
  $tr = array();
  
  $sv->act = "pages";
  $smarty->assign("sv", $sv);
  
  foreach($ar as $d) {
    $smarty->assign("d", $d);
    $table = $smarty->fetch("parts/page_row.tpl");
    $tr[] = "{$table} \n<div class='page-childs' id='page-{$d['id']}-childs' style='display:none;'></div>";
    
  }
    
  $ret = implode("\n\n", $tr);
  return $ret;
}


/**
 * attent: no check for page_existsance
 *
 * @param unknown_type $id
 */
function init_content($id, $check_page = true) {
  global $sv, $std, $db;
  
  $body_id = null;
  $parts = array();
  $id = intval($id);
  
  if ($check_page) {
    $page = $this->get_item($id, 1);
    if (!$page) {
      die("Can't find page {$id} in <b>page->init_content()</b>");
    }    
  }
  
  $db->q("SELECT * FROM {$sv->t['page_parts']} WHERE page_id='{$id}' ORDER BY id ASC", __FILE__, __LINE__);
  
  $i = 0;
  while ($d = $db->f()) {
    if ($d['name']==='body' && is_null($body_id)) {
      $body_id = $d['id'];
      $parts[0] = $this->parse_content_part($d);
    }
    elseif ($d['name']==='body') {
      $d['name'] = "body_copy(!)";
      $i++;
      $parts[$i] = $this->parse_content_part($d);
    }
    else {
      $i++;
      $parts[$i] = $this->parse_content_part($d);
    }
  }
  
  //no body
  if (is_null($body_id)) {
    $db->q("
    INSERT INTO {$sv->t['page_parts']} 
    SET `name`='body', `page_id`='{$id}', content='', 
        `created_at`='{$sv->date_time}', created_by='{$sv->user['session']['account_id']}'", __FILE__, __LINE__);  
    $db->q("SELECT * FROM {$sv->t['page_parts']} WHERE page_id='{$id}' AND `name`='body' ", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $d = $db->f();
      $parts[0] = $this->parse_content_part($d);
      $body_id = $d['id'];
    }
    else {
      die("Can't create BODY part of page {$id} on ".__FILE__." - ".__LINE__);
    }    
  }
  
  $this->parts = $parts;
  $this->body_id = $body_id;
  
}

/**
 * Общая функция для получения готового контента страницы по id
 *
 * @param unknown_type $page_id
 * @return unknown
 */
function get_content($page_id) {
  
  $this->init_content($page_id);
  
  $body = $this->compile_part($this->parts[0]);
  
  return $body;
}


/* PARTS  
   parts counted by 0-1-2-3 from body (0)
*/
function compile_part($d) {
  global $sv;
  
  $t = $d['content'];
  
  switch ($d['filter_id']) {
    case 'nl2br':
      $t = nl2br($t);
    break;
    case 'raw': default:
      //nothing
  }
  
  // замена [video][/video] тегов на плеер
  $sv->load_model('attach');
  if (method_exists($sv->m['attach'], "process_text")) {
    $t = $sv->m['attach']->process_text($t, $d['filter_id']);
  }
  
  return $t;
}

function parse_content_part($d) {
  global $std;
  
  $d['v_content'] = $std->text->cut($d['content'], 'replace', 'replace');
  
  $opts = array();
  foreach($this->filters as $k=>$v) {
    $s = ($d['filter_id']==$k) ? " selected" : "";
    $opts[] = "<option value='{$k}'{$s}>{$v}</option>";
  }
  
  $d['filter_opts'] = implode("\n", $opts);
  
  return $d;
}

/**
 * Возвращает массив возможных компоновок, берется из папки templates/layouts/(\w).tpl название из контента файла: {* layout_title="Главная страница" *}
 *
 */
function scan_layouts() {
  global $smarty, $std;
  
  
  if (!defined("LAYOUTS_DIR")) {
    $this->log(__FUNCTION__." not defined LAYOUTS_DIR");
    $dir = $smarty->template_dir;
  }
  else {
    $dir = LAYOUTS_DIR;
  }
  
  $dir = preg_replace("#/*$#si", "", $dir);
  $ar = $std->file->file_list($dir, 0);
  
  $ret = array();
  foreach($ar as $fn) {
    if ($fn=='default.tpl') continue;
    if (preg_match("#^([a-z0-9\_\-]+)\.tpl$#si", $fn, $m)) {
      $code = strtolower($m[1]);
    }
    else {
      continue;
    }
    
    $file = file_get_contents($dir."/".$fn);
    $title = (preg_match("#layout_title=\"([^\"]+)\"#si", $file, $m)) ? $m[1] : $code;
    $ret[$code] = $title;
  }
  
  return $ret;
}

/**
 * Получаем код текущего лейаута
 *
 * @param unknown_type $page_id
 * @param unknown_type $page = соотвествующая запись из таблицы
 * @param unknown_type $url = урл по которому получаем родительские коды, если не указан то указанной страницы
 * @return string $ret = код
 */
function get_layout_id($page_id, $page = false, $url = "") {
  global $sv, $db;
  
  $default = "default";  
  $page_id = intval($page_id);
  
  // если не заданы параметры страницы выбираем их
  if (!$page) {
    $page = $this->get_item($page_id, 1);
    if (!$page) {
      $this->log(__FUNCTION__." can't get PAGE data by id={$page_id}");
      return $default;
    }
  }
  
  // если у страницы указан код возвращаем его
  if ($page['layout_id']!='') {
    return $page['layout_id'];
  }
  // если главная то сразу возращаем дефолтный
  elseif ($url=='/') {
    return $default;
  }  
  // иначе пробуем вычленить из родителей
  elseif($url=='' && isset($sv->view->page['id']) && $sv->view->page['id']==$page_id && $sv->view->d['url']) {
    $url = $sv->view->d['url'];
  }
  // и в самом крайнем случае (например в админке) запрашиваем из базы по page_id
  else {
    $db->q("SELECT url FROM {$sv->t['urls']} WHERE `page`='{$page_id}'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $f = $db->f();
      $url = $f['url'];
    }
  }
  
  if ($url=='') {
    $this->log(__FUNCTION__." can't get current URL");
    return $default;
  }
  
  $last_layout = '';
  $parents = $this->parse_parents_by_url($url, 1);
  foreach($parents as $d) {
    if ($d['layout_id']!='') {
      $last_layout = $d['layout_id'];
    }
  }
 
  $ret = ($last_layout!='') ? $last_layout : $default;
  
  return $ret;
}

/**
 * Построение дерева подразделов
 *
 * @param unknown_type $page_id
 * @return unknown
 */
function build_tree($page_id = 1, $sitemap = 1, $unset_after = 1, $only_on = 1) {
  global $sv, $std, $db;
  
  $page_id = intval($page_id);
  $childs = array();
  $ar = array();
  
  $wh_ar = array();
  if ($only_on) {
    $wh_ar[] = "p.status_id='1'";
  }  
  if ($sitemap<2) {
    $wh_ar[] = "p.sitemap='{$sitemap}'";
  }
  $wh_ar[] = "u.primary='1'";
  
  $wh = (count($wh_ar)>0) ? "WHERE ".implode(" AND ", $wh_ar) : "";
  $db->q("  SELECT p.id, p.title, p.parent_id, p.status_id, u.url
            FROM {$this->t} p
            LEFT JOIN {$sv->t['urls']} u ON (p.id=u.page)
            {$wh} ORDER BY p.title ASC", __FILE__, __LINE__);
  
  while($d = $db->f()) {    
    $ar[$d['id']] = $d;
    $childs[$d['parent_id']][] = $d['id'];
  }


  $this->tree = array();
  $this->tree_step = 0;
  $this->tree_items = $ar;
  $this->tree_childs = $childs;
  
  // если не найдна указанная страница
  if (!isset($ar[$page_id])) {
    $this->log(__FUNCTION__ . " " . __FILE__ . " " . __LINE__ . " не найдена, либо скрыта корневая страница {$page_id}");
    return $this->tree;
  }
  else {
    $root = $ar[$page_id];
    $root['step']  = $this->tree_step;
  }
  
  // корневой элемент  
  $this->tree[] = $root;
  
  // если нет потомков
  if (!isset($childs[$page_id])) {
    return $this->tree;
  }
  
  foreach($childs[$page_id] as $ch_id) {
    $this->tree_step++;
    
    $d = &$this->tree_items[$ch_id];
    $d['step'] = $this->tree_step;
    $this->tree[] = $d;
    
    // если есть потомки запрашиваем рекурсивно все дерево
    if (isset($this->tree_childs[$d['id']])) {
      $this->tree_subs($d['id']);
    }
    $this->tree_step--;    
  }
  
  $ret = $this->tree;
  
  if ($unset_after) {
    $this->tree = array();
    $this->tree_step = 0;
    $this->tree_items = array();
    $this->tree_childs = array();  
  }
  
  return $ret;
}

/**
 * Вспомогательная рекурсивная функция для построения дерева
 *
 * @param unknown_type $page_id
 * @return unknown
 */
function tree_subs($page_id) { 
  // если страницы нет в списке или нет потомков
  if (!isset($this->tree_items[$page_id]) || !isset($this->tree_childs[$page_id])) {
    return false;
  }
  
  $this->tree_step++;
  
  // перебираем потомков и запрашиваем рекурсивно потомков второго уровня и дальше
  foreach($this->tree_childs[$page_id] as $ch_id) {
    $d = $this->tree_items[$ch_id];
    $d['step'] = $this->tree_step;
    $this->tree[] = $d;
    if (isset($this->tree_childs[$d['id']])) {
      $this->tree_subs($d['id']);
    }
  }
  
  $this->tree_step--;    
  return true;
}

/**
 * Получение массива хлебных крошек от корня до указанного элемента
 *
 * @param unknown_type $page_id
 * @param boolean $with_home
 * @return unknown
 */
function build_breadcrumb($page_id = 0, $with_home = 1, $parse = 0)  {
  global $sv, $std, $db;
  
  $page_id = intval($page_id);
  $page_id = ($page_id == 0) ? $sv->view->page['id'] : 0;
  
  $pages = array();
  $page = $this->get_item($page_id);
  if (!$page) {
    $this->errm("Не найдена указанная страница для breadcrumb {$page_id}", 1);
    return false;
  }
  
  // выбираем страницу по id с самым коротким урл
  $db->q("SELECT * FROM {$sv->t['urls']} WHERE `page`='{$page_id}' ORDER BY url ASC LIMIT 0,1", __FILE__, __LINE__);
  if ($db->nr()<=0)   {
    $this->errm("Не найден URL для breadcrumb {$page_id}", 1);
    return false;
  }
  else {
    $d = $db->f();
    if ($parse) {
      $d = $this->parse($d);
    }
    $url = $d['url'];
  }
  $page['url'] = $url;

  // родители
  $ret = $this->parse_parents_by_url($url, $with_home, $parse);  

  // текущая
  $ret[$page['url']] = $page;
  
  return $ret;
}

function parse_parents_by_url($url, $with_home = 0, $parse = 0) {
  global $sv, $db, $std;
  
  $ret = array();
  
  // если нужна главная
  if ($with_home) {
    $home = $this->get_item_wh("`parent_id` IS NULL", $parse);
    if ($home) {     
      $home['url'] = "/";
      $ret[$home['url']] = $home;
    }
  }
  
  // рекурсивно разбираем урл, на дочерние пути
  $urls = array();   
  $c_url = $url;
  while(preg_match("#^(.+)/[^/]*#si", $c_url, $m)) {
    $urls[] = $m[1];
    $c_url = $m[1];
  }
  
  $urls = array_reverse($urls);
  
  $wh = array();
  foreach($urls as $url) {   
    if ($url!='') {
      $wh[] = "u.url='".$db->esc($url)."'";
    }    
  }  
  if (count($wh)>0) {
    $db->q("  SELECT p.*, u.url 
              FROM {$sv->t['urls']} u              
              LEFT JOIN {$this->t} p 
              ON (u.page=p.id)
              WHERE ".implode(" OR ", $wh)." ", __FILE__, __LINE__);
    while($d = $db->f()) {
      if ($parse) {
        $d = $this->parse($d);
      }
      $ret[$d['url']] = $d;
    }
  }

  return $ret;
}
//end of class
}  
  
?>