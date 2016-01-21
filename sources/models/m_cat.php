<?php
/*
модель обычной категории... товаров, или других объектов
*/

class m_cat extends class_model {

  var $tables = array(
    'cats' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `description` text null,
      `note` text null,
      `slug` varchar(255) NOT NULL default '',
      `status` int(1) not null default '1',
      `count` int(11) NOT NULL default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `slug` (`slug`)    
        "
  );

var $c_cat = false;  
  
function __construct() {
  global $sv;  


   
  $this->t = $sv->t['cats'];
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название категории',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Описание категории',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
    
  $this->init_field(array(
  'name' => 'note',
  'title' => 'Служебная информация',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 0
  ));   
    
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Адрес категории (англ)',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
 $this->init_field(array(
  'name' => 'count',
  'title' => 'Количество записей в категории',
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    
  $this->init_field(array(
  'name' => 'status',
  'title' => 'Включена?',
  'type' => 'boolean',  
  'default' => 1,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  

  if (isset($sv->modules) && in_array($sv->modules->current, array('cats', 'subcats', 'products', 'orders', 'basketedit'))) {
    $this->init_admin_menus();
  }
     
}

function init_admin_menus() {
  global $sv, $std, $db;
  
  // проверяем куки, если есть команда сменить - меняем!
  if (isset($sv->_get['set_cat_id'])) {
    $sv->_c['cat_id'] = intval($sv->_get['set_cat_id']);
    setcookie('cat_id', $sv->_c['cat_id'], $sv->post_time+60*60*24*365);
  }
  $c_cat_id = (isset($sv->_c['cat_id'])) ? intval($sv->_c['cat_id']) : 0;
  
  // считываем и запоминаем текущую категорию
  $this->c_cat = $this->get_item($c_cat_id, 1);
   
  
  $ar = $this->item_list("`status`='1'", "`title` ASC", 0, 1);
  
  // формируем доп пункты в меню, выделяем текущий если есть
  foreach($ar['list'] as $d) {    
    //$sv->parsed['admin_menu_shortcuts'][$d['title']] = u('products')."&set_cat_id={$d['id']}";
    $selected = ($sv->act=='products' && $d['id']==$c_cat_id)  ? 1 : 0;
    $sv->admin_menu_virtuals['shop'][] = array('module' => 'products', 'title' => $d['title'], 'url' => u('products')."&set_cat_id={$d['id']}", 'selected' => $selected);
  }
  
}

function parse($d) {
  
  $d['url'] = "/shop/{$d['slug']}/";
  
  return $d;
}

function catlist_main() {
  global $sv;
  
  $cats = $this->item_list("`status`='1'", "title asc", 0, 1);
  
  $sv->load_model('subcat');
  $subs = $sv->m['subcat']->item_list("`status`='1'", "title asc", 0, 1);
  $subs_by_cat = array();
  foreach ($subs['list'] as $sub) {
  	$subs_by_cat[$sub['cat_id']][] = $sub;
  }
  
  $tr = array();
  foreach($cats['list'] as $cat) {
    $tr[] = "<h2><a href='{$cat['url']}'>{$cat['title']}</a></h2>";
    if (!isset($subs_by_cat[$cat['id']])) continue;
    $subtr = array();
    foreach($subs_by_cat[$cat['id']] as $sub) {
      $c = ($sub['count']>0) ? " ({$sub['count']})" : '';
      $subtr[] = "<li><a href='{$sub['url']}'>{$sub['title']}</a>{$c}</li>";
    }
    $tr[] = "<ul>".implode($subtr)."</ul>";
  }
  $ret = implode("", $tr);
  
/*
 	<h2>Садовая мебель</h2>
        <ul>
        	<li><a href="#">Садовые качели</a></li>        	
        	<li class="last"><a href="#">Чехлы укрытия для садовой</a></li>
        </ul>
    	<h2>Электроинструмент</h2>
        <ul>       
        	<li class="last"><a href="#">Аккумуляторы</a></li>
        </ul>
    	<h2>Садовая мебель</h2>
        <ul>
        	<li><a href="#">Складные столы</a></li>
        	<li><a href="#">Текстильные изделия</a></li>
        	<li class="last"><a href="#">Чехлы укрытия</a></li>
        </ul>
*/
  return $ret;
}
//eoc
}

?>