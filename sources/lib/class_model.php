<?php
/**
 * ver 1.0
 * ec - embed_controller
 * c - user controller
 * 
 * esc - embed submit controller
 * sc - user submit controller
 * 
 * vcb - view callback (val)
 * wcb - write callback (val)
 * 
 * df - default_view callback
 * 
 * master_info_{$field_name}($master_data, $act) 
 * 
 * параметр virtual обязательно жолжен указывть на какое нибудь существующее поле
 *

 /*
 
 // AFTER
  $this->after_create($p, $err); // esc_create, init_submit_create
  $this->after_update($d, $p, $err); // esc_edit, init_submit_update
  $this->after_remove($d, $err);  // esc_remove, remove_item
  $this->after_insert($p); // success insert_row
  $this->after_change($p); // success after_create, succes after_update, success after_remove
  
 // BEFORE
  $this->last_v($p); // esc_create, esc_edit, init_submit_update, init_submit_create
  
  $this->before_default(); // ec_default
  $this->before_create(); // esc_create, init_submit_create - only after sumit
  $this->before_edit(); // ec_edit
  $this->before_update() // esc_edit, init_sumit_update - only after submit
  
  $this->v_before_remove($d); // ec_remove
  $this->before_remove();  // esc_remove, remove_item
  
  $this->garbage_collector($d);  // esc_remove, remove_item
*/
  


class class_model {
  
  var $code = "";
  var $model = ""; // @deprecated --> name
  var $name = "";
  var $title = ''; // Полное название модли на русском, задается вручную
  var $t = "";  // основная таблица
  var $primary_field = 'id'; // хак для тех таблиц у которых ключевое поле не id
  
  /**
   * SQL описания полей таблиц  
   *
   * @var array('table1' => "`id` bigint, ... , key(`id`)", 'table2' => "`id` bigint, ... , key(`id`)" );
   */
  var $tables = array(); 
  
  var $call = ""; // called controller
  var $submit_call = false;
  
  var $active_field = '';
  var $current_callback = '';
  var $current_validation = "";
  
  var $current_record = 0;
  
  var $c_order = ""; 
  var $c_dir = "";
  
  var $sub = "";
  var $sub_id = 0;

   
  
  var $per_page = 50;
  
  var $fkeys = array();
  var $fields = array();
  var $init_fields = false;
  var $controllers = array();
  
  var $active_fields = array();
  var $virtual_fields = array();
  var $inited_virtual_fields = 0;
  
  var $v_err = false; // validation err
  var $v_errm = array(); //validation errm

  // массив с ошибками
  var $errs = array(); // массив с названиями функций в которых были завиксированы ошибки  
  var $errm = array(); // ? используйте $this->errm('text', $err);
    
  /**
   * Значения полей для текущей формы
   * !неэкранированные (no htmlentites)
   *
   * @var unknown_type
   */
  var $vals = array(); 
  

  
  var $n = array();
  var $p = array();
  var $d = array();
  
  var $no_validation = array();
  var $vars = array();
  
  /**
   * Параметры которые необходимо добавить в конфиг
   * @example   
   * @var $name = 'Форум';
   * @var $config_vars = array('forum_rules' => array('title' => 'Правила форума', 'type' => 'text', 'value' => '', 'size' => '100'), ...);
   *
   */
  var $config_vars = array();
  
  // ЗАГРУЗКА ФАЙЛОВ 
  /**
   * Массив раширений для одиночно загружаемых файлов 
   *
   * @var unknown_type
   */
  var $ext_ar = array();  
  /**
   * Пути по умолчанию (необходимо назначить)
   *
   * @var unknown_type
   */
  var $uploads_dir = "uploads/";
  var $uploads_url = "uploads/";
    
  var $uploads_make_resize = 0;
  var $uploads_w = 200;
  var $uploads_h = 150;
  var $uploads_resize_type = "fixed";  // fixed | by_width | by_height
  
  var $upload_err_codes_eng = array(
    0=>"There is no error, the file uploaded with success.", 
    1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini.", 
    2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
    3=>"The uploaded file was only partially uploaded.",
    4=>"No file was uploaded.",
    6=>"Missing a temporary folder."          
  );
  var $upload_err_codes = array( 
    0=>"Файл загружен без ошибок.", 
    1=>"Првышен лимит upload_max_filesize в натсройках сервера php.ini.", 
    2=>"Превышен лимит MAX_FILE_SIZE указанный в форме.",
    3=>"Файл был загржен лишь частично.",
    4=>"Файл не был указан.",
    6=>"Временная папка для загрузки не доступна." 
  );
  
  var $last_rows_count = 0;
  var $last_insert_id = 0;
  var $last_affected = 0;
  
  var $log_file = "tmp/models.log";
  var $verbose = 1;
  
  // form design
  var $design_selectors_bottom = 0;
  var $custom_titles = array('create' => 'Создать', 'edit' => 'Редактирование записи');
  var $table_compact = 0; // если включить то таблицы редактирования будут состоять из одной колонки вместо двух
  var $table_width = "";
  
  var $url = ""; // относительный урл текщей страницы с кодом (если есть) и слешем вконце
  var $root_url = ""; // тоже самое что  предыдущее без кода
    
  // belongs to vars
  var $slave_mode = 0;
  var $slave_fields = array();  
  var $slave_url_vars = array();
  var $slave_url_addon = ""; 
  
  /**
   * Копия slave_url_addon в виде hidden input для GET-форм
   *
   * @var unknown_type
   */
  var $hidden_inputs = ""; 
  
  // for slave 
  var $master = array();  //data from master table  
  var $master_info = "";  //compiled html from _master_info
  
  // for master
  var $slaves = array(); // list of slave objects by modelname
  var $slave_act = "";

  // item selection
  /**
   * Массив параметров выборки array(
   * 'field1' => "`field1`='var1'",
   * 'field2' => "`field2`='var2'"
   * }
   */
  var $where = array(); // parameter => value  
  
  /**
   * Если не равен false то содержит массив array(  
   * [f] => , t1.title as product, t2.login as user
   * [j] => LEFT JOIN products t1 ON (t1.id=orders.product_id)
   * LEFT JOIN accounts t2 ON (t2.id=orders.user_id))
   *
   * @var unknown_type
   */
  var $joins = array('j' => '', 'f' => '');
  var $use_joins = 1;
  
  /**
   * Предустановленные значения полей, для списков и слейв моделей
   * array('field1' => 'var1', 'field2' => 'var2')
   * записываются при создании редактировании полей.
   */
  var $predefined = array();  

  
  // СЕЛЕКТОРЫ
  /**
   * Список полей селекторов
   * 
   * @var array
   */
  var $selector_fields = array(); 
  /**
   * Инициализированные значения
   * var => val
   * если не задано, то не используется
   *
   * @var unknown_type
   */
  var $selector_vals = array();
  
  // ПОИСК ПО СПИСКУ
  /**
   * Список полей для поиска
   *
   * @var array
   */
  var $search_fields = array();
  
  /**
   * Поиск только в определенном поле
   *
   * @var string
   */
  var $search_in = "";
  
  /**
   * Текущий поисковый запрос
   *
   * @var string
   */
  var $search_query = "";
  
  /**
   * Дополнительное условие для публичного поиска
   * например "AND {$this->t}.status='1'"
   *
   * @var unknown_type
   */
  var $public_search_sql_addon = '';
  
  // ПРОМЕЖУТОЧНЫЕ результаты
  /**
   * массив options после belongs_to_selector
   *
   * @var array
   */
  var $last_opts = array();  
 
/**
 * Инициализация поля
 *
 * @param $d - массив параметров инициализации
 * @return unknown
 */


/**
 * Доп функции в контроллере Edit
 *
 * @var unknown_type
 */
  var $load_attaches = 0;
  var $load_markitup = 0;
  var $attaches_page = '';
  var $attaches_top_margin = 500;
  var $markitup_use_tinymce = 1;
  var $markitup_use_emoticons = 0;
  var $markitup_width = '100%';
  var $markitup_selector = '#full-text';
  var $markitup_type = 'html';
  

  /**
   * Записывать все изменения в лог? esc_edit().
   *
   * @var unknown_type
   */
  var $log_edit = 0;
  
  /**
   * Включение древовидного режима
   *
   * @var boolean
   */

  var $tree_mode = 0;
  
