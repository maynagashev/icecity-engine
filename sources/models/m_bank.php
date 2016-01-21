<?php


class m_bank extends class_model {
  


  var $tables = array(
    'banks' => "
`bid` int(10) NOT NULL auto_increment COMMENT 'Уникальный идентификатор банка',
`rbid` int(11) NOT NULL COMMENT 'Уникальный идентификатор ревиизии данных банка',
`uid` int(10) NOT NULL COMMENT 'Уникальный идентификатор учетной пользователя для редактиравания информации о банке',
`name` char(255) NOT NULL COMMENT 'Название банка',
`url` char(255) NOT NULL COMMENT 'ЧПУ банка',
`logo` char(32) NOT NULL COMMENT 'Название файла с логотипом банка',
`name_full` char(255) NOT NULL COMMENT 'Полное название',
`licence` int(10) NOT NULL COMMENT 'Номер лицензии',
`link_cbr` int(10) NOT NULL COMMENT 'Информация на сайте ЦБ РФ (ссылка)',
`views` int(10) NOT NULL COMMENT 'Количество просмотреных данных банка',
`status` tinyint(4) NOT NULL COMMENT 'Статус банка (опубликовано/не опубликовано)',
`time` int(11) NOT NULL COMMENT 'Время добавления банка',
`widget` tinyint(1) not null default '0',
`created_at` datetime default NULL,
`created_by` int(11) NOT NULL default '0',
`updated_at` datetime default NULL,
`updated_by` int(11) NOT NULL default '0',
`expires_at` datetime default NULL,
`t_deposits0` tinyint(1) not null default '0',
`t_deposits1` tinyint(1) not null default '0',
`t_credits` tinyint(1) not null default '0',
`t_bankomats` tinyint(1) not null default '0',
`t_branches` tinyint(1) not null default '0',
`t_exchanges` tinyint(1) not null default '0',
`map` varchar(255) null,

PRIMARY KEY  (`bid`),
KEY `url` USING BTREE (`bid`,`url`),
KEY (`widget`)
    "
  );
    

  var $uploads_make_resize = 0;
  var $uploads_w = 188;
  var $uploads_h = 85;
  var $uploads_resize_type = "fixed";  // fixed | by_width | by_height
  
  var $uploads_dir = "uploads/banks/";
  var $uploads_url = "uploads/banks/";
  var $ext_ar = array('gif', 'png', 'jpg');
  var $primary_field = "bid";
  
  var $open_url = "/banks/";
  var $open_page = 7;
  var $c_url = '';
  
  var $rko_code = '';
  var $rko_field = '';
  var $rko_titles = array(
   'rko' => 'РКО',
   'metals' => 'Операции с драг. металлами',
   'safe' => 'Аренда банковских сейфов',
   'transfers' => 'Денежные переводы'
  );
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['banks'];
  
  $this->uploads_dir = PUBLIC_DIR.$this->uploads_dir;
  $this->uploads_url = PUBLIC_URL.$this->uploads_url;
  
  $this->init_field(array(
  'name' => 'name',
  'title' => 'Название банка',
  'type' => 'varchar',  
 'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1,
  'not_null' => 1,
  'public_search' => 1
  ));    
  
 $this->init_field(array(
  'name' => 'url',
  'title' => 'ЧПУ банка',
  'type' => 'varchar',  
  'len' => '30',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1,
  'not_null' => 1
  ));      
  
  
 $this->init_field(array(
  'name' => 'logo',
  'title' => 'Загрузка лого 188х85',
  'type' => 'varchar',  
  'input' => 'file',
  'len' => '30',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));   
    

  $this->init_field(array(
  'name' => 'logo_view',
  'title' => 'Лого',
  'virtual' => 'logo',
  'show_in' => array('edit'),
  'write_in' => array()
  ));  
    
