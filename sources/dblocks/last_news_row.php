<?php

$sv->load_model('news');
$sv->m['news']->news_url = "/news/";
$ar = $sv->m['news']->item_list("`status_id`='1'", "`date` DESC", 1, 1);

$row = '';
foreach($ar['list'] as $d) {
  $r = ($d['replycount']>0) ? "&nbsp;<span style='color:gray;'>({$d['replycount']})</span>" : '';
  $row = "<a href='{$d['url']}'>{$d['title']}</a>{$r}";
}


$dblock_content = "{$row} / <b><a href='/news/'>Все новости</a></b>";


?>