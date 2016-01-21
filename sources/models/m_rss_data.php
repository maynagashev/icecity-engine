<?php

/**
 * Модель нововстей с RSS каналов с публичными контролеррами для вывода ленты новостей из них
 *
 */
class m_rss_data extends class_model {
  
  var $tables = array(
    'rss_data' => "
      `id` bigint(20) NOT NULL auto_increment,
      `guid` varchar(255) NOT NULL default '',
      `title` varchar(255) NOT NULL default '',
      `link` varchar(255) NOT NULL default '',
      `description` text,
      `author` varchar(255) NOT NULL default '',
      `category` varchar(255) NOT NULL default '',
      `comments` varchar(255) NOT NULL default '',
      `enclosure` text,
      `pubdate` varchar(255) default NULL,
      `source` varchar(255) default NULL,
      `date` datetime default NULL,
      `active` tinyint(3) NOT NULL default '1',
      
      `site_id` varchar(255) null,
      `cat` varchar(255) null,
      `fulltext` text not null default '',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`date`),
      KEY (`cat`),
      KEY (`site_id`, `guid`)    
    "
  );
  
  
  
  var $cats = array(
    'auto' => 'Автоновости',
    'politics' => 'Политика',
    'computers' => 'Hi-Tech',
    'sport' => 'Спорт',
    'culture' => 'Культура',
    'celebrity' => 'Звезды'
    
  );
  
  // cache var название записи в кэше
  var $cvar_months = "rssdata_months";
  var $cache_interval_h = 0.5;

  /**
   * путь подстановки
   *
   * @var unknown_type
   */
  var $news_url = "/news/";
  var $per_page = 20;
   

function m_rss_data() {
  return $this->__construct();
}
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['rss_data'];
  
   
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',  
  'setcurrent' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  

  
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',
  'size' => '255',
  'len' => '80',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'link',
  'title' => 'Ссылка',
  'type' => 'varchar',
  'size' => '255',
  'len' => '70',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  )); 
   
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Краткое описание',
  'type' => 'text',
  'size' => '50000',
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'pubdate',
  'title' => 'pubDate',
  'type' => 'varchar',
  'size' => '255',
  'len' => '30',
  'show_in' => array(),
  'write_in' => array('edit')
  ));  
  

   
  
  $this->init_field(array(
  'name' => 'author',
  'title' => 'Автор',
  'type' => 'varchar',
  'size' => '255',
  'len' => '30',
  'show_in' => array('remove'),
  'write_in' => array()
  ));
     
 
  $this->init_field(array(
  'name' => 'active',
  'title' => 'Активный',
  'type' => 'boolean',
  'size' => '3',
  'len' => '3',
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

    
  $this->init_field(array(
  'name' => 'guid',
  'title' => 'Уникальный идентификатор',
  'type' => 'varchar',
  'size' => '255',
  'len' => '80',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
 
  ));    
  
  $this->init_field(array(
  'name' => 'source',
  'title' => 'Источник RSS',
  'type' => 'varchar',
  'size' => '255',
  'len' => '80',
  'default' => '',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
 
  ));      
  
  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Категория',
  'type' => 'varchar',
  'input' => 'select',
  'len' => '30',
  'show_in' => array('remove'),
  'write_in' => array('edit', 'create'),
  'belongs_to' => array('list' => $this->cats)
  ));
       

  
  
}

function df_title($v) {
  return "<div style='text-align:left;'>{$v}</div>";
}



function parse($d) {
  global $std;
  
  $d['title'] = preg_replace("#\.$#msi", "", $d['title']);
  $d['time'] = strtotime($d['date']);
  $d['f_time'] = $std->time->format($d['time'], 3);
  $d['f_date'] = $std->time->format($d['date'], 0.9, 1); 
  $d['f_date2'] = $std->time->format($d['date'], 0.6, 1); 
  
  $d['url'] = $this->news_url."item/?id={$d['id']}";
  
  $d['description'] = str_replace("НОРИЛЬСК. \"Таймырский Телеграф\" – ", "", $d['description']);
  $d['description'] = str_replace("НОРИЛЬСК. Таймырский Телеграф\" – ", "", $d['description']);
  
  $d['ann'] = $d['description'];
  
  return $d;  
}