  $this->init_field(array(
  'name' => 'name_full',
  'title' => 'Полное название',
  'type' => 'varchar',  
 'len' => '70',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
  
  $this->init_field(array(
  'name' => 'licence',
  'title' => 'Номер лицензии',
  'type' => 'int',  
  'len' => '20',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));       
  
  $this->init_field(array(
  'name' => 'link_cbr',
  'title' => 'Информация на сайте ЦБ РФ (ссылка)',
  'type' => 'int',  
  'len' => '20',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));       
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотры',
  'type' => 'int',  
  'len' => '20',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));       
  
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Статус банка (опубликовано?)',
  'type' => 'boolean',    
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));     
  
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Время добавления банка',
  'type' => 'int',  
  'input' => 'time',
  'len' => '20',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));      


  $this->init_field(array(
  'name' => 'bid',
  'title' => 'BID',
  'type' => 'varchar',  
  'len' => '70',
  'show_in' => array('default', 'edit', 'remove'),
  'write_in' => array(),
  'unique' => 1
  ));      
    
  
  $this->init_field(array(
  'name' => 'rbid_view',  
  'title' => 'Используемая ревизия',
  'virtual' => 'rbid',  
  'show_in' => array('edit'),  
  ));    
    
  $this->init_field(array(
  'name' => 'rbid',  
  'title' => 'Ревизия',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'bank_revision', 'field' => 'rbid', 'return' => 'rbid', 'where' => array(array('remote' => 'bid', 'operator'=> '=', 'local' => 'bid'))),
  'show_in' => array('remove'),
  'write_in' => array('edit'),
  'selector' => 0
  
  ));    
  
  


   
  $this->init_field(array(
  'name' => 'bid_view',
  'title' => 'Ревизии и вклады',
  'virtual' => 'bid',  
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));    
      
  $this->init_field(array(
  'name' => 'widget',
  'title' => 'Показывать в виджете на главной?',
  'type' => 'boolean',    
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));     

  
 $this->init_field(array(
  'name' => 'map',
  'title' => 'Загрузка схемы проезда',
  'type' => 'varchar',  
  'input' => 'file',
  'len' => '30',
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit')
  ));   
    

  $this->init_field(array(
  'name' => 'map_view',
  'title' => 'Карта проезда',
  'virtual' => 'map',
  'show_in' => array('edit'),
  'write_in' => array()
  ));  
    
     
}

function parse($d) {
  global $std;
  
  $d['v_name'] = $std->text->cut($d['name'], 'cut', 'cut');
  $d['open_url'] = "/banks/{$d['url']}/";
  $d['url_logo'] = ($d['logo']!='') ? $this->uploads_url.$d['logo'] : '';
  $d['url_map'] = ($d['map']!='') ? $this->uploads_url.$d['map'] : '';
  $d['img_logo'] = ($d['logo']!='') ? "<img src='{$d['url_logo']}' border='0' alt='{$d['v_name']}' title='{$d['v_name']}' width='188' height='85'>" : '';
  
  return $d;
}

function parse_search($d) {
  global $std;
  
  $title = $d['name'];
  $desc = $d['name_full'];
  $url = "/banks/{$d['url']}/";
  
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}

