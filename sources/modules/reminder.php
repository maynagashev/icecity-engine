<?php

class reminder {
  
function auto_run()   {
  global $sv, $std, $db;
  
  $sv->load_model('account');
  $ret = $sv->m['account']->scaffold('public_reminder');
  
  return $ret;  
}
}
?>