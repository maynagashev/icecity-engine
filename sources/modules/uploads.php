<?php

class uploads {  
  var $m = null;
function auto_run() {
  global $sv, $db, $smarty;  
  
  $sv->sub_url = u($sv->act, '', $sv->id);
  
  
  $sv->load_model('upload');
  $this->m = &$sv->m['upload'];
  
  $db->q("SELECT * FROM {$sv->t['pages']} WHERE id='{$sv->id}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();
    $this->m->object = $d;  
    
  } else {
    die("Не найдена страница к которой должны быть прикреплены файлы.");
  }
  
  
  $sv->id = (isset($sv->_get['id'])) ? intval($sv->_get['id']) : 0;
  $sub = (isset($sv->_get['sub'])) ? $sv->_get['sub'] : "";
  $ar = $this->m->scaffold($sub);   
  $this->m->custom_titles['create'] = "Загрузить файл";
  $this->m->custom_titles['edit'] = "Редактирование информации о файле";
  $this->m->custom_titles['remove'] = "Удаление файла";
  
  
  $smarty->assign("sv", $sv);
  $smarty->assign("ar", $ar);
  $smarty->display("admin_frame.tpl");
  exit();
}
}
?>