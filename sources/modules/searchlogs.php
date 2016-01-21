<?php

class searchlogs {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('searchlog');
  return $sv->m['searchlog']->scaffold();   
}
    
}

?>