<?php


class m_advstream extends class_model {


  var $tables = array(
    'adv_streams' => "
     `id` int(10) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `position` varchar(255) default NULL,
      `sort` int(11) NOT NULL default '0',
      `count` int(11) NOT NULL default '0',
      `active` tinyint(1) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `pos` (`position`),
      KEY `active` (`active`)
    "
  );
    
  var $positions = array(
  'top' => 'cверху',
  'left' => 'слева',
  'bottom' => 'снизу'
  );
  
  var $active_ids = array();
  var $active_list = array();
  var $active_list_parsed = false;
  
  var $pos_groups = array();
  
  var $find = array();
  var $replace = array();
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['adv_streams'];
  foreach($this->positions as $k=>$v) {
    $this->pos_groups[$k] = 0;  
  }
 

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'position',
  'title' => 'Расположение',
  'type' => 'varchar',
  'size' => '255',
  'len' => '30',
  'default' => '',
  'input' => 'select',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => $this->positions, 'not_null'=>1),
  
  
  ));  
  
   $this->init_field(array(
  'name' => 'sort',
  'title' => 'Сортировка',
  'type' => 'int',
  'size' => '11',
  'len' => '3',
  'default' => '',
  'show_in' => array('default'),
  'write_in' => array('create', 'edit')
  
  )); 
  
  $this->init_field(array(
  'name' => 'active',
  'title' => 'Включен',
  'type' => 'boolean',
  'default' => 0,
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));  
  
  $this->init_field(array(
  'name' => 'code',
  'title' => 'Код для вставки',
  'show_in' => array('default', 'edit', 'remove'),
  'virtual' => 'id'
  ));  
  
  
  $this->init_field(array(
  'name' => 'count',
  'title' => 'Активных блоков',
  'show_in' => array('default', 'edit', 'remove')  
  ));  
  
  
         
  
         
  
         
      
}

function v_title($val) {
  
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm = "Не указан адрес.";
  }
  
  return $val;  
}

function df_code($val) {   
  return "[stream={$val}]";
}
function vcb_code($val) {  
  return "[stream={$this->d['id']}]";
}


function before_default() {
  global $sv, $std, $db;
  
  $ar = array();
  $db->q("SELECT stream_id, count(*) as size FROM {$sv->t['adv_show']} GROUP BY stream_id", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[$d['stream_id']] = $d['size'];
  }
  $db->q("UPDATE {$this->t} SET `count`='0'");
  foreach($ar as $sid => $size) {
    $sid = intval($sid);
    $size = intval($size);
    $db->q("UPDATE {$this->t} SET `count`='{$size}' WHERE id='{$sid}'");
  }
  
  if (!isset($sv->_post['new']['items'])) return false;
  $ar = $sv->_post['new']['items'];
  foreach($ar as $k=>$d) {
    $id = intval($k);
    $active = (isset($d['active']) && $d['active']=='on') ? 1 : 0;
    $sort = intval(abs($d['sort']));
    $db->q("UPDATE {$this->t} SET active='{$active}', `sort`='{$sort}' WHERE id='{$id}'", __FILE__, __LINE__);
    
    
  }
  
}
function after_update($d, $p, $err) {
  global $db, $sv;
  
  if (!$err) {
    $db->q("
    UPDATE {$sv->t['adv_show']} 
    SET position='".addslashes($p['position'])."'             
    WHERE stream_id='{$this->current_record}'", __FILE__, __LINE__);    
  }

}


function parse($d) {
  $d['code'] = "[stream={$d['id']}]";
  return $d;
}

function get_active_list() {
  global $db;
  
  $this->active_list_parsed = true;
  
  $ids = array();
  
  $db->q("SELECT * FROM {$this->t} WHERE active='1' ORDER BY `sort` ASC", __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $this->parse($d);
    $ar[$d['position']][] = $d;
    $ids[] = $d['id'];
    $this->pos_groups[$d['position']] = (isset($this->pos_groups[$d['position']])) ? $this->pos_groups[$d['position']] + $d['count'] : $d['count'];
  }
  
  $this->active_ids = $ids;
  $this->active_list = $ar;
  return $ar;  
}

function get_replace_ar() {
  global $sv, $db;
  
  $f = array();
  $r = array();
  $blocks = array();
  
  $streams = $this->active_ids;
  
  
  if (count($streams)>0) {
    $in = implode(", ", $streams);
    $db->q("SELECT id, stream_id, text, position FROM {$sv->t['adv_show']} 
            WHERE stream_id IN ({$in}) ORDER BY time ASC", __FILE__, __LINE__);
    while($d = $db->f()) {
      $blocks[$d['stream_id']][] = $d;
    }
  }
  
  $showed = array();
  $positions = array();
  
  foreach($streams as $sid) {
    $code = "[stream={$sid}]";
    $f[] = $code;  
    
    if (isset($blocks[$sid])) {      
      //выбираем первый
      $text = $blocks[$sid][0]['text'];
      $showed[] = "'".$blocks[$sid][0]['id']."'";
      
      // группа занята
      $positions[$blocks[$sid][0]['position']] = true;
      
      unset($blocks[$sid]);
      
    }
    else {
      $text = "{$code}<br><small>В потоке отсутствуют показы рекламных блоков, назначьте показы или отключите поток.</small>";
    }
    $r[] = $text;    
  }
    
  if (count($showed)>0) {
    $in = implode(", ", $showed);
    $q = "UPDATE {$sv->t['adv_show']} SET `time`='{$sv->post_time}', `views`=`views`+1 WHERE id IN ({$in})";   
    $db->q($q, __FILE__, __LINE__);
  }
  
  $this->find = $f;
  $this->replace = $r; 
    
  return array('f'=>$f, 'r'=>$r);
}

/**
 * основная функция 
 *
 * @param unknown_type $html
 * @return unknown
 */
function replace($html) { 
  global $sv;
  
  $sv->parsed['streams'] = $this->get_active_list();
  
  $adv = $this->get_replace_ar();  

  if (isset($adv['f']) && is_array($adv['f']) && count($adv['f'])>0) {  
    $html = str_replace($adv['f'], $adv['r'], $html);  
  }
  
  //удаляем пустышки
  $html = preg_replace("#\[stream=[0-9]+\]#si", "", $html);
  
  return $html;
}



//eoc
}  
  
?>