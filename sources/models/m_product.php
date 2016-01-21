<?php

/*

абстрактная модель различных объектов на продажу
1) название
2) код
3) цена
4) размер
5) фото
6) краткое содержание

------------
1) title
2) price
3) photo
4) text

5) slug
6) cat_id
7) subcat_id
8) hit?

*/
class m_product extends class_model {
  var $tables = array(
    'products' => "
      `id` bigint(20) NOT NULL auto_increment,
      `title` varchar(255) NOT NULL default '',
      `slug` varchar(255) not null default '',
      `cat_id` int(11) not null default '0',
      `subcat_id` int(11) not null default '0',
            
      `ann` text,      
      `text` text,
      `photo` varchar(255) not null default '',
      
      `file` varchar(255) null,
      `file_mime` varchar(255) null,
      
      `views` int(11) not null default '0',
      `downloads` int(11) not null default '0',
      `replycount` int(11) not null default '0',
      `orders` int(11) not null default '0',
      
      `price` float(16,4) not null default '0',
      `date` datetime default NULL,
      
      `status_id` int(11) not null default '1',
      `hit` tinyint(1) not null default '0',
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`date`)
    "
  );
  
  /*
  `size` bigint(20) not null default '0',
      
  */
  var $status_ar = array(
    0 => 'Черновик',
    1 => 'Опубликован',
    2 => 'Отключен'
  );
  
  var $ext_ar = array('jpg');  

  /**
   * Текущий раздел, должен быть выбран при вызове public_cat
   *
   * @var unknown_type
   */
  var $c_cat = "";
  
  var $c_subcat = 0;
  
  var $active_cat = false;
  var $active_subcat = false;
  var $active_product = false;
  
  /**
   * Вид каталога, если ключено то как на сайтах продажи рпограммного обеспеч.
   *
   * @var unknown_type
   */
  var $soft_style = 0;
  
  var $per_page = 50;
  
  var $photo_url = "uploads/products/";
  var $photo_dir = "uploads/products/";
  var $photo_ext = array('jpg', 'jpeg', 'png', 'gif');
  
  /// файлы на продажу
  var $files_dir = "shop_files/";
  var $files_ext = array('pdf', 'exe', 'rar', 'zip', 'gz', 'bz2', 'doc', 'xls', 'ppt', 'avi', 'flv', 'jpg', 'jpeg', 'png', 'gif', 'txt');
  
  // временный стек для хранения доп параметров записи
  var $stack = array();
  
  var $load_attaches = 1;
  var $attaches_page = '';
  var $attaches_top_margin = 500;
  

  var $load_markitup = 1;
  var $markitup_use_tinymce = 1;
  var $markitup_use_emoticons = 0;
  var $markitup_width = '100%';
  var $markitup_selector = 'textarea';
  var $markitup_type = 'html';
    
  var $uploads_make_resize = 1;
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['products'];
  
  $this->photo_dir = UPLOADS_DIR."products/";
  $this->photo_url = UPLOADS_URL."products/";
  
  $this->files_dir = ROOT_DIR."shop_files/";
  $this->files_url = PUBLIC_URL."file/download/";
  
  $sv->load_model('cat');
 
  /*
  $this->init_field(array(
  'name' => 'created_at',  
  'show_in' => array(),
  'write_in' => array()
  ));    
  $this->init_field(array(
  'name' => 'updated_at',  
  'show_in' => array(),
  'write_in' => array()
  ));    
  $this->init_field(array(
  'name' => 'id',  
  'show_in' => array(),
  'write_in' => array()
  ));    
    
  */  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Название',
  'type' => 'varchar',  
  'len' => '70',
  'not_null' => 1,
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));    
    

  $this->init_field(array(
  'name' => 'slug',
  'title' => 'Адрес товара (лат.)',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default'),
  'write_in' => array('create', 'edit'),

  ));      
  
  /*
  
  $this->init_field(array(
  'name' => 'cat_id',
  'title' => 'Раздел',
  'type' => 'varchar',  
  'input' => 'select',
  'belongs_to' => array('table' => 'cats', 'field' => 'id', 'return' => 'title'),
  'show_in' => array(),
  'write_in' => array()
  ));    

  
  $this->init_field(array(
  'name' => 'cat',
  'title' => 'Категория',
  'virtual' => 'cat_id',  
  'show_in' => array('default'),
  'write_in' => array()
  ));      
 */ 
      
  $cat_id = ($sv->m['cat']->c_cat) ? $sv->m['cat']->c_cat['id'] : 0;
  $this->init_field(array(
  'name' => 'subcat_id',
  'title' => 'Категория',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'subcats', 'field' => 'id', 'return' => 'title',  'where' => array(array('remote' => 'cat_id', 'operator'=> '=', 'value' => $cat_id))),
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'subcat',
  'title' => 'Категория',
  'virtual' => 'subcat_id',  
  'show_in' => array('default'),
  'write_in' => array()
  ));      
 
   /*
  $this->init_field(array(
  'name' => 'file',
  'title' => 'Загрузка файла для продажи',
  'type' => 'varchar',  
  'input' => 'file',
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));     
  
   
  $this->init_field(array(
  'name' => 'file_preview',
  'title' => 'Файл для продажи',
  'virtual' => 'file',  
  'show_in' => array('edit'),
  'write_in' => array() 
  ));     
 
  
  $this->init_field(array(
  'name' => 'file_mime',
  'title' => 'MIME тип файла',
  'type' => 'varchar',
  'len'  => '50',
  'show_in' => array(),
  'write_in' => array('edit') 
  ));     
*/
  
  $this->init_field(array(
  'name' => 'views',
  'title' => 'Просмотров',
  'type' => 'int',
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit') 
  ));     
     
  /*
  $this->init_field(array(
  'name' => 'downloads',
  'title' => 'Скачиваний',
  'type' => 'int',
  'len' => 10,
  'show_in' => array(),
  'write_in' => array('edit') 
  ));   
  */
  
  $this->init_field(array(
  'name' => 'replycount',
  'title' => 'Отзывов',
  'type' => 'int',
  'len' => 10,
  'show_in' => array(),
  'write_in' => array('edit') 
  ));     
    
  
  $this->init_field(array(
  'name' => 'orders',
  'title' => 'Заказов',
  'type' => 'int',
  'len' => 10,
  'show_in' => array(),
  'write_in' => array('edit') 
  ));     
    

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'hit',
  'title' => 'Хит продаж!',
  'type' => 'boolean',  
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    
    
   
      
  $this->init_field(array(
  'name' => 'hr',
  'title' => "<div style='background-color: #efefef;'>&nbsp;</div>",
  'virtual' => 'id',  
  'show_in' => array('edit'),
  'write_in' => array() 
  ));     
    

  
  $this->init_field(array(
  'name' => 'photo',
  'title' => 'Загрузка основного фото',
  'type' => 'varchar',  
  'input' => 'file',
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));     
  
  $this->init_field(array(
  'name' => 'photo_preview',
  'title' => 'Превью основного фото',
  'virtual' => 'photo',  
  'show_in' => array('edit'),
  'write_in' => array() 
  ));     

 
  $this->init_field(array(
  'name' => 'ann',
  'title' => 'Краткое описание',
  'type' => 'text',  
  'len' => '60',
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    
 

  $this->init_field(array(
  'name' => 'text',
  'title' => 'Полное описание',
  'type' => 'text',  
  'len' => '90',
  'show_in' => array(),
  'write_in' => array('create', 'edit'),
  'id' => 'full-text'
  
  ));      
  

  $this->init_field(array(
  'name' => 'price',
  'title' => 'Стоимость (руб.)',
  'type' => 'float',  
  'len' => '20',
  'show_in' => array('default',),
  'write_in' => array('create', 'edit')
  ));      
   
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата публикации',
  'type' => 'datetime',
  'show_in' => array('remove', 'default'),  
  'write_in' => array('edit')
  ));      
  
}

