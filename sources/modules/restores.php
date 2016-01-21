<?php
class restores {  
function auto_run()   {
  global $sv;  
  $sv->load_model('restore');  
  return $sv->m['restore']->scaffold();
}
}

?>