  var $tree = array();
  var $tree_step = 0;
  var $tree_items = array();
  var $tree_childs = array();  
  var $tree_slug = '/';
  var $tree_ids = '/';          
  var $tree_used = array();
  var $tree_item_table = 'contacts';
  var $tree_item_field = 'region_id';
    
  
function init_field($d) {
  
  if (!$this->init_fields) {
    $this->init_embed_fields();
  }
  
  if (!isset($d['name'])) {
    echo "field name not specified in model {$this->name}";
    return false;
  }
  
  if (isset($this->fields[$d['name']])) {
    unset($this->fields[$d['name']]);
   // return false;
  }
  
  if (!isset($d['public_search'])) $d['public_search'] = 0;
  if (!isset($d['type']))       $d['type'] = "";
  if (!isset($d['description']))       $d['description'] = "";
  if (!isset($d['show_in']))    $d['show_in'] = array();
  if (!isset($d['write_in']))   $d['write_in'] = array();
  if (!isset($d['default']))    $d['default'] = "";
  if (!isset($d['unique']))     $d['unique'] = 0;
  if (!isset($d['hidden']))     $d['hidden'] = 0;  
  if (!isset($d['input']))      $d['input'] = $d['type'];
  if (!isset($d['call']))       $d['call'] = "";
  if (!isset($d['len']))        $d['len'] = "30";
  if (!isset($d['not_null']))   $d['not_null'] = 0;
  if (!isset($d['setcurrent'])) $d['setcurrent'] = 1;
  if (!isset($d['input']))      $d['input'] = $d['type'];
  if (!isset($d['belongs_to'])) $d['belongs_to'] = false;
  if (!isset($d['id']))         $d['id'] = "";
  if (!isset($d[$this->primary_field]))         $d[$this->primary_field] = "";
  if (!isset($d['class']))         $d['class'] = "";

  $d['belongs_to'] = $this->parse_belongs_to($d['belongs_to'], $d);
    
  // SELECTOR?
  // если является списочным элементом, то скорее всего нужен селектор
  $d_selector = ($d['belongs_to']!==false || $d['type']=='boolean') ? 1 : 0;
  $d['selector'] = (!isset($d['selector'])) ? $d_selector : $d['selector'];
  $d['selector'] = ($d['selector']==1) ? 1 : 0;
  
  // SEARCH?
  // если является текстовым элементом то скорее всего нужен поиск
  $search_types = array('varchar', 'text', 'bigint', 'int', 'float', 'double');
  $d_search = (in_array($d['type'], $search_types)) ? 1 : 0;
  // если уже назначен селектор то поиск скорее всего не нужен
  $d_search = ($d['selector']==1) ? 0 : $d_search;
  $d['search'] = (!isset($d['search'])) ? $d_search : $d['search'];
  $d['selector'] = ($d['selector']==1) ? 1 : 0;
  
  $this->fields[$d['name']] = $d;
  $this->fkeys[] = $d['name'];
   
   
  return  true;
}
  
// main functions ==============================  
function scaffold($code="") {
  global $sv, $std, $smarty;
  
  $this->code = ($code!='') ? $code : $sv->code; 
  $this->code = preg_replace("#[^a-z0-9\_\-]#si", "", $this->code);    
  if ($this->code=='default' && $this->tree_mode) {
    $this->code .= "_tree";
  }
  
  $this->current_record = intval($sv->id);    
  $this->url = $sv->view->safe_url;
  $this->root_url = $sv->view->root_url;
  
  if (count($this->config_vars)>0) {
    $this->sync_config_vars();
  }
  
  $this->init_controllers();
  $this->slave_init();
  
  // new hook
  $this->before_scaffold();
  
  eval("\$ret = \$this->{$this->call}();");
  
  
  $ret = (!$ret) ? array() : $ret;
  if (!isset($ret['m'])) {
    $ret['m'] = &$this;
  }
  
  if (is_object($smarty)) {
    $smarty->assign('m', $ret['m']);    
  }
  elseif(is_object($sv->tpl)) {
    $sv->tpl->m = &$this;
  }
  
  $ret['err_box'] = $std->err_box($this->v_err, $this->v_errm);
  
  // compatibility hack
  if (isset($ret['s']['err_box']) && $ret['err_box']==$ret['s']['err_box']) {
    unset($ret['s']['err_box']);
  }  
 
  return $ret;
}  
    
function call_controller($name='') {
  if (!method_exists($this, $name)) {
    die("Controller <b>{$this->name}</b> &rarr; <b>{$name}</b> not found.");
  }
  else {
    return $this->$name();
  }
}

function init_controllers() {
  $c = $this->get_controllers($this->code);
  $this->call = $c['call'];
  $this->submit_call = $c['submit'];  
}

function get_controllers($code)   {
  
  $e_controller = "ec_".$code; // embed controller
  $controller = "c_".$code;    // user controller
      
  $embed_submit_controller = "esc_".$code; // embed controller
  $submit_controller = "sc_".$code;    // user controller
          
  $ret = array('call' => false, 'submit'=>false);
      
  // action controller choose
  if (!method_exists($this, $controller)) {
    if (!method_exists($this, $e_controller)) {
      die("Controller <b>{$this->name}</b>&rarr;<b>{$controller}</b> not exists");
    }
    else {
      $ret['call'] = $e_controller;
    }
  }
  else {
    $ret['call'] = $controller;
  }
  
  
  // submit controler choose
  if (!method_exists($this, $submit_controller)) {
    if (method_exists($this, $embed_submit_controller)) {      
      $ret['submit'] = $embed_submit_controller;
    }
  }
  else {
    $ret['submit'] = $submit_controller;
  }
  
  return $ret;   
}
  
/**
 * Инициализацируем селекторы и значения
 * $this->selector_fields
 * $this->selector_vals
 * 
 * @var boolean $update_where = обновлять занчения $this->where? $this->predefined?
 * 
 *
 */
function init_selectors($update_where = 1) {
  global $sv, $std, $db;
  
  $fields = array();
  $vals = array();
  
  foreach($this->fields as $d) {
    if ($d['selector']) {
      $fields[] = $d['name'];
      if (isset($sv->_get[$d['name']]) && $sv->_get[$d['name']]!='*') {
        // проверяем заданные параметры
        $val = $std->text->cut($sv->_get[$d['name']], 'allow', 'mstrip');
        $val = $this->validate_field($d['name'], $val, 0);
        $vals[$d['name']] = $val;
      }
    }
  }
  $this->selector_fields = $fields;
  $this->selector_vals = $vals;
  
  
  if ($update_where) {
    foreach($vals as $k=>$v) {
      $this->where[$k] = "`{$this->t}`.`{$k}`='".$db->esc($v)."'";
      $this->predefined[$k] = $v;
      $this->slave_url_vars[$k] = $v;
    }
    $this->update_slave_url_addon();
  }
 
  return true;
}

/**
 * Иницициализация поисковых переменных
 * (админцентр)
 * 
 * @param unknown_type $update_where
 * @return unknown
 */
function init_search($update_where = 1) {
  global $sv, $std, $db;
  
  $fields = array();
  $vals = array();
  
  foreach($this->fields as $d) {
    if ($d['search']) {
      $fields[] = $d['name'];
    }
  }
  $this->search_fields = $fields; 
  
  // в каких полях искать
  if (isset($sv->_get['search_in']) && in_array($sv->_get['search_in'], $fields)) {
    $this->search_in = $sv->_get['search_in'];
    $vals['search_in'] = $this->search_in;
  }
  
  // обрабатываем запрос
  $err = 0;  
  $or = array();
  
  $this->search_query = $q = (isset($sv->_get['query'])) ? trim($std->text->cut($sv->_get['query'], 'allow', 'mstrip')) : "";
  $vals['query'] = $this->search_query;
  
  // получаем слова для поиска
  if ($q=='') {
    $err =1;
  }
  if (!$err) {
    $words = $std->search->parse_sstr($q);
    if (count($words)<=0 && $q<>'')  {
      $err = 1;
      $this->errm("Неподходящая строка для поиска.", $err);
    }
  }
  
  // компилим условия (field1 LIKE "%w1%w2%" OR field2 LIKE "%w1%w2%")
  if (!$err) {
    $like = "%".implode("%", $words)."%";
    foreach ($fields as $fn) {
      // используем только выбранные поля
      if ($this->search_in!='' && $fn<>$this->search_in) continue;      
      $or[] = "`{$this->t}`.`{$fn}` LIKE \"{$like}\"";
    }
  }
  
  
  
  if ($update_where) {
    if (count($or)>0) {
      $this->where['search'] = "(".implode(" OR ", $or).")";
      
      $in = ($this->search_in!='') ? " в полях <b>{$this->fields[$this->search_in]['title']}</b>" : "";
      $this->errm("Результаты поиска строки \"<b>".$std->text->cut($q, 'replace', 'replace')."</b>\"{$in}.", $err);
    }
    foreach($vals as $k=>$v) {     
      $this->slave_url_vars[$k] = $v;
    }
    $this->update_slave_url_addon();
  }

  
  return true;
}

/**
 * Добавляет все config_vars модели - в текущую конфигурацию
 *
 */
function sync_config_vars() {
  global $sv;  
  if (!$sv->m['config']->loaded) {
    // загружаем поименнованный список текущих параметров конфигурации
    $sv->m['config']->load_cfg();
  }  
  // прописываем в категорию параметра - название текущей модели, если она не указана
  foreach ($this->config_vars as $name => $d) {
    $this->config_vars[$name]['cat'] = (!isset($this->config_vars[$name]['cat'])) 
      ? (($this->title) ? $this->title : $this->name) : $this->config_vars[$name]['cat'];
  }
  $sv->m['config']->sync_vars($sv->m['config']->name_list, $this->config_vars);
}

// EMBED CONTROLLES ==============================  
function ec_default() {
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
function esc_default($n) {
 global $sv, $std, $db;
  
  $removed = array();
  $not_removed = array();
  
  if (isset($n['selected']) && is_array($n['selected'])) {
    foreach($n['selected'] as $id) {
      $id = intval($id);
      $this->remove_item($id);
      if ($this->err("remove_item{$id}")) {
        $not_removed[] = $id;
      }
      else {
        $removed[] = $id;
      }
    }
  }
 
  $removed_c = count($removed);
  $not_removed_c = count($not_removed);
  if ($removed_c>1) {
    $this->errm("Записи № <b>".implode(", ", $removed)."</b> ( {$removed_c} шт.) успешно удалены.");
  }
  elseif($removed_c==1) {
    $this->errm("Запись № <b>".implode(", ", $removed)."</b> успешно удалена.");
  }
  if ($not_removed_c>0) {
    $this->errm("Записи № <b>".implode(", ", $not_removed)."</b> ( {$not_removed_c} шт.) не удалось удалить.");
  }
  elseif($not_removed_c==1) {
    $this->errm("Запись № <b>".implode(", ", $removed)."</b> не удалось удалить.");
  }  

  
}

/**
 * Дефолтный контроллер вывода дерева
 * должны сохранится возможности поиска и выборки записей по условиям, по умолчанию выводятся корневые
 * 
 * отличия от стандартного
 * 1) отключена сортировка, установлена своя parent_slug ASC (уровни), place (порядок), title (алфавит)
 * 2) страницы не нужны - вырезаны
 * 3) устанавливается layout = tree|list
 * @return unknown
 */
function ec_default_tree() {
 global $sv, $std, $db;
  
 $this->sync_tree();
 
  $s = $this->init_submit();
  $ret['s'] = $s;
    
  $ret['fields'] = $this->get_active_fields('show');

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
  
  if ($where=='') {
    $ret['layout'] = 'tree';
    $where = "WHERE {$this->t}.parent_slug='/'";
  }
  else {
    $ret['layout'] = 'list';
  }
  
  // всего записей
  $db->q("SELECT 0 FROM {$this->t}", __FILE__, __LINE__);
  $ret['all_count'] = $db->nr();

  // страницы в списке
  if ($ret['layout']=='list')  {
    $db->q("SELECT 0 FROM {$this->t} {$where}");  
    $page = (isset($sv->_get['page'])) ? $sv->_get['page'] : 1;
    $ret['pl'] = $pl = $std->pl($db->nr(), $this->per_page, $page, u($sv->act, $sv->code, $sv->id).$this->slave_url_addon."&page=");
  }
  else {
    $ret['pl'] = $pl = array('ql' => '', 'page' => 1);
  }
  
  // выборка списка
  $ar = array(); $i = 0;
  $q = "  SELECT {$this->t}.*{$j['f']} 
          FROM {$this->t} 
          {$j['j']}
          {$where}
          ORDER BY {$this->t}.parent_slug ASC, {$this->t}.place ASC, {$this->t}.title ASC
           {$pl['ql']}";  
  $res = $db->q($q, __FILE__, __LINE__);
  while ($d = $db->f($res)) { $i++;    
    $d = $this->e_parse($d);
    $d = $this->parse($d);
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

function ec_create() {
  global $sv, $std, $db;

  $s = $this->init_submit();
 
  if ($s['submited'] && !$s['err']) {
    if (isset($sv->_post['commit'])) {
      //return to index
      header("Location: ".su($sv->act).$this->slave_url_addon);
      exit();
    }
  }
    
  $rows = array();
    
  foreach($this->fields as $f) {   
    if (in_array($this->code, $f['write_in'])) {    
      $val = (isset($s['v'][$f['name']])) ? $s['v'][$f['name']] : $f['default'];
      $rows[] = $this->wrow($f['name'], $f['input'], $val, $f['title'], $f['len']);
    } 
    if (in_array($this->code, $f['show_in'])) {       
      $rows[] = $this->row($f['title'], "", $f['name']);
    }           
  }
 
  $ret['s'] = $s;
  $ret['form'] = $this->table($rows, "Создать");
  return $ret;
}
function esc_create($n, $d=false) {
  global $sv, $std, $db;
  
  $err = 0;
  $insert_id = 0;
  $this->before_create();
  $err = ($this->err('before_create')) ? 1 : $err;

  // validation
  if (!$err) {
    $p = $this->validate_active_fields(0);
  }
 
  // predefined values
  foreach($this->predefined as $k=>$v) {
    if (!isset($p[$k])) {
      $this->vals[$k] = $p[$k] = $v;
    }
  }
 
  $r = $this->last_v($p);  
  if ($r) {
    $p = $r;
  }

  // validation errors
  $err = ($this->v_err) ? true : $err;
  
  // inserting if no errors
  if (!$err) {
    $af = $this->insert_row($p);    
    if ($af <= 0) {
      $err = 1;
      $this->errm("DB error, can't insert data.", $err);
    }
    else {
      $this->errm("Запись успешно создана.", $err); 
      $this->current_record = $insert_id = $db->insert_id();
      $this->errm("<A href='".su($sv->act, 'edit', $this->current_record)."{$this->slave_url_addon}'>Отредактировать новую запись</a> 
      &nbsp;&nbsp;|&nbsp;&nbsp; 
      <a href='".su($sv->act)."{$this->slave_url_addon}'>Вернуться к списку</a>
      &nbsp;&nbsp;|&nbsp;&nbsp; 
      <a href='".su($sv->act, 'create')."{$this->slave_url_addon}'>Создать еще запись</a>
      ", $err);
    }
  }  
  
  $this->after_create($p, $err);
    
  if ($err) $this->errs[] = __FUNCTION__;

  $ret['insert_id'] = $insert_id;
  $ret['v'] = $this->escape_vals();
  
  return $ret;
}

function ec_edit() {
  global $sv, $std, $db;
  
  // deprecated ?
  // $ret['fields'] = $this->get_active_fields('edit');
  
  $d = $this->get_current_record();
    
  $this->before_edit();
    
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
  
 
  if ($this->load_attaches) {
    // attaches
    $sv->load_model('attach');  
    $sv->m['attach']->action_url = u($sv->act, "attaches", $d[$this->primary_field]);
    $page = ($this->attaches_page!='') ? $this->attaches_page : $this->name;    
    $ret['attach'] = $sv->m['attach']->init_object($page, $d[$this->primary_field], $sv->user['session']['account_id']);
    $sv->parsed['admin_sidebar'] = "
      <div style='margin-top: {$this->attaches_top_margin}px; border: 1px solid #dddddd;'>
        <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
        {$ret['attach']['form']}
      </div>";
  }
  
  if ($this->load_markitup) {  
    // markitup
    $std->markitup->use_tinymce = $this->markitup_use_tinymce;
    $std->markitup->use_emoticons = $this->markitup_use_emoticons;
    $std->markitup->width = $this->markitup_width;
    $std->markitup->compile($this->markitup_selector, $this->markitup_type);  
    $ret['markitup'] = $std->markitup;
  }

  
   
  return $ret;
}
function esc_edit($n, $d=false) {
  global $sv, $std, $db;
  
  $err = 0;
  $d = $this->get_current_record();
  
  $this->before_update();
  $err = ($this->err('before_update')) ? 1 : $err;
 
  // validation
  if (!$err) {
    $p = $this->validate_active_fields(0);
  }
  
  // predefined values
  foreach($this->predefined as $k=>$v) {
    if (!isset($p[$k])) {
      $this->vals[$k] = $p[$k] = $v;
    }
  }

  $r = $this->last_v($p);  
  if ($r) {
    $p = $r;
  }

  
  // validation errors
  $err = ($this->v_err) ? true : $err;
  $this->errm($this->v_errm);
  
  if ($this->log_edit) {
    $log_text = "---\n{$sv->ip} [{$sv->user['session']['account_id']} = {$sv->user['session']['login']}] {$sv->date_time} {$sv->request_uri}\n";
    ec("{$log_text}Submit array: \n".t($this->n), 0, 1, 'edit.log');
    ec("Write array: \n".t($this->p), 0, 1, 'edit.log');
  }
  
  // updating if no errors
  if (!$err) {
    $af = $this->update_row($p);
    if ($af <= 0) {
      $err = 1;
      $this->errm("Данные не изменились.");
    }
    else {
      $this->errm("Данные сохранены [".$std->time->format($sv->post_time, 0.5)."]", $err);
    }
  }  
  
  $this->after_update($d, $p, $err);
   
  if ($err) $this->errs[] = __FUNCTION__;
      
  $ret['v'] = $this->escape_vals();
  
  return $ret;
}

function ec_remove() {
  global $sv, $std, $db;

  $err = 0; 
  $ret['fields'] = $this->get_active_fields('remove');
  
  $d = $this->get_current_record();
  
  // validation
  if (method_exists($this, 'v_before_remove')) {
    $this->v_before_remove($d);     
  }
  
  $err = ($this->v_err) ? 1 : $err;
  
  //if no errors
  if (!$err) {    
    // submit
    $s = $this->init_submit();  
    
    if (isset($sv->_post['commit']) && count($sv->msgs)<=1) {
      //return to index
      header("Location: ".su($sv->act).$this->slave_url_addon);
      exit();
    }
    
    $ar = $this->get_active_fields('show');        
    $rows = array("<input type='hidden' name='new[accepted]' value='1'>");
    $rows[] = "<tr bgcolor=#efefef><th colspan=2>Подтверждение</td></tr>";
    $rows[] = "<tr bgcolor=white><tD colspan=2>Вы уверены что хотите удалить 
               <a href='".su($sv->act, 'edit', $sv->id)."'>запись</a>?</td></tr>";
    foreach($ar as $f) {
      $rows[] = $this->row($this->fields[$f]['title'], $d[$f], $f);
    }    
    
    $ret['form'] = $this->table($rows, "Удалить", 1);
    $ret['s'] = $s;
  }
  
  $ret['d'] = $d;
  
  return $ret;
}
function esc_remove($n, $d = false) {
  global $sv, $std, $db;
   
  $err = 0;
  
  if ($d===false) {
    $d = $this->get_current_record();
  }
  else {
    $this->current_record = $d[$this->primary_field];
  }     
 
  $this->before_remove();
  $err =  ($this->err('before_remove')) ? 1 : $err;
  
  if (!$err) {
    $this->garbage_collector($d);
    $err = ($this->err('garbage_collector')) ? 1 : $err;
  }
  
  // removing if no errors
  if (!$err) {    
    $af = $this->remove_row($d['id']);
    
    $db->q("SELECT 0 FROM {$this->t} WHERE `".$this->primary_field."`='{$this->current_record}'", __FILE__, __LINE__);
    
    if ($db->nr() > 0) {
      $err = 1;
      $this->errm("Ошибка базы данных, не удалось удалить данные.", $err);
    }
    else {
      $this->errm("Запись успешно удалена.", $err);
    }
  }  
  
  $this->after_remove($d, $err);
  
  if ($err) $this->errs[] = __FUNCTION__;
  
}

function ec_attaches() {
  global $sv, $std, $db;
    
  $d = $this->get_current_record();
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $this->current_record);
  $ret['attach'] = $sv->m['attach']->init_object($this->name, $this->current_record, $sv->user['session']['account_id']);
  
  return $ret;
}

// database functions ====================
/**
 * Выбирает текущую запись для встроенных контроллеров edit/remove и подобных
 * проверяется стек where (для slave моделей)
 * 
 * @return unknown
 */
function get_current_record($parse = 0) {
  global $db;
  
  $wh = $this->get_where();
  $where = ($wh!='') ? " AND {$wh}" : "";
  
  $db->q("SELECT * FROM {$this->t} WHERE `".$this->primary_field."`='{$this->current_record}' {$where}", __FILE__, __LINE__);
  
  if ($db->nr()>0) {
    $d = $db->f();
    $d = $this->e_parse($d);
    
    foreach ($this->fields as $k=>$ar) {
      if (!isset($d[$k])) {
        $d[$k] = $ar['default'];
      }
    }
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
    }
    $this->d = $d;
    return $d;
  }
  else {
    $sub = ($wh!='') ? "<br>Requirements: <b>{$wh}</b>." : "";
    die("Model <b>{$this->name}</b>: record <b>{$this->current_record}</b> not found. {$sub}"); 
  }
  
}

function insert_row($p) {
  global $sv, $db;

  $p['created_at'] = $sv->date_time;
  
  $p['created_by'] = (isset($sv->user['session']['account_id'])) ? intval($sv->user['session']['account_id']) : 0;
  
  $s = array();
  foreach ($p as $k=>$v) {
    if (is_null($v)) {
      $s[] = "`".addslashes($k)."`=NULL";
    }
    else {
      $s[] = "`".addslashes($k)."`='".$db->esc($v)."'";
    }
  }
  
  $q = "INSERT INTO {$this->t} SET \n".implode(", \n", $s);     
  $db->q($q, __FILE__, __LINE__);     
  $this->last_insert_id = $db->insert_id();
  $this->last_affected = $db->af();
  
  if ($this->last_affected) {
    $this->after_insert($p);
  }
  return $this->last_affected;
}

/**
 * хук вызываемый при успешной вставке insert_row
 *
 * @param unknown_type $p
 */
function after_insert($p) { }

function update_row($p, $id=0, $update_stamps = 1) {
  global $sv, $db;

  $id = intval($id);
  if ($id<=0) {
    $id = intval($this->current_record);
    if ($id<=0) {
      die("Not specified ID for update.");
    }
  }
  
  if ($update_stamps) {
    $p['updated_at'] = $sv->date_time;
    $p['updated_by'] = (isset($sv->user['session']['account_id'])) ? intval($sv->user['session']['account_id']) : 0;
  }
  
  $s = array();
  foreach ($p as $k=>$v) {
    $s[] = "`".addslashes($k)."`='".$db->esc($v)."'";
  }
  
  $q = "UPDATE {$this->t} SET \n".implode(", \n", $s)." WHERE `".$this->primary_field."`='{$id}'";     
  $db->q($q, __FILE__, __LINE__);   
  $this->last_affected = $db->af();
  
  return $this->last_affected;
}

function update_wh($p, $wh = false, $update_stamps = 1) {
  global $sv, $db;
  
  if ($wh===false) {
    $this->log("<b>\$wh</b> not defined in {$this->name}->update_wh()");
  }
  
  if ($update_stamps) {
    $p['updated_at'] = $sv->date_time;
    $p['updated_by'] = (isset($sv->user['session']['account_id'])) ? intval($sv->user['session']['account_id']) : 0;
  }
  
  $s = array();
  foreach ($p as $k=>$v) {
    $s[] = "`".$db->esc($k)."`='".$db->esc($v)."'";
  }
  
  $q = "UPDATE {$this->t} SET \n".implode(", \n", $s)." WHERE {$wh}";     
  $db->q($q, __FILE__, __LINE__);     
  $this->last_affected = $db->af();
  
  return $this->last_affected;
}

/**
 * Удаление строки без вызова хуков
 *
 * @param unknown_type $id
 * @return unknown
 */
function remove_row($id=0) {
  global $sv, $db;

  $id = intval($id);
  if ($id<=0) {
    $id = intval($this->current_record);
    if ($id<=0) {
      die("Not specified ID for delete.");
    }
  }  
  
  $q = "DELETE FROM {$this->t} WHERE `".$this->primary_field."`='{$id}'";     
  $db->q($q, __FILE__, __LINE__);     
  
  $this->last_affected = $db->af();
  return $this->last_affected;
}

/**
 * синоним и замена remove_items_wh
 * 
 * @param unknown_type $where
 * @return unknown
 */
function remove_rows_wh($where) {
  global $db;
    
  $where = ($where!='') ? "WHERE {$where}" : ""; 
  $db->q("DELETE FROM {$this->t} {$where}", __FILE__, __LINE__);

  $this->last_affected = $db->af();
  return $this->last_affected;
}

/**
 * @deprecated 
 * @see remove_rows_wh()
 * 
 */
function remove_items_wh($where) {
  return $this->remove_rows_wh($where);
}


/**
 * Стандартная функция удаления записи, вызывает все хуки и мусоросборщик
 *
 * @param unknown_type $id
 * @param unknown_type $d
 * @param unknown_type $parse
 * @param unknown_type $update_current
 * @return array('affected' => , 'err' => , 'errm' => )
 */
function remove_item($id = 0, $d = false, $parse = 0, $update_current = 1) {
  global $sv, $std, $db;
  
  $id = intval($id);
  $err = 0;
  $affected = 0;
  
  if (!$d) {
    $d = $this->get_item($id, $parse, 0);
    if (!$d) {
      $err = 1;
      $this->errm("Запись №{$id}, подлежащая удалению, не найдена.", $err);
    }
  }

  if (!$err && $update_current) {
    $this->current_record = $d;
  }
  
  if (!$err) {
    $this->before_remove();
    $err = ($this->err('before_remove')) ? 1 : $err;
  }
  
  if (!$err) {
    $r = $this->garbage_collector($d);
    $err = ($this->err('garbage_collector')) ? 1 : $err;
  }
  
  if (!$err) {
    $affected = $this->remove_row($d[$this->primary_field]);    
  }
  
  $this->after_remove($d, $err);
  
  $ret['affected'] = $affected;
  if ($err) $this->errs[] = "remove_item";
  if ($err) $this->errs[] = "remove_item{$id}";
  
  return $ret;  
}

function get_joins($update_joins = 1 ) {
  global $sv;
  
  $this->init_virtual_fields();
  
  $f = array();
  $j = array();
  
  $i = 0;
  
  foreach ($this->virtual_fields as $vf) { 
    $field = $this->fields[$vf]['virtual'];
    $d = $this->fields[$field]['belongs_to'];
    
    $keys = (is_array($d)) ? array_keys($d) : array($d);
    
    if ($keys[0]=='table') { $i++;
      $short = "t{$i}";
      $table = $sv->t[$d['table']];     
      $f[] = ", {$short}.{$d['return']} as {$vf}";
      $j[] = "LEFT JOIN {$table} {$short} ON ({$short}.{$d['field']}={$this->t}.{$field})";
    }
    else {
     
    }
  }
  
  
  $ret['f'] = implode("", $f);
  $ret['j'] = implode("\n", $j);
  
  if ($update_joins) {
    $this->joins = $ret;
  }
  return $ret;
}

function get_where($prefix = 0, $delimiter = "AND") {
  global $sv, $db;
  
  if (count($this->where)<=0) {
    $ret = "";
  }
  else { 
    $ret = implode(" {$delimiter} ", $this->where);
    if ($prefix) {
      $ret = "WHERE ".$ret;
    }
  }
  return $ret;
}

// form generating =============================

function wrow($f, $type, $val, $title="", $s = false) {
  global $sv, $std, $db;
  
  $d = $this->fields[$f];
  if ($d['hidden']) return "";
  

  // callbacks
  $callback = "wcb_{$f}";
  if (method_exists($this, $callback)) {    
    $this->current_callback = $f; 
    $val = $this->$callback($val, $d);    
  }

   
  $attr_id = ($d['id']) ? " id='{$d['id']}'" : "";
  $attr_class = ($d['class']!='') ? " class='{$d['class']}'" :  "";
  $attr_id .= $attr_class;
  
  switch ($type) {
    case 'varchar':
      $v = $std->text->cut($val, 'replace', 'replace');
      $s = ($s===false) ? 50 : $s;
      $input = "<input type='text' name='new[{$f}]' size={$s} value='{$v}'{$attr_id}>";
    break;
    case 'password':
      $v = $std->text->cut($val, 'replace', 'replace');
      $s = ($s===false) ? 50 : $s;
      $input = "<input type='password' name='new[{$f}]' size={$s} value=''{$attr_id}>";
    break;
    case 'boolean':
      $net = ($val==1) ? "" : " selected";
      $input = "<select name='new[{$f}]'{$attr_id}><option value='1'>Да</option><option value='0'{$net}>Нет</option></select>";
    break;
    case 'integer': case 'int': case 'bigint':     
      if ($type=='bigint') {
        $v =preg_replace("#[^0-9]#msi", "", $val);
        $v = ($v=='') ? 0 : $v;
      }
      else {
        $v = intval($val);
      }    
      $s = ($s===false) ? 12 : $s;
      $input = "<input type='text' name='new[{$f}]' size='{$s}' value='{$v}'{$attr_id}>";      
    break;
    case 'float':
      $v = floatval($val);
      $s = ($s===false) ? 12 : $s;
      $input = "<input type='text' name='new[{$f}]' size='{$s}' value='{$v}'{$attr_id}>";      
    break;    
    case 'double':
      $v = doubleval($val);
      $s = ($s===false) ? 12 : $s;
      $input = "<input type='text' name='new[{$f}]' size='{$s}' value='{$v}'{$attr_id}>";      
    break;      
    case 'select':      
      $d = $this->fields[$f];
      $b = $this->fields[$f]['belongs_to'];
      $input = $this->belongs_to_select($b, $f, $val);
    break;
    case 'multiselect':      
      $d = $this->fields[$f];
      $b = $this->fields[$f]['belongs_to'];
      $input = $this->belongs_to_select($b, $f, $val, 1);
    break;
    
    case 'text':
      $v = $std->text->cut($val, 'replace', 'replace');
      $s = ($s===false) ? 50 : $s;
      $r = round($s/6);
      $input = "<textarea name='new[{$f}]' cols={$s} rows='{$r}'{$attr_id}>{$v}</textarea>";
      
    break;
    case 'checkbox':
      $s = ($val) ? " checked" : "";
      $input = "<input type='checkbox' name='new[{$f}]'{$s}{$attr_id}>";      
    break;
    case 'file':
      $s = ($s===false) ? 50 : $s;
      $help = ($this->uploads_make_resize) ? " [ресайзы включены, тип={$this->uploads_resize_type}, w={$this->uploads_w}, h={$this->uploads_h}]" : "";
      $input = "<input name='{$f}' type=file size={$s}{$attr_id}><br><span style='color:gray;'>".implode(", ", $this->ext_ar)."{$help}</span>
      <input type='hidden' name='new[file_submit_{$f}]'>";
    break;
    case 'antispam':
      $sv->init_class('antispam');            
      $k = $sv->antispam->generate_keys();
      $input = "
       <table cellpadding=0 cellspacing=0 style='margin:0px;' border=0>        
         <tr>
            <td style='padding:5px 5px 0 0;'>  	
            <img src='/captcha_{$k['key1']}.gif' width=200 height=60 
            alt='turn on images' 
            title='turn on images' style='border: 1px solid #999999;'>
        	  </td>        	
         </tr><tr>
            <td style='padding:5px 0 0 0px;'>
            <input type='hidden' size=20 name='new[key1]' value='{$k['key1']}'>
            <input type='text' size=10 name='new[key2]' value='' maxlength=7 style='font-size:100%;text-align:center;'>
            </td>
          </tr>
       </table>
        "; 
         
    break;
    case 'date':      
      $c = ($d['default']=='now') ? 1 : 0;
      $ch = (isset($d['setcurrent']) && $d['setcurrent']==1) ? 1 : 0;
      $input = $std->time->date_box($val, 1, "new[{$f}]", $c, $ch);
    break;
    case 'datetime':
      $c = ($d['default']=='now') ? 1 : 0;
      $ch = (isset($d['setcurrent']) && $d['setcurrent']==1) ? 1 : 0;
      $input = $std->time->datetime_box($val, 1, "new[{$f}]", $c, $ch);
    break;
    case 'time':
      $c = ($d['default']=='now') ? 1 : 0;
      $ch = (isset($d['setcurrent']) && $d['setcurrent']==1) ? 1 : 0;
      $input = $std->time->datetime_box($val, 0, "new[{$f}]", $c, $ch);
    break;
    case 'custom':
      if (method_exists($this, "ci_{$f}")) {
        eval("\$input=\$this->ci_{$f}(\$val);");
      }
      else {
        $input = "<span style='color:red;'>Custom input method \"<b>ci_{$f}</b>\" not defined.</span>";
      }
    break;
    default: 
      $input = "input not defined for <b>{$type}</b> in wrow";
    break;
    
  }
  
  $t = ($title!='') ? $title : $f;
  $desc = ($d['description']!='') ? "<div style='color:gray;font-size:90%;'>{$d['description']}</div>" : '';
  $ret = ($this->table_compact) 
    ? "<tr bgcolor=white><td><div class='formvar-lable'>{$t}{$desc}</div><div>{$input}</div></td></tr>" 
    : "<tr bgcolor=white><td>{$t}{$desc}</td><td>{$input}</td></tr>";
  return $ret;  
}

function row($t, $val, $name, $fatal_callback = 0) {
  global $sv, $std, $db;
  
  $t_name = $name;
  $virtual = false;

  if ($this->is_virtual($name)) {    
    $name = $this->get_virtual($name);
    $val = (isset($this->d[$name])) ? $this->d[$name] : null; 
    $virtual = true;
  }

  // callbacks
  $callback = "vcb_{$t_name}";
 
  if (method_exists($this, $callback)) {
    $this->current_callback = $name;   
    $val = $this->$callback($val);    
  }
  elseif ($fatal_callback) {
    die("View callback <b>{$this->name}-&gt;{$callback}</b> not defined.");
  }
 
  //check belongs to  
  if (!$virtual) {    
    $f =  $this->fields[$name];
    $val = $this->belongs_to_view($f['belongs_to'], $name, $val);
  }
  else {
    $f =  $this->fields[$t_name];
  }
   
  $desc = ($f['description']!='') ? "<div style='color:gray;font-size:90%;'>{$f['description']}</div>" : '';
  $ret = ($this->table_compact) 
  ? "<tr bgcolor=white><td><div class='formvar-lable'>{$t}{$desc}</div><div>{$val}</div></td></tr>" 
  : "<tr bgcolor=white><td>{$t}{$desc}</td><td>{$val}</td></tr>";
    
  return $ret;
}

function table($rows, $name="Сохранить", $commit = 1) {
  
  $c = ($commit) ? "<input type='submit' value='{$name} и продолжить редактирование'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : "";
  
  $w = ($this->table_width!='') ? " width='{$this->table_width}'" : "";
  $ret = "
  <table bgcolor=#cccccc cellpadding=5 cellspacing=1{$w}>
  ".implode("\n", $rows)."
  <tr bgcolor=#efefef><tD align=center colspan=2>{$c}<input type='submit' name='commit' value='{$name}'></td></tr>
  </table>";
  
  return $ret;
  
}

function compile_edit_table($d, $name="Сохранить", $commit = 1) {     
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
  return  $this->table($rows, $name, $commit);  
}

/**
 * Выдает готовые строки публичной таблицы с данными
 *
 * @param array $fields - список полей которые надо вывести
 * @param array $d - данные
 * @param boolean $fatal_callback
 * @param boolean $hide_empty
 * @return string $tr
 */
function compile_public_table($fields, $d, $fatal_callback = 1, $hide_empty = 1) {
  
  $tr = array();
  
  foreach($fields as $fn) {
    $vn = $this->get_virtual($fn);
    if (!$d[$vn] || $d[$vn]=='0000-00-00') continue;
         
    $f = $this->fields[$fn];    
    $tr[] = $this->row($f['title'], $d[$vn], $fn, $fatal_callback);
  }
  
  $ret = implode("\n", $tr);
  return $ret;
}

function default_view_keys($d) {
  
  foreach($d as $k=>$v) {
    
    if (method_exists($this, "df_{$k}")) {
      eval("\$v = \$this->df_{$k}(\$v);");
      $d[$k] = $v;
    }
  }
  
  return $d;
}

/**
 * готовая таблица с селекторами
 *
 * @return unknown
 */
function html_selectors() {
  global $sv, $std, $db;
  
  $tr = array();
  foreach($this->selector_fields as $fn) {
    $val = (isset($this->selector_vals[$fn])) ? $this->selector_vals[$fn] : "";
    $tr[] = "<td>".$this->compile_selector($fn, $val)."</td>";
  }
  if (count($tr)>0) {
    $tr[] = "<td><input type='submit' value='Выбрать'></td>";
  }
  
  $ret = "
  <form action='index.php' method='GET' enctype='multipart/form-data'>      
    <input type='hidden' name='{$sv->act}_{$this->code}' value='{$sv->id}'>    
    {$this->hidden_inputs}
    <table class='selectors'><tr valign='bottom'>".implode("\n", $tr)."</tr></table>
  </form>
  ";
  return $ret;
}

/**
 * Сборка селектора выбранного поля
 *
 * @param unknown_type $fn
 * @param unknown_type $val
 * @return unknown
 */
function compile_selector($fn, $val) {
  global $sv, $std, $db;
  
  $field = &$this->fields[$fn];
  // не выбирать ничего - если ничего не задано
  $select = (!isset($this->selector_vals[$fn])) ? 0 : 1;
    
  if ($field['belongs_to']) {
    
    $this->belongs_to_select($field['belongs_to'], $fn, $val, 0, $select);
    $tr = $this->last_opts;
  }
  elseif($field['type']=='boolean') {
    $s1 = ($select && $val==1) ? " selected" : "";
    $s2 = ($select && $val==0) ? " selected" : "";
    $tr = array(
      "<option value='1'{$s1}>Да</option>",
      "<option value='0'{$s2}>Нет</option>"    
    );
  }
  else {
    // иначе по сгруппированным значениям поля текущей таблицы
    $tr = array();
    $db->q("SELECT `{$fn}` FROM {$this->t} GROUP BY `{$fn}` ORDER BY `{$fn}` ASC ", __FILE__, __LINE__);
    while($d = $db->f()) {      
      $v = $d[$fn];
      $ev = $std->text->cut($v, 'replace', 'replace');
      $s = ($select && $v==$val) ? " selected" : "";
      $tr[] = "<option value='{$ev}'{$s}>{$v}</option>";
    }
  }
  
  $ret = "
  <small>{$this->fields[$fn]['title']}</small><br>
  <select name='{$fn}'>
    <option value='*'> * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
    ".implode("\n", $tr)."
  </select>";
  return $ret;
}


/**
 * готовая таблица с поисковой формой
 *
 * @return string
 */
function html_search() {
  global $sv, $std, $db;
  
  $opts = array("<option value='*'> * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>"); 
  
  foreach($this->search_fields as $fn) {
    $s = ($this->search_in == $fn) ? " selected" : "";
    $opts[] = "<option value='{$fn}'{$s}>{$this->fields[$fn]['title']}</option>";
  }
 
  $query = $std->text->cut($this->search_query, 'replace', 'replace');
  $ret = "
  <form action='index.php' method='GET' enctype='multipart/form-data'>  
    <input type='hidden' name='{$sv->act}_{$this->code}' value='{$sv->id}'>   
    {$this->hidden_inputs} 
    <table class='search'><tr valign='bottom'>
      <td><input type='text' size='40' value='{$query}' name='query'></td>
      <td>
        <select name='search_in'>".implode("\n", $opts)."</select>
      </td>
      <td><input type='submit' value='Поиск'></td>
    </tr></table>
  </form>
  ";
  return $ret;
}

/**
 * обычная инциализация сабмита с вызовом подконтроллера
 *
 * @param boolean $need_parsing - парсить или нет значения полей если данные не отправлены 
 * (исползуется только в edit., текущая запись должна быть объявлена)
 * @return unknown
 */
function init_submit($need_parsing = 0) {
  global $sv, $std, $db;
  
  $err = 0;
  $submited = 0;

  $n = (isset($sv->_post['new'])) ? $sv->_post['new'] : false; //  || (isset($sv->_get['submited']) && $sv->_get['submited']==1)
  if ($n!==false) {
    $submited = true;
  }

  $this->n = $n;  
  $this->active_fields = $ret['fields'] = $this->get_active_fields('write');
   
  if ($submited && $this->submit_call!==false) {
    
    eval("\$r = \$this->{$this->submit_call}(\$n);");
    $err = ($this->err($this->submit_call)) ? 1 : $err;
      
    if (is_array($r)) {
      foreach($r as $k=>$v) {
        $ret[$k] = $v;
      }    
    }
  }
  elseif ($submited && $this->submit_call===false) {
    t("{$this->name}::{$this->code} no controller for submit", 1);   
  }
  
  // если не отправлена форма пытаемся взять дефолтные значения полей из текущей записи
  if (!$submited && $need_parsing) {
    foreach ($this->active_fields as $fn) {      
    	$this->vals[$fn] = (isset($this->d[$fn])) ? $this->d[$fn] : "";
    }
  }
      
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $this->v_errm);  
  $ret['submited'] = $submited;
  $ret['v'] = $this->escape_vals();
  
  return $ret;
}
  
/**
 * автоматическая обработка update(edit) форм по списку полей заданных в write_in  для текущего кода
 *
 * Важно! Текущая запись (this->d) должна быть определена!
 * @param boolean $fatal_validation - фатальная ошибка если влаидация для одного из полей не прописана
 * @return array('err_box' => , 'v' => 'submited' => 0/1, 'fields' => )
 */
function init_submit_update($fatal_validation = 1) {
  global $sv, $std, $db;
  
  $err = 0;
  
  $p = array();
  $submited = false;

  if (!isset($this->d[$this->primary_field])) {
    $err = 1;
    $this->errm("Ошибка проектирования. Текущая запись не определена в ".__FUNCTION__, $err);
  }
  $n = (isset($sv->_post['new']))  // || (isset($sv->_get['submited']) && $sv->_get['submited']==1)
       ? $sv->_post['new'] : false;
  if ($n!==false) {
    $submited = true;
  }

  $this->n = $n;  
  $this->active_fields = $ret['fields'] = $this->get_active_fields('write');
   
  // если отправлена форма то вызываем по порядку стандартные процендуры обработки update форм
  if ($submited) {
  
    $this->before_update();
    $err = ($this->err('before_update')) ? 1 : $err;
   
    // validation
    if (!$err) {      
      $p = $this->validate_active_fields($fatal_validation);
    }
    
    // predefined values
    foreach($this->predefined as $k=>$v) {
      if (!isset($p[$k])) {
        $this->vals[$k] = $p[$k] = $v;
      }
    }
     
    $r = $this->last_v($p);  
    if ($r) {
      $p = $r;
    }
  
    // validation errors
    $err = ($this->v_err) ? 1 : $err;
  
    // updating if no errors
    if (!$err) {
      $af = $this->update_row($p);
      if ($af <= 0) {
        $err = 1;
        $this->errm("Данные не изменились.");
      }
      else {
        $this->errm("Данные сохранены [".$std->time->format($sv->post_time, 0.5)."]");
      }
    }  
    
    $this->after_update($this->d, $p, $err);
    
  } 
  else {
    // иначе подготавливаем текущие значения для формы
    foreach ($this->active_fields as $fn) {
    	$this->vals[$fn] = $this->d[$fn];
    }
  }
      
  if ($err) $this->errs[] = __FUNCTION__;
  
  $ret['submited'] = $submited;
  $ret['v'] = $this->escape_vals();
  return $ret;
}

/**
 * автоматическая обработка insert(create) форм по списку полей заданных в write_in  для текущего кода
 *
 * 
 * @param boolean $fatal_validation - фатальная ошибка если влаидация для одного из полей не прописана
 * @return array('err_box' => , 'v' => 'submited' => 0/1, 'fields' => , 'insert_id' => , 'err' => , 'errm' => )
 */
function init_submit_create($fatal_validation = 1, $show_result_msg = 1) {
  global $sv, $std, $db;
  
  $err = 0;
  $p = array();
  $submited = false;
  $insert_id = 0;

  $n = (isset($sv->_post['new']))  // || (isset($sv->_get['submited']) && $sv->_get['submited']==1)
       ? $sv->_post['new'] : false;
  if ($n!==false) {
    $submited = true;
  }

  $this->n = $n;  
  $this->active_fields = $ret['fields'] = $this->get_active_fields('write');
  
  // если отправлена форма то вызываем по порядку стандартные процендуры обработки insert форм
  if ($submited) {
    
    $this->before_create();
    $err = ($this->err('before_create')) ? 1 : $err;
   
    // validation
    if (!$err) {      
      $p = $this->validate_active_fields($fatal_validation);
    }
    
    // predefined values
    foreach($this->predefined as $k=>$v) {
      if (!isset($p[$k])) {
        $this->vals[$k] = $p[$k] = $v;
      }
    }
  
    $r = $this->last_v($p);  
    if ($r) {
      $p = $r;
    }
      
    // validation errors
    $err = ($this->v_err) ? true : $err;
  
    // insert if no errors
    if (!$err) {
      $af = $this->insert_row($p);
      if ($af <= 0) {
        $err = 1;
        $this->errm("Ошибка базы данных, не удалось добавить данные.", $err);
      }
      else{
        $this->current_record = $insert_id = $db->insert_id();
        if ($show_result_msg) {        
          $this->errm("Запись добавлена [".$std->time->format($sv->post_time, 0.5)."]");
        }
        foreach ($this->vals as $k=>$v) {
          $this->vals[$k] = '';
        }
      }
    }  
    
    $this->after_create($p, $err);
   
  } 
  else {
    // иначе подготавливаем текущие значения для формы
    foreach ($this->active_fields as $fn) {
    	$this->vals[$fn] = $this->fields[$fn]['default'];
    }
  }
 
  if ($err) $this->errs[] = __FUNCTION__;
  
  $ret['err'] = $err;
  $ret['submited'] = $submited;
  $ret['insert_id'] = $insert_id;
  $ret['v'] = $this->escape_vals();
  
  return $ret;
}

/**
 * обработка данных для вывода в форме
 * вызывается в конце submit обработки (init_submit*)
 * @param array $vals
 * @return array
 */
function escape_vals($vals = false) {
  global $std;
  
  $ret = array();  
  $vals = (!$vals) ? $this->vals : $vals;
  foreach($this->vals as $k => $v) {
    $ret[$k] = $std->text->cut($v, 'replace', 'replace');
  }
  return $ret;
}

function escape_val($v) {
  global $std;  
  return $std->text->cut($v, 'replace', 'replace');
}
// validation ==================================

/**
 * Инициализация служебных полей, вызывается однократно при вызове инициализации обычных полей init_field
 * бывш: init_fields
 */
function init_embed_fields() {
  
  $this->init_fields = true;

  $this->init_field(array(
    'name' => $this->primary_field,
    'title' => 'Идентификатор',
    'type' => 'int',
    'len' => '11',
    'write_in' => array(),
    'show_in' => array('edit')
    ));
    
  $this->init_field(array(
    'name' => 'created_at',
    'title' => 'Дата создания',
    'type' => 'datetime',
    'show_in' => array('edit')    
  ));    
  $this->init_field(array(
    'name' => 'created_by',
    'title' => 'Created By',
    'type' => 'int',
    'search' => 0
  ));    
  
  $this->init_field(array(
    'name' => 'updated_at',
    'title' => 'Дата последнего редактирования',
    'type' => 'datetime',
    'show_in' => array('edit')    
  ));    
  $this->init_field(array(
    'name' => 'updated_by',
    'title' => 'Updated By',
    'type' => 'int',
    'show_in' => array(),
    'search' => 0    
  ));    
  
  $this->init_field(array(
    'name' => 'expires_at',
    'title' => 'Дата устаревания',
    'type' => 'datetime'    
  ));     
        
}

/**
 * Инициализаия массива active_fields и virtual_fields
 *
 * @param unknown_type $type [show|write]
 * @return unknown
 */
function get_active_fields($type='show') {
  global $sv;  
  
  $code = $type."_in";
  
  $ar = array();
  $this->virtual_fields = array();  
  foreach($this->fields as $d) {    
    if (isset($d[$code]) && in_array($this->code, $d[$code]))  {
      $ar[] = $d['name']; 
    }   
    if ($this->get_virtual($d['name'])!=$d['name']) {
      $this->virtual_fields[] = $d['name'];
    }
  }
  $this->active_fields = $ar;  
  $this->inited_virtual_fields = 1;
    
  return $ar;
}

/**
 * Инициализация виртуальных полей
 * оптимизированный дубликат в  get_active_fields 
 *
 */
function init_virtual_fields($force = 0) {
  if ($this->inited_virtual_fields && !$force) {
    return $this->virtual_fields;
  }
  
  $this->virtual_fields = array();
  foreach($this->fields as $d) {       
    if ($this->get_virtual($d['name'])!=$d['name']) {
      $this->virtual_fields[] = $d['name'];
    }
  }  
  $this->inited_virtual_fields = 1;
  return $this->virtual_fields;
}

/**
 * Комплексная валидация с вызовом подпроцедур v_
 * Значения полученные из формы лучше предварительно проверять: 
 * $n[$f] = (isset($this->n[$f])) ? $this->n[$f] : false; 
 * 
 * Основная задача первичная обработка и вызов подпроцедур
 *
 * @param string $field_name
 * @param mixed $val
 * @param boolean $fatal_validation - если установлена то при отсутсвии функции валидации
 * - скрипт завершается с ошибкой
 * @return mixed $val
 */
function validate_field($field_name, $val, $fatal_validation = 0) {
global $std, $sv;
  
  $f = $field_name; 
  if (!isset($this->fields[$f])) {
    die("field not found on validation <b>{$f}</b>");
  }
  $field = $this->fields[$f];

  // TYPE
  $type = $field['type'];
 
  
  $need_custom_validation = 0;
  
  switch($field['input']) {
    case 'checkbox':
      $val = ($val=='on') ? 1 : 0;
    break;
    case 'file':
      // filename
      $need_custom_validation = 1;
    break;
    case 'multiselect':
      // array, no action   
      $need_custom_validation = 1;
    break;     
    case 'custom':
      // mixed
      $need_custom_validation = 1;
    break;
    case 'time': case 'datetime':
      $type = 'time';
    break;
    case 'date': 
      $type = 'date';
    break;
  }
  
  
  switch($type) { // ?    
    case 'int': case 'integer':
      $val = intval($val);
    break;
    case 'bigint':
      $val =preg_replace("#[^0-9]#msi", "", $val);
      $val = ($val=='') ? 0 : $val;
    break;    
    case 'varchar':
      if (!$need_custom_validation) {
        $val = $std->text->cut($val, 'allow', 'mstrip');
      }
    break;
    case 'float': 
      $val = floatval($val);
    break;
    case 'double':
      $val = doubleval($val);
    break;
    case 'boolean':      
      $val = ($val==1) ? 1 : 0;   
    break;
    
    case 'date':
      $setcurrent = (isset($val['setcurrent']) && $val['setcurrent']=='on') ? 1 : 0;
      if (!checkdate($val['month'], $val['day'], $val['year']) 
          && !($val['month']=='00' && $val['day']=='00' && $val['year']=='0000')) {
        $this->errm("Неверный формат даты в поле <b>{$field['title']}</b>");
        $val = "0000-00-00";
      }
      else {
        $val = "{$val['year']}-{$val['month']}-{$val['day']}";
      }
            
      if ($setcurrent) {
        $val = date("Y-m-d", $sv->post_time);
      }      
     
    break;
    
    case 'datetime': case 'time':
      $setcurrent = (isset($val['setcurrent']) && $val['setcurrent']=='on') ? 1 : 0;
     
      array_walk($val, "intval");
      if (!checkdate($val['month'], $val['day'], $val['year']) 
          && !($val['month']=='00' && $val['day']=='00' && $val['year']=='0000')) {
        $this->errm("Неверный формат даты в поле <b>{$field['title']}</b>");
        $date = "0000-00-00";
      }
      else {
        $date = "{$val['year']}-{$val['month']}-{$val['day']}";
      }   
      
      $h = ($val['h']>=0 && $val['h']<=24) ? $val['h'] : 0;
      $m = ($val['m']>=0 && $val['m']<=60) ? $val['m'] : 0;
      $s = ($val['s']>=0 && $val['s']<=60) ? $val['s'] : 0;
            
      $time = "{$h}:{$m}:{$s}";      
      $val = "{$date} {$time}";
      
      if ($setcurrent) {
        $val = date("Y-m-d H:i:d", $sv->post_time);
      }
      
      if ($field['input']=='time') {
        $val = strtotime($val);
      }
    break;
    
    case 'text':
      if (is_array($val)) {
        foreach($val as $k=>$v) {
          if (is_array($v)) {
            foreach($v as $k2 => $v2) {
              $val[$k][$k2] = $std->text->cut($v2, 'allow', 'mstrip');
            }
          }
          else {
            $val[$k] = $std->text->cut($v, 'allow', 'mstrip');
          }
        }       
      }
      else {
        $val = $std->text->cut($val, 'allow', 'mstrip');
      }
    break;

    default:
      if (!$need_custom_validation) {
        $val = $std->text->cut($val, 'allow', 'mstrip');
      }
  }
  
  if ($field['not_null']) {
    $val = $this->ev_not_null($val, $field);
  }

  // validations
  $validation = "v_{$f}";
  if (method_exists($this, $validation)) {    
    $this->current_validation = $f; 
    $val = $this->$validation($val);    
  }
  else {   
    if ($need_custom_validation || $fatal_validation) {      
      die("No validation <b>{$this->name}</b>&rarr;<b>{$validation}</b> for <b>{$field['title']}</b>");
    }
    $this->no_validation[] = $f;
  }
     
  return $val;
}


function validate_unique($k, $v, $id=0) {
  
  if (!$this->unique($k,$v, $id)) {
    $this->v_err = 1;
    $this->errm("Такое значение поля <b>".strtolower($this->fields[$k]['title'])."</b> уже существует.", 1);
  }
  
}

/**
 * Валидация активных полей в сабмит контроллерах
 *
 * @param unknown_type $fatal
 * @return $p массив готовых отвалидированных полей
 */
function validate_active_fields($fatal = 0) {
  global $std;
  
  $p = array(); 
  
  $count = count($this->active_fields);
  for($i=0; $i<$count; $i++) { if ($i>300) die("бесконечный цикл: ".__FILE__." ".__LINE__);
    
    $f = $this->active_fields[$i];     
    
    // виртуальные поля не валидируем и не добавляем
    if (in_array($f, $this->virtual_fields)) continue;
    
    $n[$f] = (isset($this->n[$f])) ? $this->n[$f] : false;                  
    $r = $this->validate_field($f, $n[$f], $fatal); 
    
    if (!is_null($r)) {
      $this->p[$f] = $this->vals[$f] = $p[$f] = $r;
      if ($this->fields[$f]['unique']) {
        $this->validate_unique($f, $p[$f], $this->current_record);
      }             
    }
    $count = count($this->active_fields);   
  }   

  return $p; 
}

/**
 * select all key-fields to table
 * if found v_err = 1, v_errm[] = $msg
 *
 * @param array $keys - field names
 * @param array $p - data
 * @param unknown_type $msg to v_errm
 * 
 */
function validate_unique_many($keys, $p, $msg = "") {
  global $sv, $std, $db;
  
  $and = array();
  if (!is_array($keys) || !is_array($p)) {
    die("wrong parameters in model-><b>validate_unique_many(keys, p, msg)</b>");
  }
  
  $names = array();
  foreach($keys as $k) {
    $names[] = $this->fields[$k]['title'];
    $v = (isset($p[$k])) ? $p[$k] : "";
    $k = $db->esc($k);
    $v = $db->esc($v);
    $and[] = "`{$k}`='{$v}'";
  } 
  
  
  if (count($and)>0) {
    $msg = ($msg =='') ? "В базе уже существует запись с подобными значениями: ".implode(" + ", $names)."." : $msg;
        
    $q = "SELECT 0 FROM {$this->t} WHERE ".implode(" AND ", $and);
    $db->q($q, __FILE__, __LINE__);
    if ($db->nr()>0) {
      $this->v_err = 1;
      $this->errm($msg, 1);
    }
  }
}

function unique($k, $v, $id=0) {
  global $sv, $db;
  
  $id = intval($id);  

  $add = ($id>0) ? " AND `{$this->primary_field}`<>'{$id}'" : "";  
  
  $q = "SELECT 0 FROM {$this->t} WHERE `".addslashes($k)."`='".$db->esc($v)."' ".$add;
 
  $db->q($q, __FILE__,__LINE__); 
  
  $ret = ($db->nr()>0) ? 0 : 1;
  
  return $ret;
}

function validate_ext($filename, $ext_ar = false) {
  
  $ext_ar = ($ext_ar === false || !is_array($ext_ar)) ? $this->ext_ar : $ext_ar;
  
  // ext 
  if (preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $filename, $m)) {
    $name = $m[1];
    $ext = strtolower($m[2]);
  }
  else {
    $name = $filename;
    $ext = "";
  }

  $good = false; 
  
  foreach($ext_ar as $e)  {
    if ($e===$ext) {
      $good = true;
      break;
    }
  }
  
  if (!$good) {
    $this->v_err = 1;
    $this->errm("Неразрешенный для загрузки тип файла, 
    расширения \"<b>{$ext}</b>\" нет в списке: ".implode(", ", $this->ext_ar), $err);
    return false;
  }  
  if ($ext=='php' || $ext=='phtml' || preg_match("#php#msi", $ext)) {
    $this->v_err = true;
    $this->errm("Загрузка php скриптов запрещена из соображений безопасности.", $err);
    return false;    
  }
     
  return $ext;
}

function ev_not_null($val, $field) {
  
  $t = $val;
  switch ($field['type']) {
    case 'text': case 'varchar':
      $t = trim($t);
      if ($t=='') {
        $this->v_err = 1;
        $this->errm("Поле <b>{$field['title']}</b> обязательно для заполнения.", 1);
      }
    break;
    case 'int': case 'tinyint': case 'bigint': case 'float': case 'double':
      $t = intval($t);
      if ($t===0) {
        $this->v_err = 1;
        $this->errm("Поле <b>{$field['title']}</b> не может быть нулевым.", 1);
      }
    break;    
  }
  
  return $val;
}

// other ======================================
function get_sort($ar, $default_dir = 'asc') {
  global $sv;
  
  if (!is_array($ar) || count($ar)<=0) {
    return " id DESC ";
  }

  $default_order = $ar[0];
  $second_order = (isset($ar[1])) ? $ar[1] : "";
  
  $cookie_order = get_class($this)."_order";
  $cookie_dir = get_class($this)."_dir";
 
  $c_dir = (!isset($sv->_c[$cookie_dir])) ? $default_dir : $sv->_c[$cookie_dir];
  $c_dir = ($c_dir=='asc') ? 'asc' : 'desc';
  $x_dir = ($c_dir=='asc') ? 'desc' : 'asc';
  
  $c_order = (!isset($sv->_c[$cookie_order])) ? $default_order : $sv->_c[$cookie_order];
  $c_order = (in_array($c_order, $ar)) ? strtolower($c_order) : $default_order;
  
  if (isset($sv->_get['setsort']) && in_array($sv->_get['setsort'], $ar)) {
    $n_order = strtolower($sv->_get['setsort']);
    $n_dir = ($c_order==$n_order) ? $x_dir : $c_dir;
    
    setcookie($cookie_order, $n_order, time()+60*60*24*365);
    setcookie($cookie_dir, $n_dir, time()+60*60*24*365);
    
    $c_order = $n_order;
    $c_dir = $n_dir;    
  }
  

  $c_order = $this->get_virtual($c_order);
 
  $this->c_order = $c_order;
  $this->c_dir = $c_dir;
  
  $c_second = ($second_order!='') ? ", {$second_order} {$c_dir}" : '';
  $ret = "{$c_order} {$c_dir}{$c_second}";
  return $ret;
}

function get_virtual($f) {

  $keys = array_keys($this->fields);
  
  if (isset($this->fields[$f]['virtual']) && in_array($this->fields[$f]['virtual'], $keys)) {
    $ret = $this->fields[$f]['virtual'];
  }
  else {
    $ret = $f;
  }
  
  return $ret;
}

/**
 * @deprecated 
 * @use get_virtual
 */
function check_virtual($f) {
  return $this->get_virtual($f);
}

function is_virtual($f) {
  return ($this->get_virtual($f)!=$f) ? 1 : 0;  
}

function add2roaster($name, $val) {
  $val =  (get_magic_quotes_gpc()) ? addslashes($val) : $val;  
  if (!in_array($name, $this->active_fields)) {
    $this->active_fields[] = $name;
  }
  $this->n[$name] = $val;
}

function remove_from_roaster($name) {  
  
  
  $f = &$this->fields[$name];
  $write_in = array();
  foreach($f['write_in'] as $code) {
    if ($this->code!=$code) {
      $write_in[] = $code;
    }
  }
  $show_in = array();
  foreach($f['show_in'] as $code) {
    if ($this->code!=$code) {
      $show_in[] = $code;
    }
  }
  $f['write_in'] = $write_in;
  $f['show_in'] = $show_in;
   
  
  $t = array();
  foreach ($this->active_fields as $f) {
    if ($f!=$name) $t[] = $f;
  }
  $this->active_fields = $t;  
}

// utils
function time2n($time) {
  

  $ret = array(
    'month' => date("m", $time),
    'day' => date("d", $time),
    'year' => date('Y', $time),
    'h' => date("H", $time),
    'm' => date("i", $time),
    's' => date("s", $time)  
    );
  
  return $ret;
}

/**
 * распечатка модели
 *
 * @param unknown_type $force
 */
function trace($force = 0) {
  global $trace_ips, $sv;
  
  if ($force) {
    if (!is_array($trace_ips)) {
      $trace_ips = array();
    }
    $trace_ips[] = $sv->ip;
  }
  
  $t = $this;
  unset($t->fields);
  unset($t->upload_err_codes_eng);
  unset($t->upload_err_codes);
  t($t, 30);
  unset($t);
}

/**
 * Была ли ошибка в функции?
 *
 * @param unknown_type $func
 * @return unknown
 */
function err($func) {
  foreach($this->errs as $f) {
    if ($func==$f) return true;
  }
  return false;
}

/**
 * стандартные триггер создания ссобщения
 *
 * @param unknown_type $text
 * @param unknown_type $type
 */
function errm($text, $err = 0, $time = 0, $microtime = 0) {
  global $sv;
  $ar = array();
  $ar['err'] = $err;
  if ($time) {
    $ar['time'] = $sv->date_time;
  }
  if ($microtime) {
    $ar['microtime'] = microtime(1);
  }
  
  if (is_null($text)) {
    return false;
  }
  elseif (is_array($text)) {    
    foreach($text as $t) {
      $ar['text'] = $t;
      $sv->msgs[] = $ar;
      $sv->msgs_count++;
      $this->errm[] = $t;
    }
  }
  else {
    $ar['text'] = $text;
    $sv->msgs[] = $ar;
    $sv->msgs_count++;
    $this->errm[] = $text;
  }
  
}
/**
 * Добавлет в общий стек собщщений указанный массив сообщений
 *
 * @param unknown_type $textst_ar
 * @param unknown_type $err
 */
function errm_merge_simple($texts_ar, $err = 0, $reverse = 0) {
  if ($reverse) { $texts_ar = array_reverse($texts_ar);} 
  foreach($texts_ar as $t) {
    $this->errm($t, $err);
  }
}
/**
 * @deprecated
 *
 * @param unknown_type $t
 */
function errm_push($t) {
  if (is_array($t)) {    
    $this->v_errm = array_merge($this->v_errm, $t);
  }
  else {
    $this->v_errm[] = $t;
  }
}

/**
 * Writes log to this->log_file
 *
 * @param unknown_type $t
 */
function log($t) {
  global $sv;
  
  $str = "[{$sv->date_time} | {$sv->ip} | {$sv->act}_{$sv->code}] {$t}\n";
  if ( ($this->verbose==1 && $sv->debug) || $this->verbose==2) {
    echo $str;
  }
  
  if ($this->verbose>=0) {
    // write_to_file
    $fn = $this->log_file;
    $h = fopen($fn, "a");
    fwrite($h, $str);
    fclose($h);
  }
  
}

// post actions

/**
 * мусоросбощик с проверкой на ошибки
 *
 * @param unknown_type $d
 * @return unknown
 */
function garbage_collector($d) { }

function after_create($p, $err) { 
  if (!$err) {
    $p[$this->primary_field] = $this->current_record;
    $this->after_change($p);
  }
}

function after_update($d, $p, $err) { 
  if (!$err) {    
    $this->after_change($p);
  }
}

function after_remove($d, $err) { 
  if (!$err) {    
    $this->after_change($d);
  }
}

/**
 * обобщаяющий метод объединяющий after - create, update, remove методы
 * вызываемый в случае успешного изменения таблицы
 *
 * @param unknown_type $d - новые параметры записи
 */
function after_change($d) { }

function before_scaffold() { }
function before_default() { }
function before_create() { }
function before_edit() { }
function before_update() { } 
function before_remove() { }

function v_before_remove($d) { }

function last_v($p) {
  return $p;
}

// Parsers ======================
function get_and_parse_virtual_fields($d) {
    
  foreach($this->virtual_fields as $vf) {
  
   
    $f = $this->fields[$vf]['virtual'];
    
    $target = $this->fields[$f];
    
    if (isset($target['belongs_to']) && $target['belongs_to']!=false ) {      
      $b = $this->fields[$f]['belongs_to'];
      $k = array_keys($b);         
      //parsing component LIST
      if ($k[0]=='list') {
        $d[$vf] = $b['list'][$d[$f]];      
      }      
    }
    else {      
      $d[$vf] = $d[$f];
      
     
    }
  }
 
  return $d;
}

/**
 * Standart parser for multi-value field, like cats = "x,y,z"
 *
 * @param unknown_type $str
 */
function parse_multi_value($str) {
  $ar = explode(",", $str);
  $ret = array();
  foreach($ar as $v) {
    $v = trim($v);
    if ($v!='') {
      $ret[] = $v;
    }
  }  
  return $ret;
}


// DB select patterns =========================
/**
 * автроенный парсинг данных из базы, рекомендуется вызывать при любой выборке
 *
 * @param unknown_type $d
 * @return unknown
 */
function e_parse($d) {
  
  // хак для нестандартных названий ключевого поля
  $d['id'] = $d[$this->primary_field];
  
  return $d;
}

function parse($d) {
  return $d;
}

function item_list($wh = "", $ord = "", $lim = 10, $parse=0, $idkeys = 0) {
  global $db;
  
  $where = ($wh=='') ? "" : "WHERE {$wh}";
  $orderby = ($ord=='') ? "" : "ORDER BY {$ord}";
  
  $lim = intval($lim);
  $limit = ($lim>0) ? " LIMIT 0, {$lim}" : "";
  
  $ar = array();
  
  if ($this->use_joins) {
    $jf = $this->joins['f'];
    $jj = $this->joins['j'];
  }
  else {
    $jf = $jj = "";
  }
  
  $res = $db->q("SELECT {$this->t}.*{$jf} FROM {$this->t} {$jj} {$where} {$orderby} {$limit}", __FILE__, __LINE__);
  $this->last_rows_count = $db->nr();
  while($d = $db->f($res)) {
    $d = $this->e_parse($d);
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
    }
    if ($idkeys) {
      $ar[$d[$this->primary_field]] = $d;
    }
    else {
      $ar[] = $d;
    }
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  
  return $ret;
}

/**
 * Item list with pagelist
 *
 * @param unknown_type $wh
 * @param unknown_type $ord
 * @param unknown_type $lim
 * @param unknown_type $page
 * @param unknown_type $parse
 * @param unknown_type $url
 * @param unknown_type $addon
 * @return unknown
 */
function item_list_pl($wh = "", $ord = "", $lim = 10, $page = 1, $parse=0, $url="", $addon="", $idkeys = 0) {
  global $db, $std, $sv;
  
  $where = ($wh=='') ? "" : "WHERE {$wh}";
  $orderby = ($ord=='') ? "" : "ORDER BY {$ord}";
  
  $lim = intval($lim);
  $page = intval(abs($page));
  $url = ($url=='') ? u($sv->act, $sv->code, $sv->id) : $url;
  
  $page = ($page<=0 && isset($sv->_get['p'])) ? intval($sv->_get['p']) : $page;
  $page = ($page<=0 && isset($sv->_get['page'])) ? intval($sv->_get['page']) : $page;
  
  $limit = ($lim>0) ? " LIMIT 0, {$lim}" : "";
  
  $db->q("SELECT 0 FROM {$this->t} {$where} ", __FILE__, __LINE__);
  $ret['pl'] = $pl = $std->pl($db->nr(), $lim, $page, $url, $addon);
  
  
  if ($this->use_joins) {
    $jf = $this->joins['f'];
    $jj = $this->joins['j'];
  }
  else {
    $jf = $jj = "";
  }
    
  $ar = array();
  $res = $db->q("SELECT {$this->t}.*{$jf} FROM {$this->t} {$jj} {$where} {$orderby} {$pl['ql']}", __FILE__, __LINE__);
  $this->last_rows_count = $db->nr();  
  $i = $pl['k'];
  while($d = $db->f($res)) { $i++;
    $d['i'] = (!key_exists('i', $d)) ? $i : $d['i'];
    $d = $this->e_parse($d);
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
    }
    if ($idkeys) {
      $ar[$d[$this->primary_field]] = $d;
    }
    else {
      $ar[] = $d;
    }
  }
  
