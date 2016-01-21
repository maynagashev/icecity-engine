<?php

class main {  
 
  
function auto_run() {
  global $sv, $std, $db;  $ret = false;
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']);
  
  $sv->load_model('news');  
  $ret['pinned'] = $sv->m['news']->item_list("status_id='2'", "`date` DESC", 0, 1);  
  $ret['news'] = $sv->m['news']->item_list_pl("status_id='1'", "`date` DESC", 10,  $sv->_get['page'], 1, "/?page=");
  
  $ret['months'] = $sv->m['news']->month_list(0);
  
  return $ret;
}



//eoc
}


?>