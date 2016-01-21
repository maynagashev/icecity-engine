<?php
/* 
  публичная новостная лента с подробным просмотром 
*/

class newsline {

var $codes = array('month', 'item', 'list');

function auto_run() {
  global $sv, $std, $db;  
  
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "list";  
  $sv->load_model('news');  
  $ret = $sv->m['news']->scaffold("public_".$sv->code);
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']); 
  
  return $ret; 
}

}

?>