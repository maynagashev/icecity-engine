<?php
/* 
  новостная лента из рсс источников
*/

class rssnews {

var $codes = array('month', 'item', 'list');

function auto_run() {
  global $sv, $std, $db;  
  
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "list";  
  $sv->load_model('rss_data');  
  $ret = $sv->m['rss_data']->scaffold("public_".$sv->code);
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']); 
  
  return $ret; 
}

}

?>