  $ret['list'] = $ar;
  $ret['count'] = count($ar);
  
  return $ret;
}

/**
 * Синоним item_list_pl с более логичым порядком параметров, как в item_list
 *
 * @param unknown_type $wh
 * @param unknown_type $ord
 * @param int $lim
 * @param boolean $parse
 * @param int $page
 * @param string $url
 * @param string $addon
 * @return unknown
 */

function item_list_pls($wh = "", $ord = "", $lim = 10, $parse=0, $page = 1, $url="", $addon="") {
  return $this->item_list_pl($wh, $ord, $lim, $page, $parse, $url, $addon);
}

/**
 * Get item by id
 *
 * @param unknown_type $id
 * @param unknown_type $parse
 * @return unknown
 */
function get_item($id, $parse = 0, $update_current = 1) {
  global $sv, $db;
  
  $id = intval($id);
  $db->q("SELECT * FROM {$this->t} WHERE `".$this->primary_field."`='{$id}'", __FILE__, __LINE__);
  $this->last_rows_count = $db->nr();
  if ($this->last_rows_count > 0) {
    $d = $db->f();
    $d = $this->e_parse($d);
    
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
    }
    if ($update_current) {
      $this->current_record = $d[$this->primary_field];
      $this->d = $d;
    }    
    $ret = $d;
  }
  else {
    $ret = false;
  }
  return $ret;
  
}

