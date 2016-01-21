<?php

class m_article extends class_model {
  
  var $tables = array(
    'articles' => "
      `id` bigint(20) NOT NULL auto_increment,
      `nomer_id` int(11) NOT NULL default '0',
      `title` varchar(255) NOT NULL default '',
      `slug` varchar(255) default NULL,
      `fraza` varchar(255) default NULL,
      `tags` varchar(255) default NULL,
      `date` datetime default '0000-00-00 00:00:00',
      `author` varchar(255) default NULL,
      `author_id` int(11) NOT NULL default '0',
      `cat_id` int(11) NOT NULL default '0',
      `status_id` int(11) NOT NULL default '0',
      `prioritet` int(11) NOT NULL default '50',
      `pop` float(6,4) NOT NULL default '0.0000',
      `views` int(11) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      `note` text,
      `aim_id` int(11) NOT NULL default '0',
      `icon` varchar(255) default NULL,
      `near_flag` tinyint(3) NOT NULL default '0',
      `ftopic` int(11) NOT NULL default '0',
      `replycount` int(11) NOT NULL default '0',
      `sended` tinyint(1) NOT NULL default '0',
      PRIMARY KEY  (`id`),
      KEY `nomer` (`nomer_id`),
      KEY `near` (`near_flag`),
      KEY `status` (`status_id`)    
    "
  );

   
  var $status_ar = array(
    0 => 'черновик',
    1 => 'опубликована',
    2 => 'отключена'
  );
  var $filters = array(
  'bbcode' => "BBCode упрощенный синтаксис [*]",
  'nl2br' => "автоподстановка &lt;br/&gt; (соблюдать абзацы)",
  'raw' => "чистый html"
  
  );
   
  var $default_filter_id = "bbcode";
  var $required_parts = array('preview');
  var $named_parts = array();
 
