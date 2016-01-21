<?php

class hourly {
  
function auto_run()   {
  global $sv;
  
  $sv->init_class('model');
  
  $sv->load_model('bank');
  $sv->m['bank']->cron_hourly();
  
}
  
}

?>