/**
 * get item where 
 *
 * @param unknown_type $where
 * @param unknown_type $parse
 * @return unknown
 */
function get_item_wh($where, $parse = 0, $update_current = 1) {
  global $db;
  
  $where = ($where!='') ? "WHERE {$where}" : "";
  $order = (!preg_match("#order\ by#si", $where)) ? "ORDER BY `{$this->primary_field}` DESC" : "";
  $limit = (!preg_match("#limit#si", $where)) ? "LIMIT 0, 1" : "";
  
  $db->q("SELECT * FROM {$this->t} {$where} {$order} {$limit}", __FILE__, __LINE__);

  $this->last_rows_count = $db->nr();
  if ($this->last_rows_count > 0) {
    $d = $db->f();
    $d = $this->e_parse($d);
    if ($parse && method_exists($this, "parse")) {
      $d = $this->parse($d);
    }
    if ($update_current) {
      $this->current_record = $d[$this->primary_field];
      $this->d = $d;
    }
    $ret = $d;
  }
  else {
    $ret = false;
  }
  return $ret;
  
}

/**
 * Select num rows by where param
 *
 * @param unknown_type $where
 * @return int nr
 */
function select_count_wh($where='') { 
  global $db;
   
  $where = ($where!='') ? "WHERE {$where}" : "";
  
  $db->q("SELECT 0 FROM {$this->t} {$where}", __FILE__, __LINE__);
  $this->last_rows_count = $db->nr();
  
  return $this->last_rows_count;
}

