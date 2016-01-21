<?php

$sv->load_model('blogpost');
$id = (isset($dblock_vars[0])) ? intval($dblock_vars[0]) : 0;
if ($id>0) {
  $d = $sv->m['blogpost']->get_item($id);
  if ($d) {
    $dblock_content = $d['replycount'];
  }
  else {
    $dblock_content = "[запись не найдена]";
  }
}
else {
  $dblock_content = "[неверно указан id]";
}


?>