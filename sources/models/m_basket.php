<?php

class m_basket extends class_model {
  var $tables = array(
    'basket' => "
      `id` bigint(20) NOT NULL auto_increment,
      `sid` varchar(255) null,
      `account_id` int(11) NOT NULL default '0',
      `product_id` bigint(20) not null default '0',
      `count` int(11) not null default '0',
      
      `price` float(16,4) not null default '0',
      `title` varchar(255) null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      `basket` TINYINT( 1 ) NOT NULL DEFAULT  '1',
      `order_id` int(11) not null default '0',
      
      PRIMARY KEY  (`id`),
      KEY (`sid`), 
      KEY (`basket`),
      KEY (`order_id`)
    "
  );
  
  var $auth = 0;
  var $account_id = 0;
  var $last_f_sum = 0;
  
  /**
   * соержимое последнего товара после проверки is_valid_product
   *
   * @var unknown_type
   */
  var $last_product = false;
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['basket'];
  
  $sv->load_model('cat');
    
  $this->init_field(array(
  'name' => 'account_id',
  'title' => 'Пользователь',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'accounts', 'field' => 'id', 'return' => 'login' ),
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'account',
  'title' => 'Пользователь',
  'virtual' => 'account_id',  
  'show_in' => array('default'),
  ));    
    
  
  $this->init_field(array(
  'name' => 'product_id',
  'title' => 'Продукт',
  'type' => 'int',  
  'input' => 'select',
  'belongs_to' => array('table' => 'products', 'field' => 'id', 'return' => 'title' ),
  'show_in' => array(),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'product',
  'title' => 'Продукт',
  'virtual' => 'product_id',  
  'show_in' => array('default'),
  ));    
    
  
  $this->init_field(array(
  'name' => 'sid',
  'title' => 'Сессия',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));    
      
  
  $this->init_field(array(
  'name' => 'count',
  'title' => 'Количество',
  'type' => 'int',  
  'len' => 10,
  'show_in' => array('default'),
  'write_in' => array('edit', 'create')
  ));    
    
  
  
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Кэшированное название товара',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'edit'),
  'write_in' => array()
  ));    
    
  $this->init_field(array(
  'name' => 'price',
  'title' => 'Кэшированная цена',
  'type' => 'float',  
  'len' => 10,
  'show_in' => array('default', 'edit'),
  'write_in' => array()
  ));    
    
  $this->init_field(array(
  'name' => 'order_id',
  'title' => 'Заказ №',
  'type' => 'int',  
  'len' => 10,  
  'show_in' => array('default', 'edit'),
  'write_in' => array(),
  'selector' => 1
  ));    
    
    
    
}

function before_scaffold() {
  global $sv;
  
  $this->auth = ($sv->user['session']['account_id']>0) ? 1 : 0;
  $this->account_id = ($sv->user['session']['account_id']>0) ? $sv->user['session']['account_id'] : 0;
}

// VALIDATIONS
function v_count($v) {
  
  $v = abs(intval($v));
  $v = ($v>9999) ? 9999 : $v;
  
  return $v;
}
function last_v($p) {
  global $sv;
  
  if( $this->code='create') {
    $p['sid'] = $sv->user['session']['sid'];
  }
  
  return $p;
}