function count() {
  return $this->select_count_wh();
}
function count_wh($where) {
  return $this->select_count_wh($where);
}

// PAGE LIST =================================
/**
 * Pagelist parser by page_count
 *
 * @param unknown_type $page_count
 * @param unknown_type $current
 * @param unknown_type $lim
 * @param unknown_type $pad
 * @return array of parsed elements
 * 
 * Preview:   <- LEFT_PAD ... LEFT (CURRENT) RIGHT ... RIGHT_PAD ->
 */
function pl_by_num($page_count, $current, $lim = 6, $pad = 2) {
  
  $ost = $lim - $pad;    
  
  $left = 1;
  $right = $page_count;
  
  $left_pad = $pad;
  $right_pad = $right - $pad + 1;
  
  $left_ost = $current - $ost;
  $right_ost = $current + $ost + 1;
  
  
  $ar = array();
  
  /* <- (1|left)#left_pad#(left_pad) 
    ... (current-ost|left_ost)#left_ost#(current)#right_ost#(current+ost|right_ost) ... 
    (right_count-pad+1|right_pad)#right_pad#(page_count|right) ->
  */
  
  $left_pad_exists = ($left_pad < $left_ost - 1 ) ? 1 : 0;
  $right_pad_exists = ($right_pad > $right_ost) ? 1 : 0;
  
  if ($current>$left) {
    $i = $current - 1;
    $ar[] = array('page' => $i, 'code'=>'left', 'title' => "Previous");    
  }
  
  // left_pad  
  for($i = $left; $i <= $left_pad; $i++) {
    if ($i < $left_ost) {
      $ar[] = array('page' => $i, 'code'=>'num', 'title' => $i);      
    }
  }  
  if ($left_pad_exists && $left_pad < $left_ost) {
    $ar[] = array('page' => 0, 'code'=>'pad', 'title' => "...");
  }  
  //left_ost
  for ($i = $left_ost; $i<$current; $i++) {
    if ($i>0) {
      $ar[] = array('page' => $i, 'code'=>'num', 'title' => $i);
    }
  }
  $ar[] = array('page' => $current, 'code'=>'current', 'title' => $current);
  // right_ost
  for($i = $current+1; $i < $right_ost; $i++) {
    if ($i<=$right) {
      $ar[] = array('page' => $i, 'code'=>'num', 'title' => $i);  
    }
  }
  if ($right_pad_exists && $right_pad > $right_ost) {
    $ar[] = array('page' => 0, 'code'=>'pad', 'title' => "...");
  }
  //right_pad
  for($i = $right_pad; $i<=$right; $i++) {
    if ($i >= $right_ost) {
      $ar[] = array('page' => $i, 'code'=>'num', 'title' => $i); 
    }
  }
  if ($current<$right) {
    $i = $current + 1;
    $ar[] = array('page' => $i, 'code'=>'right', 'title' => "Next");    
  }
  
  return $ar;
}


