<?php


class m_ventfile extends class_model {

  var $tables = array(
    'ventfiles' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `ventcat_id` int(11) not null default '0',
      `file` varchar(255) NOT NULL default '',
      
      `tcat_id` varchar(255) not null default '',
      `vent_id` varchar(255) not null default '',
      
      `filesize` int(11) not null default '0',
      `downloads` int(11) not null default '0',
      
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY (`tcat_id`),
      KEY (`vent_id`)
        "
  );
  
  var $ext_ar = array('rar', 'zip', 'pdf', 'doc', 'xls', 'jpg', 'gif', 'png');  
  var $uploads_dir = "uploads/ventfiles/";
  var $uploads_url = "uploads/ventfiles/";
    
  var $uploads_make_resize = 0;
  var $uploads_w = 200;
  var $uploads_h = 150;
  var $uploads_resize_type = "fixed";  // fixed | by_width | by_height

  var $last_filesize = 0;

   
function __construct() {
  global $sv;  
   
  $this->uploads_dir = PUBLIC_DIR.$this->uploads_dir;
  $this->uploads_url = PUBLIC_URL.$this->uploads_url;
    
  $this->t = $sv->t['ventfiles'];
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название файла',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
  
 $this->init_field(array(
  'name' => 'file',
  'title' => 'Загрузка файла',
  'type' => 'file',  
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));    
  

  $this->init_field(array(
  'name' => 'file_view',
  'title' => 'Файл',
  'virtual' => 'file',
  'show_in' => array('default','edit'),
  'write_in' => array()
  ));  
    
  $this->init_field(array(
  'name' => 'file_name',
  'title' => 'Файл закачанный по FTP',
  'description' => 'принудительная установка имени файла',
  'virtual' => 'file',
  'show_in' => array('default'),
  'write_in' => array('edit', 'create'),
  'type' => 'varchar'
  ));  
    
 $this->init_field(array(
  'name' => 'ventcat_id',
  'title' => 'Раздел',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'ventfilescats', 'field' => 'id', 'return' => 'title'),
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));      
  
  $this->init_field(array(
  'name' => 'ventcat',
  'title' => 'Раздел',
  'virtual' => 'ventcat_id',
  'show_in' => array('default'),
  'write_in' => array()
  ));  
      
  
  
  
 $this->init_field(array(
  'name' => 'tcat_id',
  'title' => 'Категория',
  'type' => 'varchar',  
  'input' => 'custom',
  'belongs_to' => array('table' => 'tcats', 'field' => 'id', 'return' => 'title', 'null' => 1),
  'show_in' => array( 'remove'),
  'write_in' => array('edit', 'create')
  ));    

  $this->init_field(array(
  'name' => 'tcat',
  'title' => 'Категория',
  'virtual' => 'tcat_id',
  'show_in' => array('default'),
  'write_in' => array()
  ));  
        
  
 $this->init_field(array(
  'name' => 'vent_id',
  'title' => 'Конкретная модель',
  'type' => 'varchar',  
  'input' => 'multiselect',
  'belongs_to' => array('table' => 'vent', 'field' => 'id', 'return' => 'title', 'null' => 0),
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create')
  ));      
  
  $this->init_field(array(
  'name' => 'vent',
  'title' => 'Модель',
  'virtual' => 'vent_id',
  'show_in' => array('default'),
  'write_in' => array()
  ));  
        
  
  $this->init_field(array(
  'name' => 'filesize',
  'title' => 'Размер',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('default','remove'),
  'write_in' => array( 'edit')
  ));   
  
  $this->init_field(array(
  'name' => 'downloads',
  'title' => 'Скачивания',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));     
    
}
// PARSERS
function parse($d) {
  global $std, $sv;
  if ($d['file']!='') {
    $d['open_url'] = $this->uploads_url.$d['file'];
  }
  else {
    $d['open_url'] = '';
  }
  
  $d['f_size'] = $std->act_size($d['filesize']);
  $d['url_download'] = "/download/?model=ventfile&id={$d['id']}";
  
  $d['img_icon'] = $std->file->get_img_icon($d['file']);
    
  return $d;
}

// VALIDATIONS

function v_file() {  
  return $this->ev_file(0);
}
function v_tcat_id($v) {
  return (is_array($v)) ? ",".implode(",", $v)."," : $v;
}
function v_vent_id($v) {
  return (is_array($v)) ? ",".implode(",", $v)."," : $v;  
}


function last_v($p) {
  global $sv;
  
  if (isset($this->n['file_name']) && $this->n['file_name']!='') {
    $p['file'] = $this->n['file_name'];
    $path = $this->uploads_dir.$p['file'];
    if (file_exists($path)) {
      $this->last_filesize = filesize($path);
    }
    
  }
  if ($this->last_filesize>0) {
    $p['filesize'] = intval($this->last_filesize);
  }
  return $p;
}

// PRE POST ACTIONS
function before_update() {
  $this->ev_init_file_remove('file', 'remove_file');   
}
function on_upload_file($file) {
  $this->last_filesize = $file['size'];
}

// callbacks
function wcb_file_name($t) {
    
  return '';
}

function vcb_file_view($val) {
  return $this->ev_file_view($this->current_callback, 'remove_file');
}

