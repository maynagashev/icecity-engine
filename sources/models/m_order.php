<?php

/*

заказ товара


*/
class m_order extends class_model {
  
  var $tables = array(
    'orders' => "
      `id` bigint(20) NOT NULL auto_increment,
      `date` datetime null,
      `username` varchar(255) null,
      `phone` varchar(255) null,
      `email` varchar(255) null,
      `text` text null,
      `content` text null,
      `ip` varchar(255) null,
      `mailed` tinyint(1) not null default '0',
      `status_id` int(11) not null default '0',
      
      `price` float(16,4) not null default '0',
      `product_id` bigint(20) not null default '0',
      `user_id` int(11) not null default '0',
      `date_pay` datetime null,           
       
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`)
    "
  );
  

  var $status_ar = array(
    0 => 'Ожидает оплаты',
    1 => 'Оплачен',
    2 => 'Отменен'
  );
  
  var $c_content = '';
  var $title = 'Заказы из магазина';  
  var $config_vars = array(
    'order_email' => array('title' => 'Почтовый адрес для отправки заказов', 'type' => 'varchar', 'value' => '', 'len' => 30)  );

   
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['orders'];
  
  $this->init_field(array(
  'name' => 'date',
  'title' => 'Дата',
  'type' => 'datetime',  
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));    
  
  $this->init_field(array(
  'name' => 'username',
  'title' => 'ФИО покупателя',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create', 'public_new')
  ));    
    
  
  $this->init_field(array(
  'name' => 'phone',
  'title' => 'Телефон покупателя',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create', 'public_new')
  ));     
  
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email покупателя',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create', 'public_new')
  ));     
    
  
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Комментарий покупателя',
  'type' => 'text',  
  'len' => 40,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create', 'public_new')
  ));   
  
  $this->init_field(array(
  'name' => 'content',
  'title' => 'Содержимое заказа',
  'type' => 'text',  
  'len' => 60,
  'show_in' => array(),
  'write_in' => array('edit', 'create')
  ));   
    
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => 30,
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));     

  $this->init_field(array(
  'name' => 'mailed',
  'title' => 'Уведомление администратору отправлено?',
  'type' => 'boolean', 
  'show_in' => array(),
  'write_in' => array('edit')
  ));     
   
    
  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Статус',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('list' => $this->status_ar),
  'show_in' => array(),
  'write_in' => array('edit', 'create')
  ));   
   
  
  /* deprecated

  $this->init_field(array(
  'name' => 'product_id',
  'title' => 'ID Продукта',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'products', 'field' => 'id', 'return' => 'title'),
  'show_in' => array(),
  'write_in' => array('edit', 'create')
  ));      
    
  $this->init_field(array(
  'name' => 'product',
  'title' => 'Продукт',
  'virtual' => 'product_id',  
  'show_in' => array('default'),
  'write_in' => array()
  ));   
  
  $this->init_field(array(
  'name' => 'user_id',
  'title' => 'ID Юзера',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login'),
  'show_in' => array(),
  'write_in' => array('edit', 'create')
  ));      
      
    
  $this->init_field(array(
  'name' => 'user',
  'title' => 'Пользователь',
  'virtual' => 'user_id',  
  'show_in' => array('default'),
  'write_in' => array()
  ));   
    
  $this->init_field(array(
  'name' => 'price',
  'title' => 'Стоимость',
  'type' => 'float',  
  'len' => 20,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create')
  ));   

  
  $this->init_field(array(
  'name' => 'date_pay',
  'title' => 'Дата оплаты',
  'type' => 'datetime',  
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));    
  
  */

  $sv->load_model('cat');  
   
}

function v_username($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'mstrip');
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Не указано имя контактного лица.", 1);
  }
  
  return $t;
}

function v_phone($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'cut');
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Не указан контактный телефон.", 1);
  }
  
  return $t;
}

function v_email($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'cut');
  $t = trim($t);
  if ($t=='') {
    $this->v_err = 1;
    $this->errm("Не указан контактный email.", 1);
  }
  elseif (!$std->text->v_email($t)) {
    $this->v_err = 1;
    $this->errm("Неверный формат email.", 1);
  }
  
  
  return $t;
}

function v_text($t) {
  global $std;
  
  $t = $std->text->cut($t, 'cut', 'mstrip');
  $t = trim($t);

  return $t;
}

function last_v($p) {
  global $sv, $db;
  
  $p['status_id'] = 0;
  $p['content'] = $this->c_content;
  
  foreach($p as $k=>$v) {
    $ar[] = "`{$k}`='".$db->esc($v)."'";
  }
  $wh = implode(" AND ", $ar);
  $double = $this->get_item_wh($wh);
 
  if ($double) {
    $this->v_err = 1;
    $this->errm("Заказ с такими параметрами уже был оформлен ранее.", 1);
  }
  
  $p['date'] = $sv->date_time;
  $p['ip'] = $sv->ip;
  
  return $p;
}


function c_public_new() {
  return $this->init_submit_create(1, 0);
}

function c_mail() {
  global $sv;
  
  $ar = $this->item_list("`mailed`='0'", "", 0, 0);
  foreach($ar['list'] as $d) {
    $this->send_notify($d['id']);
   
  }
}

// stuff
function send_notify($id, $force = 0) {
  global $sv, $std;
  
  $err = 0;
  $d = $this->get_item($id);
  if (!$d) {
    $err = 1;
    $this->errm("Заказ для отправки уведомления не найден.", $err);
  }  
  elseif ($d['mailed'] && !$force) {
    $err = 1;
    $this->errm("Уведомление о заказе N{$d['id']} уже было отправлено ранеe.", $err);
  }
  
  
  $text = 
"  Дата: {$d['date']}
  ФИО: {$d['username']}
  Телефон: {$d['phone']}
  Email: {$d['email']}
  Комментарий: {$d['text']}  
  {$d['content']}  
  
  "; 
  // отправляем
  $std->mail->send($sv->cfg['order_email'], "{$sv->vars['site_title']} новый заказ N{$d['id']}", $text);
  
  // обновляем статус
  $this->update_row(array('mailed'=>1), $d['id'], 0);
  
  if ($err) $this->errs[] = __FUNCTION__;
  
}

}
?>