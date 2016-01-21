<?php

/* Редактор мероприятий, фильмов для афиши */

class  events {  
function auto_run()   {
  global $sv;  
  $sv->load_model('event');  
  return $sv->m['event']->scaffold();
}
}

?>