function v_slug($t) {
  $t = trim($t);
  $t = preg_replace("#\s#si", "-", $t);
  return $t;
}

// файл для продажи
function wcb_file() {
   $this->ext_ar = $this->files_ext;  
}
function v_file() {
  
  $this->ext_ar = $this->files_ext;  
  
  $this->uploads_dir = $this->files_dir;
  $this->uploads_url = $this->files_dir;
  $this->uploads_make_resize = 0;
      
  return $this->ev_file(0);  
}
function on_upload_file($file) {
  
  $this->stack['file_mime'] = strip_tags($file['mime']);
  
}
function vcb_file_preview($fn) {
  global $std;
  
  $this->uploads_dir = $this->files_dir;
  $this->uploads_url = $this->files_url;
    
  $url = $this->uploads_url.$fn; 
  $path = $this->uploads_dir.$fn;
  $f_size = (file_exists($path)) ? $std->act_size(filesize($path)) : 0;
  $ret = ($fn!='') ? "{$fn} @ {$f_size}" : "<span style='color:red;'>не загружен</span>";
  
  $ret = $std->file->text_show_replace($ret);
  return $ret;
}


// разделитель
function vcb_hr() {
  return "<div style='background-color: #efefef;'>&nbsp;</div>";
}
function wcb_photo() {
  $this->ext_ar = $this->photo_ext;  
}
function v_photo() {
  
  $this->uploads_dir = $this->photo_dir;
  $this->uploads_url = $this->photo_dir;
  $this->uploads_make_resize = 1;
  $this->uploads_resize_type = "by_width";
  $this->uploads_w = 140;  
  $this->ext_ar = array('jpg', 'jpeg', 'png', 'gif');  
  
  return $this->ev_file(0);
}
function on_upload_photo($file) {
  t($file);
}

