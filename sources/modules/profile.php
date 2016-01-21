<?php
class profile {

  
var $codes = array('password', 'avatar');  
// accesslog в проекте

function auto_run() {
  global $sv, $std, $db;
  

  if ($sv->user['session']['account_id']<=0) {
    $sv->view->show_err_page('forbidden');
  }
  
  $sv->code = (in_array($sv->code, $this->codes)) ? $sv->code : "profile";
  
  $sv->load_model('account');
  return $sv->m['account']->scaffold("public_".$sv->code);  
}
}
?>