// PUBLIC_CONTROLLERS
function c_public_add() {
  global $sv;

  $err = 0;
  
  $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  if (!$this->is_valid_product_id($id)) {
    $err = 1;
    $this->errm("Неверный идентификатор товара", $err);
  }
  
  if (!$err) {
    $this->add2db($id);
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  $ret['basket'] = $this->basket_content();
  
  return $ret;
}

function c_public_default() {
  global $sv;
  
  
  $s = $this->init_submit();
  $ret['basket'] = $this->basket_content();
  
  return $ret;
}

function c_public_order() {
  global $sv;
  
  $s = $this->init_submit();
  
  
  
  $ret['s'] = $s;
  return $ret;
}

function sc_public_order($n) {
  global $sv;
  
  
  // 1. формируем список товаров
  $content = $this->txt_basket_content();

  // 2. добавляем заказ
  $sv->load_model('order');
  $sv->m['order']->c_content = $content;
  $order = $sv->m['order']->scaffold("public_new");
  $ret['order'] = &$order;
  
  // 3. обновлям товары скрываем в корзине
  if ($order['insert_id']) {
    $this->basket2order($order['insert_id']);
    $sv->m['order']->send_notify($order['insert_id']);
    $this->errm("Ваш заказ успешно отправлен, номер заказа: {$order['insert_id']}, ожидайте звонка менеджера.");
    $this->errm("<a href='/'>Вернуться на главную</a>");
    $ret['no_form'] = 1;
  }
  
  return $ret;
}

function sc_public_default($n) {
  global $sv, $db;
  
  if (isset($n['count']) && is_array($n['count'])) {
    foreach($n['count'] as $id => $count) {
      $count = $this->validate_field('count', $count, 1); 
      $id = intval($id);
      $p = array('count' => $count);
      $this->update_row($p, $id);
    }
  }
  
}

function c_public_empty() {
  global $sv, $db;
  
  $aid = $db->esc($sv->user['session']['account_id']);
  $sid = $db->esc($sv->user['session']['sid']);
  $wh = ($this->auth && $aid<>0) ? "`account_id`='{$aid}'" : "`sid`='{$sid}'";
  //только товары корзины
  $wh .= " AND `basket`='1'";
  
  $this->remove_items_wh($wh);
  $this->errm("Корзина успешно очищена.");
  
  $ret['basket'] = $this->basket_content();
  
  return $ret;
}
/**
 * добалвяем товар в корзину в базе
 *
 * @param unknown_type $id
 * @return unknown
 */
function add2db($id) {
  global $db, $sv;
  
  $err = 0; $ret = 0;
  $p = array(
    'account_id' => $sv->user['session']['account_id'],
    'product_id' => $id,
    'count' => 1,
    'sid' => $sv->user['session']['sid'],
    'basket' => 1,
    'price' => $this->last_product['price'],
    'title' => $this->last_product['title']
  );
  
  $d = $this->get_item_wh("`account_id`='".$db->esc($p['account_id'])."' AND `product_id`='".$db->esc($p['product_id'])."' AND `sid`='".$db->esc($p['sid'])."' AND `basket`='1'");
  if ($d) {
    $err = 1;    
    $this->errm("Этот товар уже есть у вас в корзине.", $err);
  }
  else {
    $ret = $this->insert_row($p);
    $this->errm("Товар успешно добавлен в корзину.");
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  return $ret;
}

/**
 * Переводим содержимое корзины в другой статус
 *
 * @param int $order_id
 */
function basket2order($order_id) {
  global $sv;
  
  $wh = $this->basket_content_wh();
  $p = array(
    'order_id' => $order_id,
    'basket' => 0,
  );
  
  return $this->update_wh($p, $wh, 1);
}

// условие запроса всех товаров в корзине для текущего юзера
function basket_content_wh() {
  global $sv, $db;
  
  $wh = ($this->auth) ? "`account_id`='".$db->esc($this->account_id)."'" : "`sid`='".$db->esc($sv->user['session']['sid'])."'";
  $wh .= " AND `basket`='1'";
  return $wh;
}
/**
 * Содержимое корзины в зависимости от типа
 *
 * @return array 
 */
function basket_content() {
  global $sv, $db;
  
  $keys = array();
  $counts = array();
  
  // присваиваем аккаунты тем кто анонимусом добавлял и авторизовался
  $this->sync_sids();
  
  // удаляем пустые
  $this->remove_rows_wh("`basket`='1' AND `count`='0'");
  
  
  $wh = $this->basket_content_wh();
  
  $basket = $this->item_list($wh, "`created_at` ASC", 0, 1);
  foreach($basket['list'] as $d) {
    $keys[] = "'".$db->esc($d['product_id'])."'";
  }

  

  $products = array();
  $ret = array(); $sum = 0;

  // выбираем из базы товары и названия
  if (count($keys)>0) {
    $in = implode(", ", $keys);
    $sv->load_model('product');
    $ar = $sv->m['product']->product_list_wh("{$sv->m['product']->t}.id IN ({$in})", "{$sv->m['product']->t}.title ASC", 0, 1);
    foreach($ar['list'] as $d) {
      $products[$d['id']] = $d;
    }
  }
  
  foreach($basket['list'] as $d) {
    $d['product'] = (isset($products[$d['product_id']])) ? $products[$d['product_id']] : false;    
    $d['sum'] = $d['product']['price']*$d['count'];
    $d['f_sum'] = number_format($d['sum']);
    $sum += $d['sum'];
    $ret[] = $d;    
  }
  $this->last_f_sum = number_format($sum);
  
  return $ret;
}

/**
 * Оформленное в виде текста содержимое корзины basket_content()
 *
 * @return string
 */
function txt_basket_content() {
  global $sv;
  $ar = $this->basket_content();
  
  $url = preg_replace("#/$#si", "", $sv->vars['site_url']);
  
  $tr = array(); $sum = 0; $i = 0;
  foreach($ar  as $d) { $i++;
    $p = &$d['product'];
    $sum += $d['sum'];    
    $tr[] = "{$i}) Товар: {$p['title']} \n    Адрес: {$url}{$p['url']} \n    Цена, количество: {$p['f_price']} x {$d['count']} шт. = {$d['f_sum']} руб. ";
  }
   
  $ret = 
"
--------------------------
".
implode("\n ---\n", $tr)
."
--------------------------
Итого к оплате: {$sum} руб.
";
  return $ret;
}

function ajax_basket_content() {
  global $sv;
  
  $this->before_scaffold();
  
  $ar = $this->basket_content();
  $count = count($ar);
  if ($count>0) {
    switch ($count) {
      case 1: $des = 'товар'; break;
      case 2: case 3: case 4: $des = 'товара'; break;
      default: $des = 'товаров';
    }
    $ret = "<span><b><a href='/shop/basket/' style='color:yellow;font-size: 120%;' >{$count} {$des}</a></b></span>";
  }
  else {
    $ret = "<span>Пока ничего нет.</span>";
  }
  
  
  return $ret;
}

function ajax_basket_add($id) {
  global $sv;

  $err = 0;
  $ret = '';
  
  $id = intval($id);
  if (!$this->is_valid_product_id($id)) {
    $err = 1;
    $ret = "Неверный идентификатор товара.";
  }
  
  if (!$err) {
    $this->add2db($id);
    if ($this->err('add2db')) {
      $ret = "Этот товар уже есть у вас в корзине.";
    }
    else {
      $ret = "Товар добавлен в корзину.";
    }
  }
  
  if ($err) $this->errs[] = __FUNCTION__;
  
  return $ret;  
}

function is_valid_product_id($id) {
  global $sv;
  
  $sv->load_model('product');
  $d = $sv->m['product']->get_item($id);
  $this->last_product = $d;
  $ret = ($d) ? 1 : 0;
  
  return $ret;
}

/**
 * вызываетеся только для авторизованных
 * обновляем записи в корзине, укоторых сессия наша а ползователь гость
 *
 */
function sync_sids() {
  global $sv, $db;
  
  if ($sv->user['session']['account_id']<=0) return false;
  
  $p = array(
   'account_id' => $sv->user['session']['account_id']
  );
  $this->update_wh($p, "`sid`='".$db->esc($sv->user['session']['sid'])."' AND `account_id`='0' AND `basket`='1'");
}

//eoc
}

?>