function vcb_photo_preview($fn) {
  global $std;
  
  $this->uploads_dir = $this->photo_dir;
  $this->uploads_url = $this->photo_url;
  
  return $this->ev_file_view('photo');
  
  $this->uploads_dir = $this->photo_dir;
  $this->uploads_url = $this->photo_url;
  
  $url = $this->uploads_url.$fn;
  $rurl = $this->uploads_url.$this->ev_file_resizename($fn);
  
  $ret = ($fn!='') ? "<a href='{$url}' target=_blank><img src='{$rurl}' border='0'></a>" : "<span style='color:red;'>не загружено</span>";
  
  $ret = $std->file->text_show_replace($ret);
  return $ret;
}


function last_v($p) {
  global $sv, $std, $db;
  
  // slug
  $id = ($this->code=='edit') ? $this->current_record : 0; 
  if (!$std->text->is_valid_slug($p['slug'], $this->t, 'slug', $id)) {
    $p['slug'] = $std->text->gen_slug($p['title'], $this->t, 'slug');
  }
 
  if ($this->code=='create') {
    $p['date'] = $sv->date_time;      
  }
  
  foreach($this->stack as $k=>$v) {
    $p[$k] = $v;
  }
   
  // cat_id
  if (isset($p['subcat_id'])) {
    $sv->load_model('subcat');  
    $sub = $sv->m['subcat']->get_item($p['subcat_id']);
    if ($sub) {
      $p['cat_id'] = $sub['cat_id'];
    }
  }
  
  
  return $p;
}