// stuff
function is_exists($site_id, $guid) {
  global $sv, $db;
  
  $site_id = $db->esc($site_id);
  $guid = $db->esc($guid);
  $db->q("SELECT * FROM {$this->t} WHERE site_id='{$site_id}' AND guid='{$guid}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $this->d = $db->f();
    $this->current_record = $this->d['id'];
    return true;
  }
  else {
    return false;
  }
}


function get_last($lim=10) {
  global $db, $std;
  $lim = intval($lim);
  
  $ar = array();
  $db->q("SELECT * FROM {$this->t} ORDER BY `date` DESC LIMIT 0,{$lim}", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $d = $this->parse($d); 
    $d['f_time'] = date("H:i", $d['time']);
    $d['p_text'] = $std->text->truncate($d['description'], 200, "&#8230;");
    $ar[] = $d;
  }
  return $ar;
}

// PUBLIC CONTROLLERS
/**
 * Обычный список по порядку
 *
 * @return unknown
 */
function c_public_list() {
  global $sv, $std, $db;
  
  $ret = $this->item_list_pls("`active`='1'", "`date` DESC", $this->per_page, 1, 0, $sv->view->safe_url."?page=");
  
  $ret['months'] = $this->month_list(0);
  
  return $ret;
}

/** 
 * Подробный вид c навигацией - вперед, назад
 *
 * @return unknown
 */
function c_public_item() {
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  
  $sv->vars['p_title'] = $d['title'];
  $ret['d'] = $d;
  
  $ret['next'] = $this->get_item_wh("`date`>'{$d['date']}' ORDER BY `date` ASC", 1);
  $ret['prev'] = $this->get_item_wh("`date`<'{$d['date']}' ORDER BY `date` DESC", 1);
  $ret['months'] = $this->month_list(0);
  
  return $ret;
}

/**
 * Выборка за месяц (не тестировалось)
 *
 * @return unknown
 */
function c_public_month() {
  global $sv, $std, $db;
  
  $year = (isset($sv->_get['year'])) ? intval($sv->_get['year']) : 0;
  $month = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
     
  if ($month<1 || $month>12 || $year<1950 || $year>2100) {
    $sv->view->show_err_page("badrequest");
  }
 
  $ret = $this->item_list_pls( "year(`date`)='{$year}' AND month(`date`)='{$month}'", 
         "`date` asc", $this->per_page, 1, 0, $sv->view->safe_url."?id={$month}&year={$year}&page=");
         
  $ret['months'] = $this->month_list(0);
  $ret['month'] = $month;
  $ret['year'] = $year;       
  
  
  return $ret;
}

// MONTHS CACHE 
/**
 * инициализация текущего списка месяцев
 * получение из кэша, если устарел то обновление
 *
 * @param unknown_type $force
 * @return unknown
 */
function month_list($force=0) {
  global $sv, $std, $db;
    
  $sv->load_model('cache');
  $ar = $sv->m['cache']->read($this->cvar_months, 1, 1, array());
  
  $c = $sv->m['cache']->d;  
  $exp = $sv->post_time - 60*60*$this->cache_interval_h; // 1 day
  
  // if expired update
  if ($c['time']<$exp || $force) {
    $ar = $this->parse_month_list();
    $sv->m['cache']->write($this->cvar_months, $ar, 1);
  }
        
  return $ar;
}

/**
 * генерация списка месяцев на основе списка новостей в базе
 *
 * @return unknown
 */