  var $parts;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['articles'];
  $this->parts_table = $sv->t['article_parts'];
  $this->per_page = 30;    
  
  
  
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1  
  ));  
  
  
  $this->init_field(array(
  'name' => 'icon',
  'title' => 'Иконка',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));
  
  
  $this->init_field(array(
  'name' => 'nomer_id',
  'title' => 'Газетный номер',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit', 'create'),
  'belongs_to' => array('table' => $sv->t['nomera'], 'field'=>'id', 'return' => 'nomer2')
  ));    
    

  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Путь к странице (англ)',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('edit')  
  ));  
  
   
  $this->init_field(array(
  'name' => 'fraza',
  'title' => 'Кульминационная фраза',
  'type' => 'text',
  'input' => 'varchar',
  'len' => '80',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  

     
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата и время публикации',
  'type' => 'datetime',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  

   
  $this->init_field(array(
  'name' => 'author',
  'title' => 'Автор',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));
  
  
  $this->init_field(array(
  'name' => 'author_id',
  'title' => 'Идентификатор автора',
  'type' => 'int',
  'size' => '11',
  'len' => '3',
  //'show_in' => array('default', 'remove'),
  //'write_in' => array('create', 'edit')
  ));

  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Рубрика',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit'),
  'belongs_to' => array('table' => $sv->t['maincats'], 'field'=>'id', 'return' => 'title')
  ));  
    

      

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '',
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));  
  

  $this->init_field(array(
  'name' => 'prioritet',
  'title' => 'Приоритет',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '50',
  'show_in' => array(),
  'write_in' => array()
  ));    
  
  $this->init_field(array(
  'name' => 'pop',
  'title' => 'Индекс популярности',
  'type' => 'float',
  'size' => '6,4',
  'len' => '10',
  'default' => '',
  'show_in' => array(),
  'write_in' => array()
  ));  
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Количество просмотров',
  'type' => 'int',
  'size' => '11',
  'len' => '10',
  'default' => '',
  'show_in' => array(),
  'write_in' => array()
  ));  
  
  $this->init_field(array(
  'name' => 'note',
  'title' => 'Служебная информация',
  'type' => 'text',
  'size' => '30',
  'len' => '30',
  'default' => '',
  'show_in' => array(),
  'write_in' => array('edit')
  ));    
  
  //virtual
  $this->init_field(array(
  'name' => 'nomer',
  'title' => 'Номер',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "nomer_id"
  ));    
  
  
  //virtual
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'write_in' => array(),
  'virtual' => "status_id"
  ));     

  
   
  $this->init_field(array(
  'name' => 'tags',
  'title' => 'Теги',
  'type' => 'text',
  'input' => 'varchar',
  'len' => '80',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  

   
  $this->init_field(array(
  'name' => 'sended',
  'title' => 'Разослана',
  'type' => 'tinyint',
  'input' => 'boolean',
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit')
  ));  

  
     
   
}

// CONTROLLERS 
function c_create() {
  global $sv, $std, $db;
  
  $s = $this->init_submit();
  $ret['s'] = $s;  
   
  foreach($this->fields as $f) {   
    if (in_array($this->code, $f['write_in'])) {   
      if ($f['name']=='nomer_id') continue;
      $rows[] = $this->wrow($f['name'], $f['input'], $s['v'][$f['name']], $f['title'], $f['len']);
    }  
  }

  
  
  $c_nomer_id = (isset($sv->_get['nomer'])) ? intval($sv->_get['nomer']) : 0;
  $t = $this->selector_nomer_id($c_nomer_id);
  $selector = "<select name='new[nomer_id]'>".implode("\n", $t)."</select>";
  $rows[] = "<tr bgcolor=white><td>Номер газеты</td><td>{$selector}</td>";
  
  $ret['form'] = $this->table($rows, "Создать");

  return $ret;
}
function c_edit() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  $d['icon'] = trim($d['icon']);
  
  $this->d = $d; 
  //parts
  $this->init_content($d['id']);     
  $s = $this->init_submit();    
  
  
  if ($s['submited'] && !$s['err']) {
    $d = $this->get_current_record();
   
  }
  
  
  
  $ret['s'] = $s;  
  $ret['d'] = $d;
    
  $ret['datetime'] = $std->time->datetime_box($d['date'], 1, "new[date]", 0, 1);
 
  
  // data for inputs
  $v = array();
  foreach($d as $k=>$val) { 
    $v[$k] = $std->text->cut($val, 'replace', 'replace');    
  }  
  $ret['v'] = $v;
  
  
  $opts = array();
  $opts['nomer_id'] = $this->selector_nomer_id($d['nomer_id']);

  foreach($this->status_ar as $k=>$v) {
    $s = ($d['status_id']==$k) ? " selected" : "";
    $opts['status_id'][] = "<option value='{$k}'{$s}>{$v}</option>";
  }
  
  $opts['cat_id'][] = "<option value='0'>&lt;рубрика не выбрана&gt;</option>";
  $db->q("SELECT * FROM {$sv->t['maincats']} ORDER BY title ASC", __FILE__, __LINE__);
  while($f = $db->f()) {
    $s = ($d['cat_id']==$f['id']) ? " selected" : "";
    $opts['cat_id'][] = "<option value='{$f['id']}'{$s}>{$f['title']}</option>";
  }
  
  foreach($opts as $k=>$ar) {
    $ret['opts'][$k] = implode("\n", $ar);
  }
  $ret['last_update'] = $std->time->format(strtotime($d['updated_at']), 3);
  
  $db->q("SELECT login FROM {$sv->t['account']} WHERE id='{$d['updated_by']}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $ret['last_login'] = $d['login'];
  }
  
   $ret['parts'] = $this->parts;  
 
   
  return $ret;
}