// Parsers
function parse($d) {
  global $sv, $std;
  /*
  $d['url_details'] = $d['url'] = "/{$d['cat']}/details/?id={$d['id']}"; 
  $d['url_download'] = "/{$d['cat']}/download/?id={$d['id']}";
  $d['url_buy'] = $sv->view->root_url."buy/?id={$d['id']}";
  */
  if ($d['file']) {
    $d['file_path'] = $this->files_dir.$d['file'];
    $d['file_exists'] = (file_exists($d['file_path'])) ? 1 : 0;
    $d['size'] = ($d['file_exists']) ? filesize($d['file_path']) : 0;    
  }
  else {
    $d['file_path'] = '';
    $d['file_exists'] = 0;
    $d['size'] = 0;
  }
  
  $d['f_size'] = $std->act_size($d['size']);
  $d['f_price'] = ($d['price']<=0) ? "бесплатно" : number_format($d['price'], 2);
    
  if ($d['photo']!='') {
    $d['resize_name'] = $this->ev_file_resizename($d['photo']);
    $d['photo_url'] = $this->photo_url.$d['photo'];
    $d['resize_url'] = $this->photo_url.$d['resize_name'];
    $d['photo_path'] = $this->photo_dir.$d['photo'];
    $d['resize_path'] = $this->photo_dir.$d['resize_name']; 
    
    $d['img_preview'] = "<a href='{$d['photo_url']}' target=_blank  class='lightbox'><img src='{$d['resize_url']}' border='0'></a>";
  }
  else {
    $d['img_preview'] = '';
    $d['photo_path'] = '';
    $d['resize_path'] = '';
  }
  
  
  // набор кнопок
  $d['auth'] = ($sv->user['session']['account_id']>0) ? 1 : 0;
  
  // оплачено?
  $d['payed'] = 0;
  
  $d['f_date'] = $std->time->format($d['date'], 0.6, 1);

  
  // офрмление тумб
  $d['url'] = (isset($d['subcat_slug']) && isset($d['cat_slug'])) ? "/shop/{$d['cat_slug']}/{$d['subcat_slug']}/{$d['slug']}/" : $this->active_subcat['url'].$d['slug']."/";
  //$d['url_to_basket'] = ($sv->user['session']['account_id']>0) ? "/shop/basket/add/?id={$d['id']}" : "javascript: add_to_basket({$d['id']});";
  //$d['url_to_basket'] = "javascript: $.get('/post.php',  { act: 'basket_add', id: {$d['id']}});";
  
  $d['td_content'] = "
  
    <div class='item'>
    	<a href='{$d['url']}'>{$d['title']}</a>
        <div class='image'><img src='{$d['resize_url']}' width='140' /></div>
        <div class='price'>Цена: <span>{$d['f_price']}</span> <span class='rur'>p<span>уб.</span></span></div>
        <a href='#' class='order' onclick='return basket_add({$d['id']});'><img src='/img/a_order_bg.gif' width='151' height='32' alt='' /></a>
    </div>
                      
  ";
  return $d;
}

// pre post actions
function before_default() {
  global $sv;
  
  $cat_id = ($sv->m['cat']->c_cat) ? $sv->m['cat']->c_cat['id'] : 0;
  if ($cat_id>0) {
    $this->where[] = "{$this->t}.cat_id='{$cat_id}'";
  }
}
// Controllers
/**
 * @deprecated 
 *
 * @return unknown
 */
function c_edit_old() {
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
  //$this->table_compact = 1;
  $this->table_width = "100%";
  $ret['form'] = $this->compile_edit_table($d);
  
  // attaches
  $sv->load_model('attach');  
  $sv->m['attach']->action_url = u($sv->act, "attaches", $d['id']);
  $ret['attach'] = $sv->m['attach']->init_object($this->name, $d['id'], $sv->user['session']['account_id']);
  $sv->parsed['admin_sidebar'] = "
    <div style='margin-top: 650px; border: 1px solid #dddddd;'>
      <div style='padding: 5px 10px;background-color:#efefef;'><b>Прикрепление файлов</b></div>
      {$ret['attach']['form']}
    </div>";
  
// markitup
  $std->markitup->use_emoticons = 0;
  $std->markitup->width = '100%';
  $ret['markitup'] = $std->markitup->js("textarea", "html");
    
  return $ret;
}

// pre / post
function garbage_collector($d) {
  global $sv;
  
  $err = 0;
  $errm = array();
  $d = $this->parse($d);
  
  $ar = array($d['file_path'], $d['photo_path'], $d['resize_path']);
  
  foreach ($ar as $path) { 
    if ($path) {
      if (file_exists($path)) {
        $errm[] = "<b>{$path}</b> существует, удаляем...";
        unlink($path);
        if (file_exists($path)) {
          $err = 1;
          $errm[] = "-- ошибка! не удалось удалить --";
        }
        else {
          $errm[] = "-- успешно --";
        }
      }
      else {
        $errm[] = "<b>{$path}</b> не существует.";
      }
      
      
    }
  }
  
  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}


