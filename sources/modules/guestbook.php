<?php

/**
 * Публичная часть гостевой книги
 *
 */

class guestbook {  
   
var $codes = array('add'); 
function auto_run() {
  global $sv, $std, $db;  $ret = array();
    
  $sv->vars['styles'][] = "guestbook.css";
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']);
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "publiclist";
  
  $sv->load_model('book');
  $ret = $sv->m['book']->scaffold($sv->code);
  
  return $ret;
}

}
?>