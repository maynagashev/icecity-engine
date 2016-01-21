<?php

class rssdata {
  
function auto_run()   {
  global $sv;
  
  $sv->load_model('rss_data');
  return $sv->m['rss_data']->scaffold();  
  
}
 
}

?>