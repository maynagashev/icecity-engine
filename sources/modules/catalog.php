<?php

class catalog {
  
var $codes = array(
  'index', 'cat', 'details', 'by_id'
);

function auto_run()   {
  global $sv, $std, $db;  

  $sv->load_model('product');
  
  $sv->vars['js'][] = "jquery.lightbox-0.4.min.js";
  $sv->vars['styles'][] = "jquery.lightbox-0.4.css";
  
  // 0. index  сттья о магазине, ходовые товары?
  $code = (in_array($sv->code, $this->codes)) ? $sv->code : 'index';
  
  $url = $sv->view->safe_url;

  // разбиваем урл на уровни удаляем пустые элементы
  $ar = explode("/", $url);
  $tar = array();
  foreach($ar as $k => $v) {    
    $v = trim($v);
    if ($v!='') {
      // очищаем заодно slugs
      $tar[] = preg_replace("#[^a-z0-9_\-\.]#si", "", $v);      
    }
  }
  $level = $tar;
  /* уровни
   0 - shop - общий
   1 - garden - cat / by_id
   2 - kachely - subcat
   3 - varadero - product
  */

  // инициализируем объекты начиная от категории до товара, действия с товаром
  
  // 1. cat содержимое глобальной категории - подкатегории, ходовые товары?
  if (isset($level[1])) {  
    // спец обработка
    if ($level[1]=='by_id') {
      $id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
      $d = $sv->m['product']->get_item($id);
      if ($d) {
        $sv->load_model('subcat');
        $subcat = $sv->m['subcat']->get_item_wh("`id`='{$d['subcat_id']}'");
        if ($subcat) {
          $level[1] = $subcat['cat_slug'];
          $level[2] = $subcat['slug'];
          $level[3] = $d['slug'];
        }
        else {
          $sv->view->show_err_page('notfound');
        }
      }
      else {
        $sv->view->show_err_page('notfound');
      }
    }
    
    $sv->load_model('cat');    
    $sv->m['product']->active_cat = $sv->m['cat']->get_item_wh("`slug`='".$db->esc($level[1])."'", 1); 
    $cat = &$sv->m['product']->active_cat;    
    if ($cat) {
      $code = 'cat';
      $sv->vars['p_title'] = $cat['title'];
    }
    else {
      $sv->view->show_err_page('notfound');
    }
    
  }
 
  // 2. subcat - содержимое подкатегории - список товаров?
  if (isset($level[2])) {
    $sv->load_model('subcat');
    $sv->m['product']->active_subcat = $sv->m['subcat']->get_item_wh("`cat_id`='".$db->esc($cat['id'])."' AND `slug`='".$db->esc($level[2])."'", 1);
    $subcat = &$sv->m['product']->active_subcat;    
    if ($subcat) {
      $code = 'subcat';
      $sv->vars['p_title'] = $cat['title']." - ".$subcat['title'];
    }
    else {
      $sv->view->show_err_page('notfound');
    }    
  }
  
  // 3. product
  if (isset($level[3])) {
    $sv->m['product']->active_product = $sv->m['product']->get_item_wh("`subcat_id`='".$db->esc($subcat['id'])."' AND `slug`='".$db->esc($level[3])."'", 1);
    $product = &$sv->m['product']->active_product;  
   
    if ($product) {
      $code = 'details';
      $sv->vars['p_title'] = $subcat['title']." - ".$product['title'];
    }
    else {
      $sv->view->show_err_page('notfound');
    }    
  }


  $ret = $sv->m['product']->scaffold("public_".$code);   
  
  $ret['breadcrumb'] = $sv->m['product']->compile_breadcrumb($code);
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']);
  
  return $ret;
}
    
}

?>