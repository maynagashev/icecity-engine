<?php

class bcats {
  
function auto_run()   {
  global $sv;  
  $sv->load_model('bcat');
  return $sv->m['bcat']->scaffold();   
}
    
}

?>