<?php

class sms {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('sms');
  return $sv->m['sms']->scaffold();   
}
    
}

?>