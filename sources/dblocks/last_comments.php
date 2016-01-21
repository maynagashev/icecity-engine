<?php

$sv->load_model('comment');
$limit = (isset($dblock_vars[0]))? intval($dblock_vars[0]) : 5;

$ar = $sv->m['comment']->item_list("`approved`='1'", "`time` DESC", $limit*4, 1);
$tr = array();
foreach($ar['list'] as $d) {
  $date = $std->time->format($d['time'], 0.5, 1);
  if (isseT($tr[$d['url']])) continue;
  $tr[$d['url']] = "
  <tr valign=top>
    <tD class='object_title'><a href='{$d['url']}'>{$d['object_title']}</a><br />
    <span>{$date} @ {$d['username']}</span>
    </td>
  </tr>";
}
$dblock_content = "
<div class='last-comments cont'>
<div class='header'>Новые комментарии</div>
<table width='100%'>
".implode("\n", $tr)."
</table>

</div>
";
?>