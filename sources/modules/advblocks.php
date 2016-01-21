<?php

class advblocks {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('advblock');
  return $sv->m['advblock']->scaffold();   
}
    
}

?>