// CALLBACKS
function vcb_bid_view($did) {
  global $sv;
  
  //ревизии
  $bid = intval($this->d['bid']);
  $sv->load_model('bankr');
  $ar = $sv->m['bankr']->item_list("`bid`='{$did}'", "`rbid` desc", 0, 1);
  
  $tr = array();
  foreach ($ar['list'] as $d) {
    $tr[] = "<tr bgcolor=white align=right>
    
    <td>{$d['rbid']}</td>
    <td>{$d['f_time']}</td>
    <td><a href='/admin/index.php?bankredit_edit={$d['id']}&query='>редактировать</a></td></tr>";
  }
  
  if (count($tr)<=0) {
    $tr[] = "<tr bgcolor=white><td colspan=3 align=center>список пуст</td></tr>";
  }  
  $ret = "<table cellpadding=5 cellspacing=1 bgcolor='#dddddd'>
  <tr bgcolor='#efefef' alig=center><td>Ревизия №</td><td>Дата</td><td></td></tr>
  ".implode("\n", $tr)."</table>";
  
  
  
  // вклады
  $did = intval($did);
  $sv->load_model('deposit');
  $ar = $sv->m['deposit']->item_list("`bid`='{$did}'", "`name` asc", 0, 1);
  
  $tr = array();
  foreach ($ar['list'] as $d) {
    $tr[] = "<tr bgcolor=white align=right>
    
    <td>{$d['name']}</td>
    <td>{$d['ftype']}</td>
    <td><a href='/admin/index.php?depositsedit_edit={$d['id']}&query='>редактировать</a></td></tr>";
  }
  
  if (count($tr)<=0) {
    $tr[] = "<tr bgcolor=white><td colspan=3 align=center>список пуст</td></tr>";
  }  
  $ret .= "<table cellpadding=5 cellspacing=1 bgcolor='#dddddd'>
  <tr bgcolor='#efefef' alig=center><td>Название вклада</td><td>Тип</td><td></td></tr>
  ".implode("\n", $tr)."</table>";
  
  
  return $ret;
}

function vcb_logo_view($val) {
  return $this->ev_file_view($this->current_callback);
}

function vcb_map_view($val) {
  return $this->ev_file_view($this->current_callback, 'remove_file2', 0);
}

// VALIDATIONS
function v_logo() {  
  return $this->ev_file(0);
}
function v_map() {  
  return $this->ev_file(0);
}


function last_v($p) {
  global $sv;
  if ($this->code=='create') {
    $p['time'] = $sv->post_time;
  }
  return $p;
}

// PRE post actions
function before_default() {
  $this->sync_urls();
}

function before_update() {
  global $sv, $db;  
  
  $this->ev_init_file_remove('logo');   
  $this->ev_init_file_remove('map', 'remove_file2');  
}

function after_update($d, $p, $err) {
  
  if (!$err)  {
    $p['id'] = $d['id'];
    $this->sync_url($d['id'], $p);
  }
}

// CONTROLLERS 
function c_public_details() {
  global $sv, $std, $db;
  
  $d = $this->get_item($sv->view->d['object'], 1);
  $ret['d'] = $d;
  $sv->vars['p_title'] = $d['name'];
  $sv->vars['breadcrumbs'][] = array('title' => $d['name']);  
  $this->update_row(array('views'=>$d['views']+1), $d['id'], 0);
  
  $sv->load_model('bankr');
  $r = $sv->m['bankr']->get_item($d['rbid'], 1);
  $ret['r'] = $r;

  // news
  
  $sv->load_model('article');
  $ret['news'] = $sv->m['article']->item_list("`bid`='{$d['bid']}' AND `cat_id`='banks'", "`time` DESC", 3, 1);
  $ret['news']['all_count'] = $sv->m['article']->select_count_wh("`bid`='{$d['bid']}' AND `cat_id`='banks'"); 

  
  $sv->load_model('curs');
  $ret['curs'] = $sv->m['curs']->get_item_wh("`bid`='{$d['bid']}' AND `active`='1'", 1);
  
  return $ret;
}

