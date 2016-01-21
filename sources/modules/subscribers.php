<?php

class subscribers {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('subscriber');
  return $sv->m['subscriber']->scaffold();    
}
     
}

?>