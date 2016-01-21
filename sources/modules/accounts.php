<?php

/* Редактор учетных записей */

class accounts {  
function auto_run()   {
  global $sv;  
  $sv->load_model('account');  
  return $sv->m['account']->scaffold();
}
}

?>