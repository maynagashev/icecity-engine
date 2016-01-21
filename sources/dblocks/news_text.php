<?php

/**
 * Текст указанной по id новости.
 */

$sv->load_model('news');

$id = (isset($dblock_vars[0])) ? intval($dblock_vars[0]) : 0;
$d = $sv->m['news']->get_item($id, 1);
if ($d) {
  $dblock_content = nl2br($d['f_ann']);
}
else {
  $dblock_content = "Ошибка! Новость №{$id} не найдена.";
}



?>