// PUBLIC CONTROLLERS
function c_public_index() {
  global $sv;

  $ret = array();
  
  $sv->load_model('cat');
 // $ret['catlist'] = $sv->m['cat']->catlist_main();  
  
 
  
  return $ret;
}

/**
 * Список товаров в категории
 *
 */
function c_public_cat() {
  global $sv, $std, $db; $ret = array();
  
  $sv->vars['js'][] = "jquery.lightbox-0.4.min.js";
  $sv->vars['styles'][] = "jquery.lightbox-0.4.css";
 
  // выбиарем хиты
  $c = &$this->active_cat;
  $ret = $this->product_list_wh("{$this->t}.cat_id='{$c['id']}' AND `hit`='1' AND {$this->t}.status_id='1'", "{$this->t}.title ASC", 0, 1);

  
  // подкатегории
  $sv->load_model('subcat');
  $ret['subcats'] = $sv->m['subcat']->item_list("`cat_id`='{$c['id']}'", "`title` ASC", 0, 1);
  return $ret; 
  
  
  return $ret;
}

function c_public_subcat() {
  global $sv;
  
  $sc = &$this->active_subcat;
  $ret = $this->item_list_pl("`subcat_id`='{$sc['id']}' AND `status_id`='1'", "`hit` DESC, `title` ASC", $this->per_page, $sv->_get['page'], 1, $sc['url']."?page=");
  
  $td = array();
  foreach($ret['list'] as $d){
    $td[] = $d['td_content'];
  }
  $ret['td'] = $td;  
  return $ret; 
}
/**
 * Подробный вид товара
 * 
 * @return unknown
 */
function c_public_details() {
  global $sv, $std, $db;  $ret = array();
  
  $d = $this->active_product;
  
  /*
  $sv->load_model('order');
  $ret['order_exists'] = ($sv->m['order']->exists_valid_order($sv->user['session']['account_id'], $d['id'])) ? 1 : 0;
  $ret['order'] = $sv->m['order']->d;
  */
  // stats
  $this->update_row(array('views' => $d['views']+1), $d['id']);

  $ret['d'] = $d;
  return $ret;
}

/**
 * Оформление заказа
 * Информация и подтверждение
 * 
 * @return unknown
 */
function c_public_buy() {
  global $sv, $std, $db;  $ret = array();
  
  $err = 0;
  $errm = array();
  
  $sv->load_model('order');
  
  // проверка товара
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $ret['d'] = $d = $this->get_item($id, 1);
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  
  // если товар уже заказывался ранее и не был отменен, то ошибка
  if ($sv->m['order']->exists_valid_order($sv->user['session']['account_id'], $d['id'])) {
    $err = 1;
    $errm[] = "Вы уже <a href='{$sv->m['order']->d['url']}'>заказывали данный товар</a>.";
  }
  
  if (isset($sv->_post['accept']) && $sv->_post['accept']==1 && !$err) {
    $ret['step'] = 2;
    $r = $sv->m['order']->make_order($sv->user['session']['account_id'], $d['id'], $d['price']);
    $err = ($r['err']) ? 1 : $err;
    $errm = array_merge($errm, $r['errm']);
  }
  else {
    $ret['step'] = 1;
  }
  
  $ret['err'] = $err;
  $ret['err_box'] = $std->err_box($err, $errm);
  
  return $ret;
}


/**
 * Скачивание товара
 * 
 * @return unknown
 */
