<?php

class attaches {  
function auto_run()   {
  global $sv;  
  $sv->load_model('attach');  
  return $sv->m['attach']->scaffold();
}
}

?>