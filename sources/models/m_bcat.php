<?php

class m_bcat extends class_model {

  var $title = "Категории доски объявлений";
  
  var $tables = array(
    'bcats' => "
      `id` int(11) NOT NULL auto_increment,
     
      `title` varchar(255) not null default '',
      `title2` varchar(255) not null default '',
      `parent_id` int(11) not null default '0',
      `status_id` tinyint(1) not null default '0',
      `pattern_id` varchar(255) not null default '',
      
      `show_rules` tinyint(1) not null default '0',
      `rules` text null,
      
      `use_types` tinyint(1) not null default '0',
      `t1_title` varchar(255) null,
      `t2_title` varchar(255) null,
      `t1_desc`  text null,
      `t2_desc`  text null,
      
      `adv1`  text null,
      `adv2`  text null,
      
      
      `childs` varchar(255) not null default '',
      `pchilds` varchar(255) not null default '',
      `place` int(11) not null default '0',
      `count` bigint(20) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KET (`parent_id`)
          
    "
  );

var $status_ar = array(
  0 => 'Отключена',
  1 => 'Включена'
);


var $patterns = array(
  'standart' => "Стандартный",  
  'apartment' => "Продажа жилья",
  'car' => "Автомобили",
  'photo' => "Фотографии",
  'noprice' => "Общество (без указания цены)"
);

  /**
   * перменные для построения деревеа
   *
   */
  var $tree_step = 0;
  var $tree_childs = array();
  var $tree_items = array();
  var $tree = array();
  var $tree_root = false;
  var $tree_parsed = 0;
  /**
   * Текущая цепочка элементов
   *
   * @var unknown_type
   */
  var $tree_chain = array();
    
  var $inc_count = 0;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['bcats'];
  $this->per_page = 50;    
    
  

  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    

