<?php

/* Редактор инфоблоков/сниппетов */

class infoblocks {  
function auto_run()   {
  global $sv;  
  $sv->load_model('infoblock');  
  return $sv->m['infoblock']->scaffold();
}
}

?>