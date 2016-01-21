<?php

/* Модуль для отображения стандартных страниц со статичной инфой */

class page {
function auto_run() {
  global $sv, $db;
  
  $ret['d'] = $sv->view->page;  
  $ret['body'] = $sv->m['page']->get_content($sv->view->page['id']);  
  if ($sv->view->page['comments_on']) {
    $sv->load_model('comment');  
    $sv->m['comment']->object_title_field = 'p_title';
    $ret['comments'] = $sv->m['comment']->init_system('page', $sv->view->page['id'], 'guest');
  }
  return $ret;
} 

//eoc
}

?>