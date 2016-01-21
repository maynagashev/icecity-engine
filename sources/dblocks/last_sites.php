<?php
$limit = 5;
$sv->load_model('site');
$ar = $sv->m['site']->item_list("`status_id`='1' AND `banned`='0'", "`created_at` DESC", $limit, 1);
$tr = array();

foreach ($ar['list'] as $d) {
  $date = $std->time->format($d['created_at'], 0.5, 1);
  $tr[] = "<tr><td><a href='{$d['url_details']}'>{$d['title']}</a></td><td class='date'>{$date}</td></tr>";
}

$dblock_content = "
<div class='last-sites cont'>
<div class='header'>Новые сайты в каталоге</div>
<table>".implode("\n", $tr)."</table>
</div>
";

?>