function c_public_download() {
  global $sv, $std, $db;  $ret = array();
  
  $err = 0;
  $errm = array();
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $d = $this->get_item($id, 1);
  
  if (!$d || $d['file']=='') {
    $err = 1;
    $sv->view->show_err_page("notfound");
  }

  // access
  if ($sv->user['session']['account_id']<=0) {
    return $this->scaffold("public_not_registred");
  }
  
  
  
  if (!$err) {
    // stats
    $this->update_row(array('downloads' => $d['downloads']+1));
    
    // отдаем файл
    header( "Content-Disposition: attachment; filename={$d['file']}" ); 
    if ($d['file_mime']) {
      header("Content-type: {$d['file_mime']};");
    }
    else {
      header("Content-type: application/octet-stream;");
    }
    readfile($d['file_path']);
    exit();
    
  }

  return $ret;
}

/**
 * Показ сообщения о необходимости регистрации или авторизации
 *
 */
function c_public_not_registred() {
  global $sv, $std, $db;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $ret['d'] = $d = $this->get_item($id, 1);
  if (!$d) {
    $sv->view->show_err_page('notfound');
  }
  
  $errm = array("Для доступа к файлу <b>{$d['file']}</b> требуется регистрация.");
  $errm[] = "&larr; <a href='{$d['url_details']}'>Назад</a>";
  $ret['err_box'] = $std->err_box(1, $errm);
 
  return $ret;
}


// STD 
function compile_breadcrumb($code) {

  $ar = $tr = array();
  
  switch ($code) {   
    case 'details':
      //$ar[] =  $this->active_product;    
      
    case 'subcat':
      $ar[] = $this->active_subcat;
      
    case 'cat':
      $ar[] = $this->active_cat;

    break;
    
    case 'index': default:  break;
  }

  if (count($ar)>0) {
    $tr[] = "<a href='/shop/'>Каталог</a>";
    $last = count($ar)-1;
    $ar = array_reverse($ar);
    foreach($ar as $k=>$d) {
      if ($k==$last && $code!='details') {
        $tr[] = $d['title'];
      }
      else {
        $tr[] = "<a href='{$d['url']}'>{$d['title']}</a>";
      }
    }
    $ret = implode(" / ", $tr);
  }
  else {
    $ret = '';
  }
  return $ret;
}


function get_hitlist($lim=10) {
  global $sv, $std, $db;
  
  $this->joins['j'] = "LEFT JOIN {$sv->t['subcats']} s ON (s.id={$this->t}.subcat_id)";
  $this->joins['f'] = ", s.cat_slug, s.slug as subcat_slug";
  $ret = $this->item_list("`hit`='1' AND `status_id`='1'", "`views` DESC", $lim, 1);

 
  $td = array();
  foreach($ret['list'] as $d){
    $td[] = $d['td_content'];
  }
  $ret['td'] = $td;  
    
  return $ret;
}

function product_list_wh($wh='', $order='', $lim = 20, $parse = 0) {
  global $sv, $db;
  
  $this->joins['j'] = "LEFT JOIN {$sv->t['subcats']} s ON (s.id={$this->t}.subcat_id)";
  $this->joins['f'] = ", s.cat_slug, s.slug as subcat_slug";
  $ret = $this->item_list($wh, $order, $lim, $parse);

 
  $td = array();
  foreach($ret['list'] as $d){
    $td[] = $d['td_content'];
  }
  $ret['td'] = $td;  
    
  return $ret;  
}

function cron_hourly() {
  global $sv;
  
  $sv->load_model('cat');
  $sv->load_model('subcat');
  
  $subcats = $sv->m['subcat']->item_list("", "", 0, 0);
  foreach($subcats['list'] as $subcat) {
    // обновлеяем глобальную у продуктов
    $this->update_wh(array('cat_id'=>$subcat['cat_id']), "`subcat_id`='{$subcat['id']}'", 0);
    
    // обновляем счетчики у подкатегорий
    $count = $this->count_wh("`subcat_id`='{$subcat['id']}'");
    $sv->m['subcat']->update_row(array('count'=>$count), $subcat['id']);
  }
  
  // обновляем счетчики у глобальных
  $cats = $sv->m['cat']->item_list("", "", 0, 0);
  foreach ($cats['list'] as $cat) {
    $count = $this->count_wh("`cat_id`='{$cat['id']}'");
    $sv->m['cat']->update_row(array('count'=>$count), $cat['id']);
  }
}

//eoc
}
?>