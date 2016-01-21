<?php

class users {  
function auto_run()   {
  global $sv;  
  $sv->load_model('user');  
  return $sv->m['user']->scaffold();
}
}

?>