// Связи между таблицами 

/**
 * Стандртное форматирование возвращаемого поля (можно заменять)
 *
 * @param unknown_type $d - запись из связываемой таблицы, поле по умолчанию которое нужно вернуть
 * @param unknown_type $return_field
 * @return unknown
 */
function belongs_return_format($d, $return_field='') {
  
  $ret = (isset($d[$return_field])) ? $d[$return_field] : $d[$this->primary_field];
  
  return $ret;
}


/**
 * Создание слектора из belongs_to
 *
 * @param unknown_type $b - $field[belongs_to] variable
 * @param unknown_type $f - field name
 * @param unknown_type $val - current value
 * @return unknown - html formatted selector
 */
function belongs_to_select($b, $f, $val, $multi = 0, $select = 1) {
  global $db, $sv, $std;  
  
  $this->active_field = $f;
  
  if ($b===false) {
    return "BELONGS_TO(table => string, field=>string, return=>string || list=>array) не установлен";
  }
  

  $b_keys = array_keys($b);
  $field = $this->fields[$f];
  
  $ar = array();
 
  switch($b_keys[0]) {
    case 'table':
      
      $table = (isset($sv->t[$b['table']])) ? $sv->t[$b['table']] : $b['table'];
    
      // обработка дополнительных условий выборки из зависимой таблицы
      // формат 'belongs_to' => array('table'..., 'where' => array(array('remote' => 'bid', 'operator'=> '=', 'local' => 'bid' | 'value'=> 'x'))),
      if (isset($b['where']) && is_array($b['where']) && count($b['where'])>0 && $this->code!='default') {
        $wh = array(); 
        foreach($b['where'] as $w) {
          $w['value'] = (isset($w['value'])) ? $w['value'] : '';
          $cval = (isset($w['local']) && isset($this->d[$w['local']])) ? $this->d[$w['local']] : $w['value'];
          $wh[] = "`{$w['remote']}`{$w['operator']}'{$cval}'";
        }
        $where = "WHERE ".implode(" AND ", $wh);
      }
      else {
        $where = "";
      }      
     
      $orderby = ($b['order']!='') ? $b['order'] : "`{$b['return']}` ASC";
      $db->q("SELECT * FROM {$table} {$where} ORDER BY {$orderby}", __FILE__, __LINE__);
      while ($d = $db->f()) {
        $ar[$d[$b['field']]] = $d;       
      }
      foreach($ar as $k => $d){
        $ar[$k] = $this->belongs_return_format($d, $b['return']);   
      }
      
    break;
    case 'list':
      $ar = $b['list'];      
    break;
    
  }
  
  $keys = array_keys($ar);
   
  $opts = array();
  if (isset($b['null']) && $b['null']) {
    $opts[] = "<option value=''>не выбрано</option>";    
  }
    
  if ($multi) {
    // получаем массив выбранных значений
    $values = $this->parse_multi_value($val);  
    $values = (is_array($values)) ? $values : array();
  }
  else {
    // проверяем текущей значение в возможных, если отсутствует то сбрасываем    
    $val = (!in_array($val, $keys)) ? $field['default'] : $val;
  }
  
  foreach ($ar as $k=>$v) {
    if ($multi) {
      $s = (in_array($k, $values)) ? " selected" : "";
    }
    else {
      $s = ($k==$val) ? " selected" : "";
    }
    if (!$select) {
      $s = "";
    }
    $len = 100;
    $v = (strlen($v)>$len) ? $std->text->truncate($v, $len) : $v;
    $opts[] = "<option value='{$k}'{$s}>{$v}</option>";    
  }
  
  $this->last_opts = $opts;
  
  if ($multi) {
    $size = (count($opts)>10) ? 10 : count($opts);
    $ret = "<select name='new[{$f}][]' size='{$size}'  multiple='multiple'>".implode("\n", $opts)."</select>    
    <br><small style='color:gray;'>с нажатой кнопкой Ctrl можно снять выделение либо выбрать несколько вариантов</small>"; 
  }
  else {
    $ret = "<select name='new[{$f}]'>".implode("\n", $opts)."</select>";
  }

  return $ret;
}


/**
 * Belongs to view (parse val)
 *
 * @param unknown_type $b - belongs info
 * @param unknown_type $f - fieled name
 * @param unknown_type $val - current value
 * @return parsed val
 */
function belongs_to_view($b, $fname, $val) {
  global $sv, $std, $db;
  
  if ($b===false) {
    return $val;
  }

  $b_keys = array_keys($b);
  $field = $this->fields[$fname];
  
  if ($field['input']=='multiselect') return $val;
  
  $ar = array();
 
  switch($b_keys[0]) {
    case 'table':      
     
      $table = $sv->t[$b['table']];
      
      $db->q("SELECT * FROM {$table} WHERE `{$b['field']}`='".$db->esc($val)."'", __FILE__, __LINE__);
      if ($db->nr()>0) {
        $d = $db->f();
        $ret = $this->belongs_return_format($d, $b['return']);   //$ret = $d[$b['return']];
      }
      else {
        $ret = "запись {$val} не найдена в таблице {$table}";
      }
      
    break;
    case 'list':
      $ret = $b['list'][$val];      
    break;
    
  }

   
   
  return $ret;
}

/**
 * Оределение основных параметров связи
 *
 * @param unknown_type $belongs_to
 * @return array || false
 */
function parse_belongs_to($d, $field) {
  
  $modes = array('normal', 'master', 'slave');
  $types = array('table', 'list');  
  $err = 0;
  $errm = "";

  if (!is_array($d) || count($d)<=0) {
    return false;
  }

  $keys = array_keys($d);
  $type = $keys[0]; 
  
  if (!in_array($type, $types)) {       
    $err = 1;
    $errm = "first argument not in array(".implode(", ", $types).")";
    $type = $types[0];
  }  
  
  $d['type'] = $type;    
  $d['mode'] = (isset($d['mode']) && in_array($d['mode'], $modes)) ? $d['mode'] : $modes[0];  
  $d['act'] = (!isset($d['act'])) ? $d[$type] : $d['act'];  
  $d['order'] = (!isset($d['order'])) ? '' : $d['order'];
  
  // type requirements
  if (!$err && $d['type']=='table') {
    if (!isset($d['field']) || !isset($d['return']))  {    
      $err = 1;
      $errm = "type=table required vars: field, return";
    }
    else {
      $d['var'] = $d['table']."_".$d['field'];
    }
  }
    
  // slave mode checks
  if (!$err && $d['mode']=='slave' && $type!='table') {
    $err = 1;
    $errm = "mode=slave required type=table";
  }
      
  if (!$err && $d['mode']=='slave') {
    $this->slave_mode = 1;
    $this->slave_fields[] = $field['name'];
  }

  if ($err) {
    die("PARSE_BELONGS_TO: ".$errm." [{$this->name}&rarr;{$field['name']}]");    
  }
   
  return $d;
}


