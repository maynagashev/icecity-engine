<?php

class daily {
  
function auto_run() {
  global $sv, $std;
  
  $sv->init_class('model');
  

  //SUBSCRIBE 
  $sv->load_model('subscriber');
  $sv->m['subscriber']->daily_sender();

  
  // TAGS
  $sv->load_model('tag');

  $ar['log'] = $sv->m['tag']->aims_cloud(2);
  ec("AIMS CLOUD ======================");
  //ec(implode("\n", $ar['log']));
  
  $ar['log'] = $sv->m['tag']->articles_cloud(2);
  ec("ARTICLES CLOUD ======================");
  //ec(implode("\n", $ar['log']));    
  
  $ar['log'] = $sv->m['tag']->blog_clouds(2);
   ec("BLOG CLOUD ======================");
  //ec(implode("\n", $ar['log']));    
}
  
}

?>