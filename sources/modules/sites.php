<?php

class sites {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('site');
  return $sv->m['site']->scaffold();   
}
    
}

?>