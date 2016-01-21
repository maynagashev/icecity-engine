<?php

class download {
  
function auto_run() {
  global $sv;
  
  $models = array('ventfile');
  
  $model = (isset($sv->_get['model']) && in_array($sv->_get['model'], $models)) ? $sv->_get['model'] : '';
  if ($model=='') {
    $sv->view->show_err_page("badrequest");
  }
  else {
    $sv->load_model($model);
    return $sv->m[$model]->call_controller('c_download');
  }
}

// eoc
}

?>