// VALIDATIONS
function v_title($val) {
  $val = trim($val);

  if ($val=='') {
    if ($this->code=='create') {
      $this->v_err = true;
    }
    $this->v_errm[] = "Не указан заголовок.";
  }      

  return $val;
}
function v_author($val) {
  $val = trim($val);
  if ($val=='') {
    $this->v_errm[] = "Не указан автор статьи.";
  }
  return $val;
}
function v_cat_id($val) {
  $val = intval($val);
  if ($val=='') {
    $this->v_errm[] = "Не выбрана рубрика.";
  }
  return $val;
}
function v_slug($val) {
  global $std, $db;
  
  $val = $std->text->translit($val);
  $val = preg_replace("#[\s]+#msi", "_", $val);
  
  if ($val=='' && isset($this->d['title']) && $this->d['title']!='') {
    $val = $std->text->translit($this->d['title']);
    $val = preg_replace("#[\s]+#msi", "_", $val);
  }
  
  $val = preg_replace("#[^a-z0-9\_\-\.]#msi", "", $val);
  
  if ($val=='') {
    $val = uniqid();
  }
  $val = strtolower($val);
  $i=1;
    
  $t = $val; 
  while(!$this->unique_slug($t, $this->current_record)) { $i++;
    $t = $val."_{$i}";
  }  
  $val = $t;
  
  return $val;  
}
function v_date($val) {
  global $sv, $db;
  
 
  return $val;
}
function v_icon($val) {
  $val = trim(basename($val));
  return $val;
}
function v_tags($val) {
  global $sv;
  $sv->load_model('tag');  
  $val = $sv->m['tag']->parse_str($val);  
  return $val;
}

//CALLBACKS
function df_icon($val) {
  global $sv;
  $val = trim($val);
  if ($val!='') {
    $val = "<img src='{$sv->vars['icons_url']}{$val}' width=50 height=50>";
  }
  return $val;
}

// pre / post actions
function before_create() {
  global $sv;
    
  /*
  $ar = array(  
    'year'=>date('Y', $sv->post_time),
    'month'=>date('m', $sv->post_time),
    'day'=>date('d', $sv->post_time),
    'h'=>date('H', $sv->post_time),
    'm'=>date('i', $sv->post_time),
    's'=>date('s', $sv->post_time)    
  );
  $this->add2roaster('date', $ar);
  
 */
  
}

