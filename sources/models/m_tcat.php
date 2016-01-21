<?php
/*
модель древовоидной категории... товаров, или других объектов
*/

class m_tcat extends class_model {

  var $tables = array(
    'tcats' => "
      `id` bigint(20) NOT NULL auto_increment,
      
      `title` varchar(255) NOT NULL default '',
      `slug` varchar(255) NOT NULL default '',
      `parent_id` bigint(20) not null default '0',
      `parent_slug` varchar(255) null,
      
      `description` text null,
      `text` text null,
      `image` varchar(255) null,
      `status` int(1) not null default '1',
      
      `count` int(11) NOT NULL default '0',
      `level` int(11) not null default '0',
      
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
var $tree_mode = 1;

function __construct() {
  global $sv;  


   
  $this->t = $sv->t['tcats'];
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  
  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Строковый идентификатор',
  'type' => 'varchar',
  'len' => '25',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));  
    
  
  $this->init_field(array(
  'name' => 'parent_id',
  'title' => 'Родительский объект',
  'type' => 'int',  
  'input' => 'custom',  
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'parent_slug',
  'title' => 'Родительский адрес',
  'type' => 'varchar',  
  'input' => 'varchar',  
  'show_in' => array('edit', 'default'),
  'write_in' => array()
  ));    
   
  $this->init_field(array(
  'name' => 'description',
  'title' => 'Краткое описание',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));    
  /*
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Текст',
  'type' => 'text',  
  'len' => 50,
  'show_in' => array( 'remove'),
  'write_in' => array('create', 'edit'),
  'public_search' => 1
  ));   
    */

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

     
}

// parsers
function parse($d) {
  
  $d['url'] = "/shop/{$d['slug']}/";
  
  return $d;
}

function parse_search($d) {
  global $std;
  
  $title = $d['title'];
  $url = $d['url'];
  $desc = $d['description'];  
    
  $p = array(
    'title' => $title, 
    'description' => $desc,
    'url' => $url
  );
  return $p;
}


// validators
function v_image() {  
  return $this->ev_file(0);
}
function v_parent_id($id) {
  
  $id = intval($id);
  return $id;
}
		
function last_v($p) {
  global $sv, $std, $db;
  
	 // slug
  $id = ($this->code=='edit') ? $this->current_record : 0; 
  $p['slug'] = (isset($p['slug'])) ? $p['slug'] : '';
  if (!$std->text->is_valid_slug($p['slug'], $this->t, 'slug', $id)) {
    $p['slug'] = $std->text->gen_slug($p['title'], $this->t, 'slug');
  }
 
  return $p;
 }
 
// inputs 
function ci_parent_id($val) {
  global $sv;
  
  $tr = array();
  $tr[] = "<option value='0'>-- корневой раздел -- </option>";
  $ret = "<select name='new[parent_id]'>".implode("\n", $tr)."</select>";
  
  return $ret;
}

// callbacks
function vcb_image_view($val) {
  return $this->ev_file_view($this->current_callback, 'remove_file');
}

function df_image_view($val) {  
  $ret = ($val!='') ? "<img src='{$this->uploads_url}{$val}' border=0>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}

// pre post
function before_update() {
  global $sv, $db;    
  $this->ev_init_file_remove('image', 'remove_file');   
} 

// new controllers
function ec_default_tree() {
 global $sv, $std, $db;
  
  $s = $this->init_submit();
  $ret['s'] = $s;
    
  $ret['fields'] = $this->get_active_fields('show');
  $sort = $this->get_sort($ret['fields']);

  $this->before_default();
  
  // иницизализация поиска
  $this->init_search();
  $ret['search'] = $this->html_search();
  
  // инициализация селекторов
  $this->init_selectors();
  $ret['selectors'] = $this->html_selectors();
  
  // параметры запроса
  $j = $this->get_joins();
  $where = $this->get_where(1);

  // всего записей
  $db->q("SELECT 0 FROM {$this->t}", __FILE__, __LINE__);
  $ret['all_count'] = $db->nr();
  
  // список страниц
  $db->q("SELECT 0 FROM {$this->t} {$where}");  
  $page = (isset($sv->_get['page'])) ? $sv->_get['page'] : 1;
  $ret['pl'] = $pl = $std->pl($db->nr(), $this->per_page, $page, u($sv->act, $sv->code, $sv->id).$this->slave_url_addon."&page=");
  
  // выборка списка
  $ar = array(); $i = $pl['k'];
  $q = "  SELECT {$this->t}.*{$j['f']} 
          FROM {$this->t} 
          {$j['j']}
          {$where}
          ORDER BY {$this->t}.{$sort} {$pl['ql']}";  
  $res = $db->q($q, __FILE__, __LINE__);
  while ($d = $db->f($res)) { $i++;    
    $d = $this->e_parse($d);
    $this->d = $d;
    $d['i'] = $i;
    $d = $this->get_and_parse_virtual_fields($d);
    $d = $this->default_view_keys($d);
    $ar[] = $d;
  }

  $ret['url'] = u($sv->act, $sv->code, $sv->id).$this->slave_url_addon."&page=".$pl['page'];
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  return $ret;
}

//eoc
}

?>