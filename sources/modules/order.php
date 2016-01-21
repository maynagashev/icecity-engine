<?php

/**
 * Пользовательский публичный редактор заказов
 *
 */
class order {
  
var $codes = array(
  'history', 'details', 'purchase'
);

function auto_run()   {
  global $sv, $std, $db;  

  $sv->load_model('product');
  $sv->load_model('order');
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : 'history';

  $ret = $sv->m['order']->scaffold("public_".$sv->code);   
    
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']);
  return $ret;
}
    
}

?>