function df_file_view($val) {  
  $ret = ($val!='') ? "<a href='{$this->uploads_url}{$val}' target=_blank>{$val}</a>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}

function df_vent($t) {
  global $sv;
  $sv->load_model('vent');
  $ar = explode(",",$this->d['vent_id']);
  $in_ar = array();
  foreach($ar as $id) {
    $id = intval($id);
    if ($id>0) {
      $in_ar[] = "'{$id}'";
    }
  }  
  $items = array();
  if (count($in_ar)>0) {
    $in = implode(", ", $in_ar);
    $l = $sv->m['vent']->item_list("`id` IN ({$in})", "`title` ASC", 0, 1);
    foreach($l['list'] as $d) {
      $items[] = $d['title'];
    }
  }  
  return implode(", ", $items);
}

function df_tcat($t) {
  global $sv;
  $sv->load_model('tcat');
  $ar = explode(",",$this->d['tcat_id']);
  $in_ar = array();
  foreach($ar as $id) {
    $id = intval($id);
    if ($id>0) {
      $in_ar[] = "'{$id}'";
    }
  }  
  $items = array();
  if (count($in_ar)>0) {
    $in = implode(", ", $in_ar);
    $l = $sv->m['tcat']->item_list("`id` IN ({$in})", "`title` ASC", 0, 1);
    foreach($l['list'] as $d) {
      $items[] = $d['title'];
    }
  }  
  return implode(", ", $items);
}


function ci_tcat_id($val) {
  global $sv;
  $sv->load_model('tcat');
  return $sv->m['tcat']->tree_remote_selector($val, 'tcat_id', 0, 1);
}

// CONTROLLERS 
function c_download() {
  global $sv;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  if (!$d || $d['file']=='') {
    $sv->view->show_err_page('notfound');
  }
  else {
    $this->update_row(array('downloads' => $d['downloads']+1), $d['id'], 0);
  }
  
  header("Location: {$d['open_url']}");
  exit();
}


// STD
/**
 * готовый бокс с кнопками - категориями и списком файлов
 *
 * @param unknown_type $tcat_id
 * @param unknown_type $vent_id
 */
function combo_list($tcat_id = 0, $vent_id = 0) {
  global $sv, $std, $db;
  
  $ret = '[ventfiles]';
  $tcat_id = intval($tcat_id);
  $vent_id = intval($vent_id);
  
  $wh = array();
  if ($tcat_id>0) {
    $wh[] = "`tcat_id` LIKE \"%,{$tcat_id},%\"";
  }
  if ($vent_id>0) {
    $wh[] = "`vent_id` LIKE \"%,{$vent_id},%\"";
  }
  $where = implode(" OR ", $wh);
  
  $this->joins = array(
    'f' => ', c.title as btn_title, c.place as btn_place',
    'j' => "LEFT JOIN {$sv->t['ventfilescats']} c ON (c.id={$this->t}.ventcat_id)"
  );
  $ar = $this->item_list($where, "{$this->t}.title ASC", 0, 1);
  
  // собираем кнопки
  $btns = array(); $by_btn = array();
  foreach ($ar['list'] as $d) {
    $btns[$d['ventcat_id']] = array(
      'id' => $d['ventcat_id'],
      'title' => $d['btn_title'],
      'place' => $d['btn_place']    
    );
    
    // и разбиваем записи по спискам
    $by_btn[$d['ventcat_id']][]  = "
    <tr>
        <td width='1%'>{$d['img_icon']}</td>
        <td><a href='{$d['url_download']}'>{$d['title']}</a></td>
        <td>{$d['f_size']}</td>
        <td><a href='{$d['url_download']}'>Скачать</a></td></tr>
    
    ";
    
  }  
  
  
  usort($btns, 'cmp_ventcat');  
  $btn_td = array();
  $i = 0;
  $active_ventcat = 0;
  foreach($btns as $btn) { $i++;
    if ($i==1) {
      $active_ventcat = $btn['id'];
      $s = " selected";
    }
    else {
      $s = '';
    }
    $btn_td[] = "<td class='btns{$s}' id='btn{$btn['id']}'><a href='#{$btn['id']}' onclick='show_ventcat({$btn['id']});'>{$btn['title']}</a></td>";
  }
  
  // собираем списки по кнопкам
  $lists = array();
  foreach($by_btn as $ventcat_id => $tr) { 
    $display = ($ventcat_id==$active_ventcat) ? "display: block;" : "display:none;";
    $lists[] = "<div class='ventcat_list_block' style='{$display}'><table width='100%'>".implode("\n", $tr)."</table></div>";
  }
    
  
  $ret = "
<script language='JavaScript'>
function show_ventcat(id) {
  $('.ventcat_list_block').toggle();
  $('.btns').removeClass('selected');
  var n = 'btn'+id;
  $('#btn'+id).addClass('selected');
}
</script>
  
<style>
  .btns td {padding: 5px 10px; font-weight: bolder; border: 1px solid #dddddd; background-color: #efefef;}
  .btns td.selected { background-color: #ffffff; border: 1px solif #dddddd; border-bottom: 0px;}
  .btns td.selected a {text-decoration: none; color: #666666;}
  .vclists td {padding: 5px;}
</style>
<div style='padding: 0px 80px 0 0;'>
  <div class='btns'>
    <table width='100%' cellpadding=5><tr>".implode("\n", $btn_td)."<td width='90%' style='border: 0px; border-bottom: 1px solid #dddddd;background-color: white;'>&nbsp;</td></tr></table>
  </div>
  <div class='vclists' style='padding: 10px;border: 1px solid #dddddd;border-top: 0;'>
    ".implode("\n", $lists)."
  </div>  
</div>
  ";
  //$ret = '';
  
  return $ret;
}
//eoc
}

function cmp_ventcat ($a, $b) {
  return $a['place'] > $b['place'] ? 1 : -1;
}

?>