function c_public_list() {
  global $sv, $db;
  
  $sv->load_model('bankr');
  
  $ar = array();
  $db->q("SELECT b.*, r.* FROM {$this->t} b 
  LEFT JOIN {$sv->m['bankr']->t} r ON (b.rbid=r.rbid) 
  WHERE b.status='1'
  ORDER BY `name` ASC", __FILE__, __LINE__);
  while($d = $db->f()) {
    $d = $this->parse($d);
    $d = $sv->m['bankr']->parse($d);
    $ar[] = $d;
  }

  $ret['list'] = $ar;
  
  return $ret;
}

function c_public_rko_list() {
  global $sv, $std, $db;
  
  $this->joins = array(
    'f' => ', r.*',  
    'j' => "INNER JOIN {$sv->t['bank_revision']} r ON ({$this->t}.rbid=r.rbid)"
  );
  $ret = $this->item_list("r.{$this->rko_field}<>''", "{$this->t}.name ASC", 0, 1);
 
  $this->joins = array('f' => '', 'j' => "");
  
  return $ret;
}

function c_public_rko_details() {
  global $sv;
  
  $bank = $this->init_bank_url(1);
  if (!$bank) {
    $sv->view->show_err_page("Банк не найден.");
  }
  $ret['d'] = $bank;
  
  $sv->vars['breadcrumbs'][] = array('title' => $bank['name'], 'url' => $bank['open_url']);  
  $rko_title = $this->rko_titles[$this->rko_code];
  $sv->vars['p_title'] = "{$rko_title} в банке &laquo;{$bank['name']}&raquo;";
  
  $sv->load_model('bankr');
  $ret['r'] = $sv->m['bankr']->get_item($bank['rbid'], 1);
  
  return $ret;
}
// STD
function init_bank_url($parse_bank = 0) {
  global $sv,$db;
  
  $url = (isset($sv->_get['bank'])) ? preg_replace("#[^a-z0-9\_\-]#si", "", $sv->_get['bank']) : '';
  $this->c_url = $url;
  
  if ($parse_bank) {
    $ret = $this->get_item_wh("`url`='".$db->esc($url)."'", 1);
  }
  else {
    $ret = $url;
  }
  return $ret;
}

function sync_urls() {
  $ar = $this->item_list("`url`<>''", "", 0, 0);
  foreach($ar['list'] as $d) {
    $this->sync_url($d['id'], $d);
  }
}

function sync_url($id, $d=false) {
  global $sv, $db;
  
  
  if (!$d) {
    $d = $this->get_item($id);
  }
  if (!$d) {
    return false;
  }
  
  $sv->load_model('url');
  $url = $this->open_url.$d['url']."";      
  $sv->m['url']->sync_url($url, array('module' => $this->name, 'object'=>$d['id'], 'page' => $this->open_page, 'title' => $d['name'], 'primary' => 0));
  
  
  return true;
}

function cron_hourly() {
  global $sv;
  
  $ar = $this->item_list("", "", 0, 0);
  $sv->load_model('deposit');
  $sv->load_model('credit');
  $sv->load_model('creditkind');
  
  $sv->load_model('branch');
  $sv->load_model('bankomat');
  
  foreach($ar['list'] as $d) {
    $bid = $d['bid'];
    $p = array();
    $p['t_deposits0'] = $sv->m['deposit']->select_count_wh("`bid`='{$bid}' AND dtype='0'");
    $p['t_deposits1'] = $sv->m['deposit']->select_count_wh("`bid`='{$bid}' AND dtype='1'");
    $p['t_credits0'] = $sv->m['creditkind']->select_count_wh("`t_bid`='{$bid}' AND t_dtype='0'");
    $p['t_credits1'] = $sv->m['creditkind']->select_count_wh("`t_bid`='{$bid}' AND t_dtype='1'");
    $p['t_branches'] = $sv->m['branch']->select_count_wh("`bid`='{$bid}'");
    $p['t_exchanges'] = $sv->m['branch']->select_count_wh("`bid`='{$bid}' AND `exchange`='1'");
    $p['t_bankomats'] = $sv->m['bankomat']->select_count_wh("`bid`='{$bid}'");
    $this->update_row($p, $bid, 0);
  }
  
  /*
`t_deposits` tinyint(1) not null default '0',
`t_credits` tinyint(1) not null default '0',
`t_bankomats` tinyint(1) not null default '0',
`t_branches` tinyint(1) not null default '0',
`t_exchanges` tinyint(1) not null default '0',

  */
}

//eoc
}

?>