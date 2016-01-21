<?php

class daystats{
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('daystat');
  return $sv->m['daystat']->scaffold();   
}
    
}

?>