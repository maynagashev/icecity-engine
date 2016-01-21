<?php

/*
Обновленная модель инфоблока / сниппета
v 2.1
*/


class m_infoblock extends class_model {

  var $tables = array(
    'infoblocks' => "    
      `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
      `slug` varchar(255) not null default '',      
      `title` VARCHAR( 255 ) NULL ,
      `text` TEXT NULL ,
      `layouts` varchar(255) NULL,
      `status_id` TINYINT( 1 ) NOT NULL DEFAULT '1',
      
      `created_at` DATETIME NULL ,
      `created_by` INT( 11 ) NULL ,
      `updated_at` DATETIME NULL ,
      `updated_by` INT( 11 ) NULL ,
      
      PRIMARY KEY ( `id` ),
      KEY (`slug`),
      KEY (`layouts`)
    "
  );
  
  var $status_ar = array(
    0 => "Черновик", 
    1 => "Включен",
    2 => "Выключен"
    
  );
  
  var $per_page = 30;
  
  var $filters = array(
  'raw' => "обычный HTML",
  'nl2br' => "автоперенос на новую строку",
  );  
    
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['infoblocks'];  
  $sv->load_model('page');
   
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название на русском',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 0
  ));    
    
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Идентификатор (англ. без пробелов)',
  'type' => 'varchar',  
  'size' => '255',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create'),
  'unique' => 1
  ));    
    
  $this->init_field(array(
  'name' => 'layouts',
  'title' => 'Используется в шаблонах',
  'type' => 'varchar',
  'input' => 'multiselect',
  'belongs_to' => array('list' => $sv->m['page']->layouts),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
   
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',
  'default' => 1,
  'input' => 'select',
  'show_in' => array(),
  'write_in' => array('edit'),
  'belongs_to' => array('list' => $this->status_ar, 'not_null'=>1)
  ));   
    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус',
  'show_in' => array('default'),
  'virtual' => 'status_id'
  ));      
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'HTML код',
  'type' => 'text',
  'len' => '80',
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'id' => 'full-text'
  ));  
   
}

// validations

function v_title($val) {  
  $val = trim($val);
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указано название";
  }
   
  return $val;
}

function v_slug($val) {
  
  $val = preg_replace("#[^a-z0-9\_\-]#msi", "", $val);  
  $val = trim($val);
  
  if ($val=='') {
    $this->v_err = true;
    $this->v_errm[] = "Не указан идентификатор (разрешены только латинские символы и цифры)";
  }
   
  return $val;
}

function v_layouts($val) {

  $ret = implode(",", $val);
  return $ret;
}

//parsers
function parse($d) {
  
  
  return $d;
}


// controllers
function c_edit() {
  global $sv, $std, $db;
  
  $d = $this->get_current_record();
  $s = $this->init_submit(); 
  
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {      
      header("Location: ".su($sv->act).$this->slave_url_addon);  exit();
    }
    $d = $this->get_current_record();     
  }
  
  $ret['s'] = $s;  
  $this->table_compact = 1;
  $this->table_width = "100%";
  $ret['form'] = $this->compile_edit_table($d);
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object($this->name, $d['id'], $sv->user['session']['account_id']);
  $sv->parsed['admin_sidebar'] = "
    <div style='margin-top: 700px; border: 1px solid #dddddd;'>
      <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
      {$ret['attach']['form']}
    </div>";
  
// markitup
  $std->markitup->use_emoticons = 0;
  $std->markitup->width = '100%';
  $ret['markitup'] = $std->markitup->js("#full-text", "html");
    
  return $ret;
}

//stuff
/**
 * Чтение контент блока (вызывается из class_view->replace_infoblocks)
 *
 * @param unknown_type $slug
 * @param unknown_type $vars
 * @return unknown
 */
function read_content($slug, $vars = array()) {
  global $sv, $std, $db;
  
  $d = $this->get_item_wh("`slug`='".$db->esc($slug)."'", 1);
  if (!$d) {
    $eslug = htmlentities($slug);
    $ret = "
    <div style='margin: 10px 0; padding: 10px 5px; border: 1px solid gray; background-color: #efefef;text-align:center;'>
    Инфоблок не найден <div style='text-align:center; margin-top: 5px;'><b>{$eslug}</b></div>
    </div>
    ";
  }
  elseif ($d['status_id']<>1) {
    $ret = "";
  }
  else {
    $ret = $d['text'];
  }
  
  return $ret;
}
/**
 * Получение списка связанных блоков
 *
 * @param unknown_type $layout_id
 * @return unknown
 */
function related_blocks($layout_id = '') {
  global $sv, $std, $db; $ret = "";
  
  if ($layout_id=='') {
    return $ret;
  }
  
  $ar = array();
  $tr = array();
  $db->q("SELECT id, title, slug, status_id FROM {$this->t} WHERE `layouts` LIKE \"%".$db->esc($layout_id)."%\"", __FILE__, __LINE__);
  while($d = $db->f()){
    $ar[] = $d;
    $color = ($d['status_id']<>1) ? "color:red;" : "";
    $tr[] = "
    <tr><tD class='admin_menu'>
      <a href='".u('infoblocks', 'edit', $d['id'])."' title='{$d['slug']}' target=_blank style='{$color}'>{$d['title']}</a>
    </td></tr>";
  }
  
  $ret = (count($tr)>0) ? "<table width='100%' cellpadding=10 cellspacing=5>".implode("\n", $tr)."</table>" : "";
  
  return $ret;
}

//eoc
}

?>