<?php

class wmresults {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('wmresult');
  return $sv->m['wmresult']->scaffold();   
}
    
}

?>