<?php

/**
 * Админская часть гостевой книги
 *
 */

class book {
  
function auto_run()   {
  global $sv, $std, $db;  
  $sv->load_model('book');
  return $sv->m['book']->scaffold();   
}
    
}

?>