  $this->init_field(array(
  'name' => 'title2',
  'title' => 'Полное название для поисковиков',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

  

  $this->init_field(array(
  'name' => 'parent_id',
  'title' => 'Родительская категория',
  'type' => 'int',  
  'input' => 'custom',
  'belongs_to' => array('table' => 'bcats', 'field' => 'id', 'return' => 'title'),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'selector' => 0
  ));    
  
  $this->init_field(array(
  'name' => 'parent',
  'title' => 'Родительская категория',
  'virtual' => 'parent_id',  
  'show_in' => array('remove', 'default'),
  ));    
    

   

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус категории',
  'type' => 'boolean',
  'input' => 'select',
  'default' => 1,
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

  

  $this->init_field(array(
  'name' => 'pattern_id',
  'title' => 'Используемый справочник для форм',
  'type' => 'varchar',  
  'input' => 'select',
  'belongs_to' => array('list' => $this->patterns),
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

   
  $this->init_field(array(
  'name' => 'rules',
  'title' => 'Правила',
  'type' => 'text',
  'len' => '70',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'id' => 'full-text'
  ));      

  $this->init_field(array(
  'name' => 'show_rules',
  'title' => 'Показывать правила?',
  'type' => 'boolean',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

    
  $this->init_field(array(
  'name' => 'use_types',
  'title' => 'Использовать закладки спрос/предложение?',
  'type' => 'boolean',
  'default' => 1,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    

    
  $this->init_field(array(
  'name' => 't1_title',
  'title' => 'Название для кнопки "предложение"',
  'type' => 'varchar',
  'len' => 50,
  'default' => 'Продам',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));    
  
    
  $this->init_field(array(
  'name' => 't1_desc',
  'title' => 'Подпись для кнопки "предложение"',
  'type' => 'text',
  'len' => 50,
  'default' => 'Нажмите эту кнопку, если Вы хотите продать товар.',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));      
    
  $this->init_field(array(
  'name' => 't2_title',
  'title' => 'Название для кнопки "спрос"',
  'type' => 'varchar',
  'len' => 50,
  'default' => 'Куплю',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));      
  
  
    
  $this->init_field(array(
  'name' => 't2_desc',
  'title' => 'Подпись для кнопки "спрос"',
  'type' => 'text',
  'len' => 50,
  'default' => 'Нажмите эту кнопку, если Вы хотите купить товар.',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));        
    
  $this->init_field(array(
  'name' => 'childs',
  'title' => 'Идентификаторы всех подразделов',
  'type' => 'varchar',
  'show_in' => array('edit', 'remove'),
  'write_in' => array()
  ));      

    
  $this->init_field(array(
  'name' => 'pchilds',
  'title' => 'Идентификаторы прямых подразделов',
  'type' => 'varchar',
  'show_in' => array('default', 'edit', 'remove'),
  'write_in' => array()
  ));      

  
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Номер по порядку для сортировки',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));         
  
    
  $this->init_field(array(
  'name' => 'idd',
  'title' => 'ID',
  'virtual' => 'id',  
  'show_in' => array('default', 'remove'),
  ));      
  
  
  $this->init_field(array(
  'name' => 'hr1',
  'title' => '<hr>',
  'virtual' => 'id',  
  'show_in' => array('edit', 'create', 'remove')
  ));        
      
  
  $this->init_field(array(
  'name' => 'adv1',
  'title' => 'Верхний блок рекламы',
  'type' => 'text',
  'len' => 70,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));      
    
  $this->init_field(array(
  'name' => 'adv2',
  'title' => 'Нижний блок рекламы',
  'type' => 'text',
  'len' => 70,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  ));      
    

   
}


function parse($d) {
  
  $d['url'] = "/category/?id={$d['id']}";
  $d['url_select'] = "/post/select_category/?id={$d['id']}";
  $d['url_subscribe'] = "/subscribe/?category_id={$d['id']}";
  $d['title2'] = ($d['title2']!='') ? $d['title2'] : $d['title'];
  
  $d['addon'] = "";
  
  
  $d['adv_top'] = (isset($d['adv1']) && $d['adv1']!='') ? "<tr class='rowLevel1'><td colspan='5'>Реклама</td></tr><tr ><td colspan='5' >{$d['adv1']}</td></tr>" : "";
  $d['adv_bottom'] = (isset($d['adv2']) && $d['adv2']!='') ? "<tr class='rowLevel1'><td colspan='5'>Реклама</td></tr><tr ><td colspan='5' >{$d['adv2']}</td></tr>": "";
  
  return $d;
}

function vcb_hr1($id){
  return "<hr>";
}

function v_title($t) {
  
  $t = trim($t);
  if($t=='') {
    $this->v_err = 1;
    $this->v_errm[] = "Поле название не заполнено.";
  }
  
  return $t;
}
function v_parent_id($id) {
  global $sv, $std, $db;

  $id = intval($id);
  
  if ($this->code=='edit') {
    
    if ($id == $this->current_record) {
      $this->v_err = 1;
      $this->v_errm[] = "В качестве родительской указана эта же категория.";
    }
    
    if ($id>0) {
      
      $ar = explode(",", $this->d['childs']);
      
      if (in_array($id, $ar)) {
        $this->v_errm[] = "В качестве родительской указана дочерняя категория 
          ({$id} не должно быть в {$this->d['childs']}).";       
        $this->v_err = 1;        
      }
    }
  }
  
  
  
  return $id;
}


function ci_parent_id() {
  global $sv, $std, $db;
  
  $ar = $this->read_and_parse_tree();

  $tr = array();
  $tr[] = "<option value='0'>корневая</option>";
  
  foreach($ar as $d) {
    //if ($d['id']==$this->current_record) continue;
    $step = $d['step']*3;
    $pad = str_pad("", $step, "- - ");
    $s = (isset($this->d['parent_id']) && $this->d['parent_id']==$d['id']) ? " selected" : "";
    $tr[] = "<option value='{$d['id']}'{$s}>{$pad}{$d['title']}</option>";
  }
  
  $ret = "<select name='new[parent_id]'>".implode("", $tr)."</select>";
  
  return $ret;
}


function after_update($d, $p, $err) {
  global $sv, $std, $db;
  
  if (!$err) {
    $this->update_all_childs();
  }
  
}

function after_create($p, $err) {
  global $sv, $db;
  
  if (!$err) {
    $this->update_all_childs();
  }
}

//controllers
function c_cron() {
  $this->update_all_childs();
  $this->update_chains();
  
  $p = load_bp('car');
  $p->cache_brands();
  
}

function ec_edit() {
  global $sv, $std, $db;
  
  // deprecated ?
  // $ret['fields'] = $this->get_active_fields('edit');
  
  $d = $this->get_current_record();
    
  
  if (method_exists($this, "before_edit")) {
    $this->before_edit();
  }  
    
  $s = $this->init_submit();
 
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {
      //return to index
      header("Location: ".su($sv->act).$this->slave_url_addon);
      exit();
    }
    $d = $this->get_current_record();     
  }
  
  $d = $this->get_and_parse_virtual_fields($d);

  $rows = array();
  foreach($this->fields as $f) {   
    if (in_array($this->code, $f['write_in'])) { 
      $rows[] = $this->wrow($f['name'], $f['input'], $d[$f['name']], $f['title'], $f['len']);
    }  
    if (in_array($this->code, $f['show_in'])) {       
      $rows[] = $this->row($f['title'], $d[$f['name']], $f['name']);
    }      
  }
 
  $ret['s'] = $s;
  $ret['form'] = $this->table($rows, "Сохранить", 1);
  
  // markitup
  $std->markitup->use_tinymce = 1;
  $std->markitup->use_emoticons = 0;
  $std->markitup->width = '100%';
  $std->markitup->compile("#full-text", "html");
  
  $ret['markitup'] = $std->markitup;
  
  return $ret;
}

//stuff
/**
 * #deprecated
 *
 * @param unknown_type $id
 * @return unknown
 */
function update_bcat_childs($id=0) {
  global $sv, $std, $db;
  
  $id = intval($id);
  if ($id<=0) return false;
  
  $ar = array();
  $db->q("SELECT id FROM {$this->t} WHERE `parent_id`='{$id}'", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[] = $d['id'];
  }

  $in = implode(",",$ar);
  $this->update_row(array('childs' => $in), $id);
}

// CRON 
function update_all_childs() {
  global $sv, $std, $db;
  
  $ar = $this->read_and_parse_tree();
  
  // количество объяв в категории
  $counts = array();
  $db->q("SELECT category_id, count(*) as size FROM {$sv->t['bposts']} 
  WHERE approved='1' GROUP BY `category_id`", __FILE__, __LINE__);
  
  while($d = $db->f()) {
    $counts[$d['category_id']] = $d['size'];
  }
  
  
  // расскладываем по уровням
  $steps = array();
  foreach($ar as $d) {
    $steps[$d['step']][] = $d;
  }
  
  // нижние уровни вперед
  krsort($steps);
  
  $childs = array();
  $pchilds = array();
  
  foreach($steps as $step => $c_ar) {
    foreach($c_ar as $d) {
      $childs[$d['parent_id']][] = $d['id'];
      $pchilds[$d['parent_id']][] = $d['id'];
      
      // скаладываем по уровням суммы объяв дочерних
      $counts[$d['id']] = (isset($counts[$d['id']])) ? $counts[$d['id']] : 0;
      $counts[$d['parent_id']] = (isset($counts[$d['parent_id']])) 
        ? $counts[$d['parent_id']] + $counts[$d['id']] : $counts[$d['id']];
      // добавляем подразделы дочерних если существуют
      if (isset($childs[$d['id']])) {
        $childs[$d['parent_id']] = array_merge($childs[$d['parent_id']], $childs[$d['id']]);
      }
    }
  }
  
  // обнуляем все записи
  $db->q("UPDATE {$this->t} SET childs='', pchilds='', `count`='0'");

  
  //записываем в базу
  foreach($childs as $id => $c_ar) {
    if ($id == 0) continue;
    $in = implode(",", $c_ar);
    $pin = implode(",", $pchilds[$id]);
    
    $p = array('childs' => $in, 'pchilds' => $pin);  
    $this->update_row($p, $id);  
  }
  
  foreach($counts as $id => $count) {
    if ($count>0 && $id>0) {
      $this->update_row(array('count' => $count), $id);
    }
  }
  
  // корневые араметры
  $root_childs = implode(",", $childs[0]);
  $root_pchilds = implode(",", $pchilds[0]);
  $root_count = $counts[0];
  
  $sv->m['config']->set_val('root_childs', $root_childs);
  $sv->m['config']->set_val('root_pchilds', $root_pchilds);
  $sv->m['config']->set_val('root_count', $root_count);
  
}

/**
 * Обновляет массив цепочек категорий
 * и записывает в конфиг
 */
function update_chains() {
  global $sv, $std, $db;
  
  
  $tr = array();
  $ar = $this->read_and_parse_tree(0);
  $items = $this->tree_items;
  
  $chains = array();
  $chains_all = array();
  
  foreach($ar as $d) {
    
   
    //$step = $d['step']*3;
    //$pad = str_pad("", $step, "- - ");
    
    $ch_ar = array();
    if (isset($d['chain'])) {
      foreach($d['chain'] as $id) {
        $ch_ar[] = $items[$id]['title'];
      }      
    }
    $ch_ar[] = $d['title'];
    $title = implode(" &gt; ", $ch_ar);
    
    if ($d['childs']!='') {      
      $chains_all[$d['id']] = $title;
    }
    else {      
      $chains[$d['id']] = $title;
      $chains_all[$d['id']] = $title;
    }
  }
  
  $sv->m['config']->set_val('cat_chains', serialize($chains));
  $sv->m['config']->set_val('cat_chains_all', serialize($chains_all));
}

function read_and_parse_tree($force_new = 0) {
  global $sv, $std, $db; 
  
  if (!$force_new && $this->tree_parsed)  {
    return $this->tree;  
  }
  
  $this->tree = array();
  $this->tree_step = 0;
    
  $db->q("SELECT * FROM {$this->t} ORDER BY place asc, title ASC", __FILE__, __LINE__);
 
  $ar = $pids = array();
  while($d = $db->f()) {
    $ar[$d['id']] = $d;
    $pids[$d['parent_id']][] = $d['id'];
  }
  
  $this->tree_items = $ar;
  $this->tree_childs = $pids;
  
  $f = (isset($this->tree_childs[0]))  ? array('key' => 0) : each($this->tree_childs);

  // нулевой уровень
  foreach($this->tree_childs[$f['key']] as $id) {
    $d = $this->tree_items[$id];
    $d['step'] = $this->tree_step;
    $this->tree[$d['id']] = $d;
    if (isset($this->tree_childs[$d['id']])) {
      $this->tree_subs($d['id']);
    }    
  }
  $this->tree_parsed = 1;
  return $this->tree;
}

/**
 * Вспомогательная рекурсивная функция для построения дерева
 *
 * @param unknown_type $page_id иентификатор текущего звена
 * @return unknown
 */
function tree_subs($page_id) { 
  // если страницы нет в списке или нет потомков
  if (!isset($this->tree_items[$page_id]) || !isset($this->tree_childs[$page_id])) {
    return false;
  }
  
  $this->tree_step++;
  $this->tree_chain[] = $page_id;
  
  // перебираем потомков и запрашиваем рекурсивно потомков второго уровня и дальше
  foreach($this->tree_childs[$page_id] as $id) {
    $d = $this->tree_items[$id];
    $d['step'] = $this->tree_step;
    $d['chain'] = $this->tree_chain;
    //$d['chain'][] = $d['id'];
    
    if (isset($this->tree[$d['id']])) {
      echo "бесконечный цикл?";
      return false;
    }
    $this->tree[$d['id']] = $d;
    if (isset($this->tree_childs[$d['id']])) {
      $this->tree_subs($d['id']);
    }
  }
  
  array_pop($this->tree_chain);
  
  $this->tree_step--;    
  return true;
}

/**
 * Список категорий по указанному родителю
 *
 * @param unknown_type $id
 * @param unknown_type $d - bcat record
 */
function list_by_parent($id, $parse = 1) {
  global $sv;
  
  $id = intval($id);
  $ar = $this->item_list("`parent_id`='{$id}'", "`place` ASC, `title` ASC", 0, $parse);
  
  return $ar;
}

/**
 * Enter description here...
 *
 * @param unknown_type $id = category_id
 * @param unknown_type $forward
 * @param unknown_type $start_new = new recurse cycle
 * @return unknown
 */
function inc_count($id, $forward = 1, $start_new = 1) {
  global $sv, $std, $db;
  
  if ($start_new) {
    $this->inc_count = 0;
  }  
  if ($this->inc_count>20) {
    die("recurse error in inc_count = limit reached {$this->inc_count}");
  }
  
  $id = intval($id);
  if ($id<=0) {
    return false;
  }
  
  $d = $this->get_item($id, 0, 0);
  if (!$d) {
    echo "inc_count error cat_id = {$id} not found";
    return false;
  }
  
    
  $new_val = ($forward) ? $d['count']+1 : $d['count']-1;  
  $this->update_row(array('count' => $new_val), $id);
  $this->inc_count($d['parent_id'], $forward, 0);
  
  return true;
}

function get_subcats($id) {
  global $sv;
  
  $id = intval($id);  
  $ar = $this->item_list("`parent_id`='{$id}'", "`place` ASC, `title` ASC", 0, 0);
  
  return $ar['list'];  
}

function pattern_by_id($id) {
  global $sv, $std, $db;
  
  $keys = array_keys($this->patterns);
  $id = (in_array($id, $keys)) ? $id : 'standart';
  
  $ret = array('title' => $this->patterns[$id], 'id' => $id);
  return $ret;
}
//eoc
}  
  
?>