function parse_month_list() {
  global $sv, $std, $db; $ret = array();
  
  $ar = array();
  $db->q("SELECT year(`date`) as year, month(`date`) as month FROM {$this->t} ORDER by date asc", __FILE__, __LINE__);
  while ($d = $db->f()) {
    if (isset($ar[$d['year']][$d['month']])) {
      $ar[$d['year']][$d['month']]['count']++;
    }
    else {
      $p = array(
        'title' => $std->time->monthtorus($d['month']),
        'count' => 1        
      );
      $ar[$d['year']][$d['month']] = $p;
    }
  }
  
  return $ar;
}

// PUBLIC ADDON @deprecated
function public_list($cat) {
  global $sv, $std, $db;
  
  $ret = "list {$cat}";
  $cat = addslashes($cat);
  $p = (isset($sv->_get['page'])) ? intval($sv->_get['page']) : 1;
  
  $ar = $this->item_list_pl("`cat`='{$cat}' AND `active`='1'", "`date` DESC", 10, $p, 1, $sv->vars['p_addr']."&page=");
 
  $tr = array();
  foreach($ar['list'] as $d) {
    $d = $this->public_parse($d);
    //http://news.yandex.ru/yandsearch?cl4url=www.regnum.ru/news/991780.html&country=Russia
    
    $url = $d['link'];
    $url = str_replace("http://news.yandex.ru/yandsearch?cl4url=", "", $url);
    $url = str_replace("&country=Russia", "", $url);
    
    $url = preg_replace("#^(.{30}).+(.{20})$#si", "\\1...\\2", $url);
    
$tr[] = 
<<<EOD


  <tr><td><b class=greentext>{$d['title']}</b></td></tr>
  <tr><td style='padding: 0 0 5px 1px;'><small>Дата публикации: <span>{$d['f_time']}</span></td></tr>
  <tr><td style='padding: 0 0 15px 20px;'>{$d['description']}
  <div style='margin: 10px 0; color: gray;font-size: 90%;'>Источник: <a href='{$d['link']}' target=_blank>{$url}</a></div>
  </td></tr>
  


EOD;


  }
  
  $pagelist = $sv->html->pagelist($ar['pl']);
  $ret = "
  <style>
  div.rss-image {margin: 10px 0; padding-left: 20px;}
  </style>
  <table width='100%'>".implode("\n", $tr)."</table>{$pagelist}
  <div style='margin: 20px 0;padding: 0 5px;'>
  <span style='color: gray;'><hr noshade size='1'>Все новости импортированы из открытых источников с использованием технологии RSS.</span>
  </div>
  ";
  
  return $ret;
}

function public_parse($d)  {
  
   
  
  return $d;
}

function public_block($lim = 10) {
  global $sv, $std, $db;
  
  $ar = $this->item_list("`active`='1'", "`date` DESC", $lim, 1);
  
  $tr = array();
  foreach($ar['list'] as $d) {
    $tr[] = "
    <tr><td><ul style='padding:3px 0 3px 20px; margin:0;' ><li type=square>
    <a href='/cat/news/{$d['cat']}.html'>{$d['title']}</a>
    </li></ul></td></tr>";
  }
  
  $list = implode("\n", $tr);
  
$ret = <<<EOD

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab_infoblock">
<tr>
<td><img src="/i/pix.gif" alt="" width="10" height="12" border="0"></td>
<td class="name_infoblock" rowspan="2" nowrap><span class="header_infoblock">Новости короткой строкой
</span></td>
<td><img src="/i/pix.gif" alt="" height="12" border="0"></td>
</tr>
<tr>
<td class="top_left_angle"><img src="/i/pix.gif" alt="" width="10" height="12" border="0"></td>
<td class="top_right_angle"><img src="/i/pix.gif" alt="" height="12" border="0"></td>
</tr>
<tr>
<td class="content_infoblock" colspan="3">

<table width='100%'>{$list}</table>

</td>
</tr>
</table>




EOD;
  return $ret;
}
//eoc
}  
  
?>