function after_create($p, $err) {
 global $sv, $db;
 
  if (!$err) {
    $db->q("UPDATE {$this->t} SET `date`='{$sv->date_time}' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
  }
  
}

function after_update($p, $d, $err) {
  global $db, $sv;
  
  if(!$err) {
    //$this->update_url($this->current_record);
    $this->update_content();
    
    if (isset($sv->_post['commit'])) {
      header("Location: ".u($sv->act));
    }
  }
  
 
   
  if (!$err) {  
    $sv->load_model('tag');
    
    $cat_id = intval($p['cat_id']);
    $db->q("SELECT title FROM {$sv->t['maincats']} WHERE id='{$cat_id}'", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $f = $db->f();
      $cat_title = $f['title'];
    }
    else {
      $cat_title = "";
    }
   
    $sv->m['tag']->save_object_tags("article", $this->current_record, $p['tags'], $p['title'], $cat_title);
    
    
    
    if ($d['nomer_id']!=$p['nomer_id']) {
      $this->recount_nomer($p['nomer_id']);      
    }  
    $this->recount_nomer($d['nomer_id']);
    
  }
  
  
  if (!$err) {
 
    $db->q("SELECT id FROM {$sv->t['nomera']} WHERE active='1'");
    if ($db->nr()>0) {
      $t = $db->f();
      $active_nomer_id = $t['id'];
      
      //refreshing near_flag in articles      
      $next_id = $this->near_id($active_nomer_id, 1);
      $prev_id = $this->near_id($active_nomer_id, 0);
      
      $db->q("UPDATE {$sv->t['articles']} SET near_flag='0' WHERE near_flag<>'0'", __FILE__, __LINE__);
      $db->q("UPDATE {$sv->t['articles']} SET near_flag='1' WHERE nomer_id='{$next_id}'", __FILE__, __LINE__);
      $db->q("UPDATE {$sv->t['articles']} SET near_flag='2' WHERE nomer_id='{$prev_id}'", __FILE__, __LINE__);
      
    }
  }
  
  
  // установка иконки принудительная
  if (!$err && !$p['icon']) {    
    // из списка
    $sv->load_model('icon');
    $db->q("SELECT * FROM {$sv->t['icons']} WHERE `object`='{$this->current_record}' ORDER BY id DESC LIMIT 0,1", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $d = $db->f();      
      $p['icon'] = $icon = $d['savename'];
      $this->update_row(array('icon'=>$icon));      
    }   
  }
  
   
  // установка иконки принудительная 2
  if (!$err && !$p['icon']) {        
    // из файлов
    $sv->load_model('icon');
    $db->q("SELECT * FROM {$sv->t['aims']} WHERE `object`='{$this->current_record}' ORDER BY `leading` DESC LIMIT 0,1", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $aim = $db->f();
      $sv->m['icon']->aim2icon($aim['id'], $this->current_record);
      $icon = $sv->m['icon']->last_name;
      $this->update_row(array('icon'=>$icon));      
    }
  }
  
}

function garbage_collector($d) {
  global $sv, $std, $db;
  
  $db->q("DELETE FROM {$sv->t['article_parts']} WHERE object_id='{$d['id']}'", __FILE__, __LINE__);
  
  //removing images
  $sv->load_model('aim');
  $aims = array();
  $db->q("SELECT * FROM {$sv->t['aims']} WHERE object='{$d['id']}'", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $aims[] = $d;
  }
  foreach($aims as $d) {
    $r = $sv->m['aim']->garbage_collector($d);
  }
 
}

/**
 * Checking for parts and creating forbidden rows
 * 
 * (!) attention: no check for article_existsance
 * 
 * @param unknown_type $id
 */
function init_content($id, $check_page = true) {
  global $sv, $std, $db;
  
  $body_id = null;
  $parts = array();
  $id = intval($id);
  
  if ($check_page) {
    $db->q("SELECT 0 FROM {$this->t} WHERE id='{$id}'", __FILE__, __LINE__);
    if ($db->nr()<=0) {
      ec("Cant find page {$id} on init_content in m_page");
      return false;
    }
    
  }
  $db->q("SELECT * FROM {$this->parts_table} WHERE object_id='{$id}' ORDER BY id ASC", __FILE__, __LINE__);
  
  $i = 0;
  $names = array();
  while ($d = $db->f()) {
    $names[] = $d['name'];
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
  
  //required_parts
  foreach ($this->required_parts as $n) {
    if (!in_array($n, $names)) {
      $db->q("
      INSERT INTO {$this->parts_table} 
      SET `name`='{$n}', `object_id`='{$id}', content='', `filter_id`='{$this->default_filter_id}',
          `created_at`='{$sv->date_time}', created_by='{$sv->user['session']['account_id']}'", __FILE__, __LINE__);  
      $db->q("SELECT * FROM {$this->parts_table} WHERE object_id='{$id}' AND `name`='{$n}' ", __FILE__, __LINE__);
      if ($db->nr()>0) {
        $d = $db->f();
        $i++;
        
        $parts[$i] = $this->parse_content_part($d);        
      }
      else {
        die("Can't create <b>{$n}</b> part of page {$id} on ".__FILE__." - ".__LINE__);
      }          
        
    }  	
  }
    
 
  
  //no body
  if (is_null($body_id)) {
    $db->q("
    INSERT INTO {$this->parts_table} 
    SET `name`='body', `object_id`='{$id}', content='', `filter_id`='{$this->default_filter_id}', 
        `created_at`='{$sv->date_time}', created_by='{$sv->user['session']['account_id']}'", __FILE__, __LINE__);  
    $db->q("SELECT * FROM {$this->parts_table} WHERE object_id='{$id}' AND `name`='body' ", __FILE__, __LINE__);
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
  
  $quick = array();
  foreach($this->parts as $k=>$v) {
    $quick[$v['name']] = &$this->parts[$k];
    $quick[$v['name']]['i'] = $k;
  }
  $this->named_parts = $quick;
 
  $this->body_id = $body_id;
  
  return true;
}

/**
 * Applying filters
 *
 * @param unknown_type $d
 * @return unknown
 */
function parse_content_part($d) {
  global $std;
  
  $d['v_content'] = $std->text->cut($d['content'], 'replace', 'replace');
  $d['compiled'] = false;
  $d['filtred'] = false;
  
  $opts = array();
  foreach($this->filters as $k=>$v) {
    $s = ($d['filter_id']==$k) ? " selected" : "";
    $opts[] = "<option value='{$k}'{$s}>{$v}</option>";
  }
  
  $d['filter_opts'] = implode("\n", $opts);
  
  return $d;
}


/**
 * parts counted by 0-1-2-3 from body (0)
 *
 */
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
      $this->parts[$k]['filter_id'] = $new['filter_id'];
      $this->parts[$k] = $this->parse_content_part($this->parts[$k]);
    }
  }
  
  foreach($up as $id=>$d) {
    $id = intval($id);   
    $q = "UPDATE {$this->parts_table} 
    SET `content`='".addslashes($d['content'])."', 
        `filter_id`='".addslashes($d['filter_id'])."', 
        `date`='".addslashes($this->p['date'])."',
        `updated_at`='".addslashes($sv->date_time)."' WHERE id='{$id}'";
    $db->q($q, __FILE__, __LINE__);    
  }
 
}


/**
 * Applying filters
 *
 * @param unknown_type $d
 */
function compile_part($name) {
  
  $part = &$this->named_parts[$name];
 
  $part['compiled'] = true;
  
  $t = $part['content'];
  if (method_exists($this, "filter_{$part['filter_id']}")) {
    eval("\$t = \$this->filter_{$part['filter_id']}(\$t);");
    $part['filtred'] = true;
  }
  else {
    $part['filtred'] = false;
  }
  

  $part['c_content'] = $t;
  
  return $t;
}


function compile_text_from($d) {
  
  $t = $d['content'];
  $t = trim($t);
  if (method_exists($this, "filter_{$d['filter_id']}")) {
    eval("\$t = \$this->filter_{$d['filter_id']}(\$t);");   
  }
 
   
  return $t;
}


// PARSERS
function parse($d) {
  global $std;
  
  $d['f_time'] = $std->time->format($d['date'], 2, 1);
  
  
  $cat_url = (isset($d['cat_url'])) ? $d['cat_url'] : "articles";
  $d['url'] = "/".$cat_url."/".$d['slug'].".html";

  return $d;
}

function parse_for_send($d) {
  global $sv, $db, $std;
  $d = $this->parse($d);
  $t = "text";
  
  if (!$this->init_content($d['id'])) {
    return "";
  }
  
  $t = $this->compile_part('preview');
  $b = $this->compile_part('body');
  $b = (strlen($b)>2000) ? substr($b, 0, 2000)."..." : $b;
  $t = (trim($t)=='') ? $b : $t;

  $time = $std->time->format($d['date'], 0.5, 1);
  $a = ($d['author']) ? "({$d['author']})" : "";
  $ret = "«{$d['title']}» {$a}\n".
         "{$t}\n\n".
         "Читать дальше <http://norilsk-zv.ru{$d['url']}>";
         
  return $ret;
}

function parse_for_rss($d) {
  global $sv, $db, $std;
  $d = $this->parse($d);
  $t = "text";
  
  $this->init_content($d['id']);
  
  $t = $this->compile_part('preview');
  $b = $this->compile_part('body');
  $b = (strlen($b)>2000) ? substr($b, 0, 2000)."..." : $b;
  $t = (trim($t)=='') ? $b : $t;

  $ret = $t;
         
  return $ret;
}



function selector_nomer_id ($c = 0) {
  global $sv, $db;
  
  $ret = array();
  $db->q("SELECT * FROM {$sv->t['nomera']} ORDER BY nomer2 DESC, nomer DESC, id DESC", __FILE__, __LINE__);
  while($f = $db->f()) {
    $s = ($c==$f['id']) ? " selected" : "";
    $add = ($f['id']==$sv->parsed['nomer']['id']) ? " [текущий] " : "";
    $ret[] = "<option value='{$f['id']}'{$s}>{$f['nomer']} ({$f['nomer2']}) от {$f['date']} {$add}</option>";
  } 
  return $ret;
}

function unique_slug($val, $id=0) {
  global $sv, $db, $std;
  
  $val = addslashes($val);
  $id = intval($id);
  $db->q("SELECT * FROM {$this->t} WHERE slug='{$val}' AND id<>'{$this->current_record}'", __FILE__, __LINE__);
  if ($db->nr()>0) {    
    return false;
  }
  else {
    return true;
  }
  
}

/**
 * articles for top of page
 *
 */
function get_top_list($lim = 4) {
  global $sv, $db;
  $lim = intval($lim);
  
  $next = $this->select_list(" a.status_id='1' AND a.near_flag='1' AND a.icon<>'' ORDER BY RAND() LIMIT 0,{$lim}");
  $c = count($next);
  $s = $lim - $c;
  
  // if not enough get prev articles
  if ($s>0) {
    $prev = $this->select_list(" a.status_id='1' AND a.near_flag='2' AND a.icon<>'' ORDER BY RAND() LIMIT 0,{$s}");
    $ret = array_merge($next, $prev);
  }
  else {
    $ret = $next;
  }
  
  return $ret;
}

function select_list($wh) {
  global $sv, $db;
  
  $ret = array();
  $q = "  SELECT a.*, c.title as cat_title, c.url as cat_url 
          FROM {$this->t} a
          LEFT JOIN {$sv->t['maincats']} c ON (c.id=a.cat_id)
          WHERE {$wh}";
  $db->q($q, __FILE__, __LINE__);
  while ($d = $db->f()) {
    $d['cat_title'] = ($d['cat_title']=='') ? "Не выбрана" : $d['cat_title'];
    $d['cat_url'] = ($d['cat_url']=='') ? "articles" : $d['cat_url'];
    $d['url'] = "/{$d['cat_url']}/{$d['slug']}.html";
    
    $ret[$d['id']] = $d;
  }
  
  return $ret;
}


//FILTERS ===================================

function filter_raw($t) {
  
  
  return $t;
}

function filter_nl2br($t) {
  $t = nl2br($t);
  
  return $t;
}

function filter_bbcode($t) {  
  $bb = new bbcode($t);
  return $bb->get_html();  
}

function recount_nomer($id) {
  global $sv, $db, $std;
  
  $id = intval($id);
  $db->q("SELECT 0 FROM {$this->t} WHERE nomer_id='{$id}' AND status_id='1'", __FILE__, __LINE__);
  $size = $db->nr();
  
  $db->q("UPDATE {$sv->t['nomera']} SET `count`='{$size}' WHERE id='{$id}'", __FILE__, __LINE__);
  
}


function near_id($id, $dir = 1) {
  global $db, $sv;
  
  $id = intval($id);  
 
  $db->q("SELECT nomer2 FROM {$sv->t['nomera']} WHERE id='{$id}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $nomer2 = intval($d['nomer2']);
  }
  else {
    return 0;
  }
  

  $sql = ($dir == 1) ?  "`nomer2`>'{$nomer2}' ORDER BY `nomer2` ASC, `id` ASC" :  "`nomer2`<'{$nomer2}' ORDER BY `nomer2` DESC, `id` DESC"; 
              
  $q = "SELECT id FROM {$sv->t['nomera']} WHERE {$sql} LIMIT 0,1";
  
  $db->q($q, __FILE__, __LINE__);
  if ($db->nr()>0) {      
    $f = $db->f();     
   
    $ret = $f['id'];
  }
  else {
    $ret = 0;
  }    
  return $ret;
}


//end of class
}  
  
?>