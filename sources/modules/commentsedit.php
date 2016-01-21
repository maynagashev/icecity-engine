<?php

class commentsedit {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('comment');
  return $sv->m['comment']->scaffold();   
}
    
}

?>