// SLAVE statements  ==========================
/**
 * Inits and checks ALL slave requrements, 
 * slave_mode defined in INIT_FIELD
 *
 */
function slave_init() {
  
  if (!$this->slave_mode) {
    return false;
  }
  
  $err = 0;
  $errm = array();
  
  // check all slave fields
  foreach($this->slave_fields as $name)  {
    $r = $this->slave_init_field($name);
    $err = ($r['err']) ? 1 : $err;
    $errm = array_merge($errm, $r['errm']);
  }
  
  if ($err) {
    foreach($errm as $m) {
      echo "{$m}<br>";
    }
    exit();
  }
  
  
}

function slave_init_field($name) {
  global $sv, $std, $db;
  
  $err = 0;
  
  $f = $this->fields[$name];
  $b = $f['belongs_to'];
  $table = $sv->t[$b['table']];

  
  // check request
  if (!isset($sv->_get[$b['var']])) {
    $err = 1;
    $this->errm("Не задан параметр <b>{$b['var']}</b>.", $err);
  }
  else {
    $var = $std->text->cut($sv->_get[$b['var']], 'allow', 'mstrip');
    $var = $this->validate_field($name, $var);    
  }
  
  // set required parameters
  if (!$err) {
    $this->where[$name] = "`{$name}`='".$db->esc($var)."'";
    $this->predefined[$name] = $var;
    $this->slave_url_vars[$b['var']] = $var;
    
    $this->update_slave_url_addon();
  } 

  // check existance
  if (!$err) {
    $db->q("SELECT * FROM {$table} 
            WHERE {$b['field']}='".$db->esc($var)."' 
            ORDER BY id DESC LIMIT 0, 1", __FILE__, __LINE__);
    if ($db->nr()>0) {
      $master = $db->f();
      $this->master[$name] = $master;
      $this->master_info[$name] = $this->compile_master_info($name, $master, $b['act']);
    }
    else {
      $err = 1;
      $this->errm("Запись \"".$std->text->cut($var, 'replace', 'replace')."\" не найдена в таблице <b>{$table}</b>.", $err);
    }
  }
  return array('err' => $err, 'errm' => $errm);
}

function update_slave_url_addon() {
  global $std;
  
  $vars = array(); $inputs = array();
  foreach($this->slave_url_vars as $k=>$v) {
    $vars[] = "{$k}=".urlencode($v);
    $inputs[] = "<input type='hidden' name='{$k}' value='".$std->text->cut($v, 'replace', 'replace')."'>";
  }
  
  $ret = "&".implode("&", $vars);
  $this->slave_url_addon = $ret;
  $this->hidden_inputs = implode("", $inputs);
  
  return $ret;
}

function compile_master_info($name, $master, $act="") {
  global $sv, $std, $db;
  
  
  $f = $this->fields[$name];
  $b = $f['belongs_to'];

  $act = ($act=='') ? $b['table'] : $act;
  $method = "master_info_{$name}";

  $master['_url'] = u($act, 'edit', $master[$this->primary_field]);
  $master['_title'] = $master[$b['return']];
  
  if (method_exists($this, $method)) {
    $ret = $this->$method($master, $act);
  }
  else {    
    $ret = $this->_master_info($master, $act);
  }
  return $ret;
}

/**
 * Standart master block for slave
 *
 * @param unknown_type $d = master data
 * @param unknown_type $act = action for urls
 * @return string $html
 */
function _master_info($d, $act) {
  global $sv, $std, $db;
  
  $ret = "
    <strong><a href='{$d['_url']}'>{$d['_title']}</a></strong>
  ";
  
  return $ret;
}

// MASTER statements  ========================== 
/**
 * @example SLAVE_BOX(slave_model_name, slave_field, slave_act, object) in virtual field with object 
 * @return formatted html table with slave items and edit urls.
 * 
 * @uses get_slave($slave_model_name, $slave_field, $slave_act) - load slave model and define slave vars
 *       slave_items($slave, $slave_field, $object)             - return standart item_list [custom allowed]
 *       slave_format_list($slave, $items)                      - returns formatted html code (table)  [custom allowed]
 *       slave_parse_item($slave, $d)                           - parse slave row, adds edit/remove urls
 */
function slave_box($slave_model_name, $slave_field, $slave_act, $object) {
  global $sv, $std, $db;
  
  $slave = $this->get_slave($slave_model_name, $slave_field, $slave_act);
  $items = $this->slave_items($slave, $slave_field, $object);
  $list  = $this->slave_format_list($slave, $items);
  
  $ret = "
  <table cellpadding=3>  
  <tr><tD>
    <b><a href='{$slave->vars['list_url']}' onclick=\"if(!confirm('Вы уверены что хотите перейти к редактированию зависимого списка? Все несохраненные настройки сделанные в этом окне будут утеряны. Если вы изменяли какие то значения, сохраните предварительно все изменения нажав кнопку - Сохранить и прожолжить редактирование.')) return false;\">Редактировать список</a></b> &nbsp; 
    
    <a href='{$slave->vars['create_url']}' target=_blank>Создать запись</a>&nbsp;[в новом окне]
  
  </td></tr>
  <tr><td>
    {$list}
  </td></tr>
  </table>
  ";
  
  return $ret;
}

function get_slave($model_name, $field_name, $act) {
  global $sv, $std, $db;
  
  if (isset($this->slaves[$model_name]) && is_object($this->slaves[$model_name])) {
    return $this->slaves[$model_name];
  }
  
  $sv->load_model($model_name);
  
  // links
  $slave = &$sv->m[$model_name];
  $this->slaves[$model_name] = &$sv->m[$model_name];
  
  if (!isset($slave->fields[$field_name])) {
    die("{$model_name}->fields[{$field_name}] not exists");
  }
  
  $f = $slave->fields[$field_name];
  $b = $f['belongs_to'];
  
  if ($b['mode']!='slave') {
    die("<b>{$model_name}->fields[{$field_name}][belongs_to][mode] = slave</b> - required");
  }
 
  $add = "&{$b['var']}=".urlencode($this->d[$b['field']]);
   
  $slave->slave_act = $act;
  $slave->slave_url_addon = $add;
  $slave->vars['list_url'] = u($act, 'default').$add;
  $slave->vars['create_url'] = u($act, 'create').$add;
  
  return $slave;
}

function slave_items($slave, $field_name, $object) {
  global $sv, $std, $db;
  
  $items = $slave->item_list("`{$field_name}`='".$db->esc($object)."'", "id ASC", 0, 1);
  foreach($items['list'] as $k => $d) {
    $items['list'][$k] = $this->slave_parse_item($slave, $d);
  }
  return $items;
}

function slave_parse_item($slave, $d) {
  
  $d['edit_url'] = u($slave->slave_act, 'edit', $d[$this->primary_field]).$slave->slave_url_addon;
  $d['remove_url'] = u($slave->slave_act, 'remove', $d[$this->primary_field]).$slave->slave_url_addon;
    
  return $d;
}

function slave_format_list($slave, $items) {
  global $std, $sv;
  
  $tr = array(); $i = 0;
  foreach($items['list'] as $k=>$d) { $i++;
    $t = ($d['title']!='') ? $d['title'] : "<span style='color: red;'>без названия</span>";
    $tr[] = "<tr>
      <td align=right>{$i}.</td>
      <td><a href='{$d['edit_url']}' target=_blank>{$t}</a></td>
      <td align=right><a href='{$d['remove_url']}' target=_blank>удалить</a></td>
      </tr>";
  }
  
  if (count($tr)<=0) {
    $tr[] = "<tr><td><i>Список пуст.</i></td></tr>";
  }
  
  $ret = "
  <table cellpadding=3 width='100%'>
  ".implode("", $tr)."
  </table>  
  ";
  
  return $ret;  
}
  
// ЗАГРУЗКА ФАЙЛОВ
/**
 * Встроенная валидация файлов
 * вызывается в обычной валидации с параметрами
 *
 * @param unknown_type $requred
 * @return unknown
 */
function ev_file($required = 0) {
  global $sv, $std, $db;
  
  $err = 0;
  $name = $this->current_validation;
  $dir = $this->uploads_dir;

  
  // текущее значение поля
  $c_file = (isset($this->d[$name])) ? $this->d[$name] : "";
  
  // если есть ошибки валидации то даже не пробуем загружать
  if ($this->v_err) return $c_file;
        
  if (!$err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $dir, 0);   
    
    // если не указан, текущий пуст и обязателен, то пишем что обязательно
    if ($file===false && $c_file=='' && $required) {
      $err = 1;
      $this->errm("Не указан файл в поле: ".$this->fields[$name]['title'], $err);
      return "";
    }
    elseif ($file===false) {
      // не указан
      return $c_file;
    }
    $err = ($file['err']) ? true : $err;
    $this->errm($file['errm'], $err);
  }  
  
  if (!$err) {
    // hook for spec file validations
    $before_upload = "before_upload_{$name}";
    if (method_exists($this, $before_upload)) {
      $r = $this->$before_upload($file);
      $err = ($r['err']) ? 1 : $err;
      $this->errm($r['errm'], $err);
    }    
  }
  
  
  if (!$err) {   
    // удаляем старый если был
    $r = $this->ev_file_remove($c_file);
    $err = ($r['err']) ? 1 : $err;
    $this->errm($r['errm'], $err);
    if (!$err) {
      $c_file = "";
    }
  }
  
  if (!$err) {  
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
      $this->errm($r['errm'], $err);
      $this->errm("Файл <b>{$file['savename']}</b> успешно загружен.", $err);  
      
      // hook for spec resizes, etc
      $on_upload = "on_upload_{$name}";
      if (method_exists($this, $on_upload)) {
        $this->$on_upload($file);
      }
      
      // если указано создаем ресайз
      if ($this->uploads_make_resize) {
        $r = $this->ev_file_resize($file['savename']);
        $this->errm($r['errm'], $err);
      }
    }
    else {     
      $this->errm("Не удалось переместить файл из временной папки: 
      {$file['tmp_name']} &rarr; {$file['savepath']}", $err);   
      
      if ($required && $c_file=='')   {
        $this->v_err = 1;
      }
      return $c_file;
    }
  }
  else {
    $this->errm("Ошибка работы с файлами сообщите администратору.", $err);
    if ($required && $c_file=='')   {
      $this->v_err = 1;
    }    
    return $c_file;
  }
 
  return $file['savename'];  
}

/**
 * Удаление файла и ресайза (если есть) по имени файла
 *
 * @param unknown_type $filename
 * @return unknown
 */
