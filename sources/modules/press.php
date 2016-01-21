<?php

/* Пресс-релизы и публикации в СМИ */

class press {  
function auto_run()   {
  global $sv;  
  $sv->load_model('press');  
  return $sv->m['press']->scaffold();
}
}

?>