function ev_file_remove($filename) {
  global $sv;
  
  $err = false;
  
  if ($filename=='') {    
    return false;
  }
  
  $dir = $this->uploads_dir;  
  
  $path = $dir.$filename;
  if (!file_exists($path)) {
    $this->errm("Файл не найден: <b>{$path}</b>", $err);
  }
  else {
    if (!unlink($path)) {
      $err = 1;
      $this->errm("Не удалось удалить файл: <b>{$path}</b>", $err);
    }
    else {
      $this->errm("Файл <b>{$path}</b> удален.", $err);
    }
  }
  
  if ($this->uploads_make_resize && !$err) {
    $path = $dir.$this->ev_file_resizename($filename);
    if (!file_exists($path)) {
      $this->errm("Ресайз не найден: <b>{$path}</b>", $err);
    }
    else {
      if (!unlink($path)) {        
        $this->errm("Не удалось удалить ресайз: <b>{$path}</b>", $err);
      }
      else {
         $this->errm("Ресайз <b>{$path}</b> удален.", $err);
      }
    }    
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
  return $err;
}

/**
 * встроенный обработчик вызывающий удаление файла
 * вставлется в before_update
 *
 * @param unknown_type $fieldname - имя поля
 */
function ev_init_file_remove($fieldname = '', $input_name='remove_file') {
  global $db, $sv;  
  
  $err = 0;
  if (!isset($this->d[$fieldname])) return false;
  $file = $this->d[$fieldname];
  
  if (isset($this->n[$input_name]) && $this->n[$input_name]=='on' && $file!='') {
    $r = $this->ev_file_remove($file);    
    $db->q("UPDATE {$this->t} SET `{$fieldname}`='' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d[$fieldname] = '';    
    $this->errm($r['errm'], $err);
  }
  
  return true;
}


/**
 * Создание стандартного ресайза на основе имени файла
 *
 * @param unknown_type $filename
 * @return unknown
 */
function ev_file_resize($filename) {
  global $std, $db, $sv;
  
  $err = false;
  
  $src = $this->uploads_dir.$filename;
  $target = $this->uploads_dir.$this->ev_file_resizename($filename);
  if (!file_exists($src)) {
    $err = 1;
    $this->errm("Исходный файл для ресайза не найден: {$src}", $err);    
  }
    
  
  if (!$err) {
    $std->resize->verbose = 0;
    
    switch($this->uploads_resize_type) {
      case 'by_width':
        if (!$std->resize->auto_by_width($src, $target, $this->uploads_w)) {
          $this->errm("Не удалось создать предпросмотр по ширине {$this->uploads_w}px
                     для <b>{$src}</b> &rarr; <b>{$target}</b>, сообщите администратору.", $err);
          $this->errm($std->resize->last_session, $err);
        }
      break;
      case 'by_height': // in future
      case 'fixed': default:
        if (!$std->resize->auto_fixed($src, $target, $this->uploads_w, $this->uploads_h)) {
          $this->errm("Не удалось создать фиксированный предпросмотр {$this->uploads_w}x{$this->uploads_h}
                     для <b>{$src}</b> &rarr; <b>{$target}</b>, сообщите администратору.", $err);
          $this->errm($std->resize->last_session, $err);
        }
      break;
      case 'fixed_nocrop':
        if (!$std->resize->auto_fixed_nocrop($src, $target, $this->uploads_w, $this->uploads_h)) {
          $this->errm("Не удалось создать фиксированный предпросмотр nocrop {$this->uploads_w}x{$this->uploads_h}
                     для <b>{$src}</b> &rarr; <b>{$target}</b>, сообщите администратору.", $err);
          $this->errm($std->resize->last_session, $err);
        }        
      break;
      default:
        $err = 1;
        $this->errm("Неизвестный uploads_resize_type.", $err);
       
    }        
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  return $err;
}

function ev_file_resizename($fn) {
  return "resize_".$fn;
}

/**
 * Блок отображения загруженного файла с чекбоксом удаления
 * 
 * @example 
 * vcb_avatar_view($val) {
 *    return $this->ev_file_view($this->current_callback);
 * }
 * @param unknown_type $fieldname
 * @return unknown
 */
function ev_file_view($fieldname, $input_name = 'remove_file', $show_preview = 1) {
  global $std;
  $fn = $this->d[$fieldname];
  $path = $this->uploads_dir.$fn;

  $checkbox = '';
  
  if ($fn=='') {
    $ret = "отсутствует";
    $exists = 0;
  }
  elseif (file_exists($path)) {
    $exists = 1;
    $url = $this->uploads_url.$fn;
    $size = filesize($path);
    $f_size = $std->act_size($size);

    $checkbox = "<div style='padding-top: 5px 0;font-size:90%; color: gray;'>{$f_size}</div><div style='padding: 5px 0 0 0;'><input type='checkbox' name='new[{$input_name}]'>&nbsp;удалить</div>";
      
    if ($this->uploads_make_resize && $show_preview) {
      $resize_url = $this->uploads_url.$this->ev_file_resizename($fn);
      $ret = "<a href='{$url}' target=_blank><img src='{$resize_url}'></a>{$checkbox}";
    }
    elseif($show_preview && in_array($std->file->extension($fn), array('jpg', 'gif', 'png'))) {
      $ret = "<img src='{$url}'>{$checkbox}";
    }
    else {
      $ret = "<a href='{$url}' target=_blank>{$fn}</a>{$checkbox}";
    }    
  }
  else {
    $exists = 0;
    $ret = ($fn=='') ? "не загружен" : "{$fn} - <span style='color:red;'>файл не найден</span>{$checkbox}";
  }
  
  return $ret;  
}


//FILTERS ===================================
function apply_filter($t, $filter_id) {  
  if (method_exists($this, "filter_{$filter_id}")) {
    eval("\$t = \$this->filter_{$filter_id}(\$t);");   
  }
  else {
    t('Filter <b>filter_{$filter_id}</b> not exists.', 1);
  }     
  return $t;
}

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

// PUBLIC SEARCH ============================
/**
 * Подготавливает три типа запросов по
 * всем public_search полям
 * для текущей модели
 * 1) field like "%word1%word2%"
 * 2) field like "%word1%" AND "%word2%"
 * 3) field like "%word1%" OR "%word2%"
 * Вызывается в m_searchresult->process_search($search)
 */
function get_public_search_sql($search) {
  global $std;
  
  $fs = array();
  $sqls = array();
  
  // joins parsing
  if ($this->use_joins) {
    $jf = $this->joins['f'];
    $jj = $this->joins['j'];
  }
  else {
    $jf = $jj = "";
  }  
  
  foreach($this->fields as $f) {
    if ($f['public_search']) {
      $fs[] = $f['name'];
    }
  }  
  
  if (count($fs)>0) {
    $words = unserialize($search['words']);
    foreach($words as $k=>$w){
      $words[$k] = $std->text->escape_search_word($w); // +quotes 
    }
    
    $field_or = array();
    foreach($fs as $fn) {
      $tar = array();
      foreach($words as $w) { 
        // отсеиваем слишком короткие слова
        if (strlen($w) >= $search['min_len']) {
          $tar[] = "{$this->t}.{$fn} LIKE \"%{$w}%\""; 
        }
      }
      
      $field_or[1][] = "{$this->t}.{$fn} LIKE \"%".implode("%", $words)."%\"";     
      if (count($tar)>0)  {
        $field_or[2][] = "(".implode(" AND ", $tar).")";
        $field_or[3][] = "(".implode(" OR ", $tar).")";
      }
    }
    
    $addon = $this->public_search_sql_addon;
    foreach($field_or as $k => $ar) {
      $sqls[$k] = "SELECT {$this->t}.*{$jf} FROM {$this->t} {$jj}  WHERE (".implode(" OR ", $ar).") {$addon}";  
    }      
  }
  
  return $sqls;
}

// TREE FUNCTIONS ===========================
/**
 * Основная независимая функция синхронизации записей для формирования дерева
 *
 * @param unknown_type $unset_after
 */
function sync_tree($unset_after = 1) {
  global $sv, $db;
  
  // родители все что есть
  $all_parent_ids = array();
  $db->q("SELECT parent_id FROM {$this->t} GROUP BY parent_id");
  while ($d = $db->f()) {
    $all_parent_ids[] = $d['parent_id'];
  }
  
  // считываем все записи с таблицы с идентификтаорами в индексе
  $ar = $this->item_list("", "", 0, 0, 1);
  $ids = array_keys($ar['list']);
  
  // определяем несщуествующих родителей путем вычеитания из родителей существующие объекты, останутся только те кого нет и нули.
  $ids[] = 0; // чтобы корневые тоже отсеял
  $not_exist_parents = array_diff($all_parent_ids, $ids);
  
  $childs = array();
  foreach($ar['list'] as $k => $d) {
    
    // проверяем родителя если че обновляем
    if ($d['parent_id']<>0 && in_array($d['parent_id'], $not_exist_parents)) {
      $d['parent_id'] = 0;
      $ar['list'][$k]['parnet_id'] = 0;
      $this->update_row(array('parent_id' => 0), $d['id'], 0);
    }
    
    $childs[$d['parent_id']][$d['id']] = $d; 
  }

  // парсим ДЕРЕВО
      $this->tree = array();
      $this->tree_step = 0;
      $this->tree_items = $ar['list'];
      $this->tree_childs = $childs;
      $this->tree_slug = '/';
      $this->tree_ids = '/';    
      if (count($childs)>0) {
        // перебираем корневые
        foreach($childs[0] as $ch_id => $d) {
          $this->tree_step++;       
          $d['new_parent_slug'] = $this->tree_slug;
          $d['new_parent_ids'] = $this->tree_ids;
          $d['new_level'] = $this->tree_step;
          $this->tree[] = $d;        
          // если есть потомки запрашиваем рекурсивно все дерево
          if (isset($this->tree_childs[$d['id']])) {
            $this->sync_tree_childs($d['id'], $d);
          }        
          $this->tree_step--;    
        }      
      }
      $tree = $this->tree;      
     
 
  // собираем идентификаторы всех подразделов для каждого раздела
  $childs_ids = array(); // идентификаторы подразделов без вложенных
  $childs_ids_all = array(); // иднетификаторы всех подразделов включая вложенные
  foreach($tree as $d) {
    $tar = explode("/", $d['new_parent_ids']);
    foreach($tar as $pid) {
      $pid = intval($pid);
      if ($pid>0) {
        $childs_ids_all[$pid][] = $d['id'];
      }
    }
    $childs_ids[$d['parent_id']][] = $d['id'];
  }
  
  // обновляем параметры если изменились
  foreach($tree as $d) {
    $child_count = (isset($this->tree_childs[$d['id']])) ? count($this->tree_childs[$d['id']]) : 0;
    
    $d['new_childs_ids'] = (isset($childs_ids[$d['id']])) ? "/".implode("/", $childs_ids[$d['id']])."/" : '';
    $d['new_childs_ids_all'] = (isset($childs_ids_all[$d['id']])) ? "/".implode("/", $childs_ids_all[$d['id']])."/" : '';
    
    if (  $d['level']<>$d['new_level'] 
       || $d['new_parent_slug']<>$d['parent_slug'] 
       || $d['new_parent_ids']<>$d['parent_ids']
       || $child_count<>$d['child_count']    
       || $d['new_childs_ids']<>$d['childs_ids']
       || $d['new_childs_ids_all']<>$d['childs_ids_all']
       ) {
      $p = array(
        'parent_slug' => $d['new_parent_slug'],
        'parent_ids' => $d['new_parent_ids'],
        'level' => $d['new_level'],
        'child_count' => $child_count,
        'childs_ids' => $d['new_childs_ids'],
        'childs_ids_all' => $d['new_childs_ids_all']
      );
      $this->update_row($p, $d['id'], 0);
    }
  }
 
  $this->sync_tree_counts();
  
  if ($unset_after) {
    $this->tree = array();
    $this->tree_step = 0;
    $this->tree_items = array();
    $this->tree_childs = array();  
  }
  
  
}

/**
 * основная фнукция пересчета элементов связанной таблицы
 * и записи их в поля дерева: count, count_all
 */
function sync_tree_counts() {
  global $sv, $db;
  
  // проверяем счетчики позиций
  $counts = array();
  $db->q("SELECT {$this->tree_item_field}, count(*) as c FROM {$sv->t[$this->tree_item_table]} GROUP BY {$this->tree_item_field}", __FILE__, __LINE__);
  while($d = $db->f()) {
    $counts[$d[$this->tree_item_field]] = $d['c'];
  }
  // $counts = array('category_id' => count_of_items, ); - прямые вхождения  
   
  foreach($this->tree as $d) {    
    $tree_count = (isset($counts[$d['id']])) ? $counts[$d['id']] : 0;
    $tree_count_all = $tree_count;
    $tar = explode('/', $d['childs_ids_all']);
    foreach($tar as $child_id) {
      $child_id = intval($child_id);
      if (!isset($counts[$child_id])) { continue; }
      $tree_count_all += $counts[$child_id];      
    }
    //t("{$d['title']} {$d['childs_ids_all']} \n {$tree_count} \n $tree_count_all", 3);
    if (  $d['count']<>$tree_count || $d['count_all']<>$tree_count_all ) {
      $p = array(
        'count' => $tree_count,
        'count_all' => $tree_count_all
      );
      $this->update_row($p, $d['id'], 0);    
    }
  }
}

/**
 * Вспомогательная рекурсивная функция для синхронизации дерева
 */
function sync_tree_childs($id, $item) { 
  // если страницы нет в списке или нет потомков
  if (!isset($this->tree_items[$id]) || !isset($this->tree_childs[$id])) {
    return false;
  }
  
  $this->tree_step++;
  $this->tree_slug .= $item['slug'].'/';
  $this->tree_ids .= $item['id'].'/';
  
  // перебираем потомков и запрашиваем рекурсивно потомков второго уровня и дальше
  foreach($this->tree_childs[$id] as $ch_id => $d) {   
    $d['new_parent_slug'] = $this->tree_slug;
    $d['new_parent_ids'] = $this->tree_ids;
    $d['new_level'] = $this->tree_step;
    $this->tree[] = $d;
    if (isset($this->tree_childs[$d['id']])) {
      $this->sync_tree_childs($d['id'], $d);
    }
  }
  
  $this->tree_slug = preg_replace("#/[^/]*/$#si", "/", $this->tree_slug);
  $this->tree_ids = preg_replace("#/[^/]*/$#si", "/", $this->tree_ids);  
  $this->tree_step--;    
  return true;
}

/**
 * Вспомогательная функция для получения списка дочерних объектов через ajax
 *
 * @param unknown_type $id 
 * @param unknown_type $act - проверенный модуль
 * @return unknown
 */

function ajax_tree_childs($id, $act = '') {
  global $sv, $db, $smarty;
  
  $id = intval($id);
  $d = $this->get_item($id);
  if (!$d) { return "Object {$id} not found."; }

  $ar = $this->item_list("`parent_id`='{$d['id']}'", "`place` ASC, `title` ASC", 0, 1);
  $tr = array();
  
  $sv->act = $act;
  $smarty->assign("sv", $sv);
  
  foreach($ar['list'] as $d) {
    $smarty->assign("d", $d);
    $table = $smarty->fetch("scaffold/parts/tree_row.tpl");
    $tr[] = "{$table} \n<div class='page-childs' id='page-{$d['id']}-childs' style='display:none;'></div>";
  }
    
  $ret = implode("\n\n", $tr);
  return $ret;
}

/**
 * Кастом ипут для селектора дерева
 *
 * @param unknown_type $val
 * @return unknown
 */
function ci_parent_id($val) {
  global $sv, $db;
  
  
  $id = (isset($this->d['id'])) ? $db->esc($this->d['id']) : 0;
  $parent_id = (isset($this->d['parent_id'])) ? $this->d['parent_id'] : 0;
  $opts = array();
  $opts[] = "<option value='0'>Корневой раздел</option>";
  
  $ar = $this->item_list("`id`<>'{$id}' AND `parent_ids` NOT LIKE \"%/{$id}/%\"", "`parent_slug` ASC, `place` ASC, `title` ASC", 0, 1);
  $tree = $this->parse_sorted_tree($ar['list']);
  
  foreach($tree as $d) {
    $s = ($parent_id==$d['id']) ? " selected" : '';
    if ($this->code=='create' && isset($sv->_get['parent_id']) && $sv->_get['parent_id']==$d['id']) {
      $s = ' selected';
    }
    $pad = str_pad("", $d['level']*3, " - ");
    $opts[] = "<option value='{$d['id']}'{$s}>{$pad} {$d['title']}</option>";
  }
  
  $ret = "<select name='new[parent_id]'>".implode("\n", $opts)."</select>";
  
  return $ret;
}
/**
 * Кастом инпут для фомрирования древовидного селектора другими моделями
 *
 * @param int $val - текущее значение
 * @param varchar $name - название инпута которое нужно подставить в вывод (название поля у текущей модели)
 * @param boolean $null - включить нулевое значение
 * @param boolean $multiselect - включить мультиселект
 * @return unknown
 */
function tree_remote_selector($val, $name = 'tcat_id', $null = 0, $multi = 0) {
  global $sv, $db;
  
  $ar = $this->item_list("", "`parent_slug` ASC, `place` ASC, `title` ASC", 0, 1);
  $tree = $this->parse_sorted_tree($ar['list']);
  
  $opts = array();
  if ($null) {
    $opts[] = "<option value='0'>[не выбрано]</option>";
  }
  
   
  if ($multi) {
    // получаем массив выбранных значений
    $values = $this->parse_multi_value($val);  
    $values = (is_array($values)) ? $values : array();
  }
    
  foreach($tree as $d) {
    if ($multi) {
      $s = (in_array($d['id'], $values)) ? " selected" : "";
    }
    else {
      $s = ($val==$d['id']) ? " selected" : '';
    }
    $level = $d['level']-1;
    $pad = str_pad("", $level*3, " - ");
    $opts[] = "<option value='{$d['id']}'{$s}>{$pad} {$d['title']}</option>";
  }
  
  if ($multi) {
    $size = (count($opts)>10) ? 10 : count($opts);
    $ret = "<select name='new[{$name}][]' size='{$size}'  multiple='multiple'>".implode("\n", $opts)."</select>    
    <br><small style='color:gray;'>с нажатой кнопкой Ctrl можно снять выделение либо выбрать несколько вариантов</small>"; 
  }
  else {
    $ret = "<select name='new[{$name}]'>".implode("\n", $opts)."</select>";
  }
  return $ret;
}  

function v_parent_id($id) {  
  $id = intval($id);
  return $id;
}

/**
 * сортирует любой набор записей в дерево или отдельные ветви
 * УСЛОВИЕ! записи должны быть отсортированы по уровню, слева направо (сначало корневые) иначе ветви будут как попало
 * @param unknown_type $ar
 * @return unknown
 */

function parse_sorted_tree($ar) {
  
  $childs = array();
  $items = array();
  
  foreach($ar as $d) {
    $items[$d['id']] = $d;
    $childs[$d['parent_id']][] = $d['id'];
  }
  
  $this->tree = array();
  $this->tree_items = $items;
  $this->tree_childs = $childs;
  $this->tree_used = array();

  // прогоняем через цикл не только выбранный уровень а все записи, не трогая только те что уже выбыли
  foreach($ar as $d) {
    if (in_array($d['id'], $this->tree_used)) continue;    
    $this->tree[] = $d;    
    $this->tree_used[] = $d['id'];
    // если есть потомки парсим их
    if (isset($this->tree_childs[$d['id']])) {
      $this->parse_sorted_childs($d['id']);
    }
  }
  return $this->tree;
}

function parse_sorted_childs($id) {
  foreach($this->tree_childs[$id] as $child_id) {
    if (in_array($child_id, $this->tree_used)) continue;    
    $d = $this->tree_items[$child_id];
    $this->tree[] = $d;    
    $this->tree_used[] = $d['id'];
    // если есть потомки парсим их
    if (isset($this->tree_childs[$d['id']])) {
      $this->parse_sorted_childs($d['id']);
    }    
  }
}


// end of class  

}
?>