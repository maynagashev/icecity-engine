<?php

$sv->load_model('press');

$cat_id = (isset($dblock_vars[0])) ? $dblock_vars[0] : '';
$cat_id = (in_array($cat_id, array_keys($sv->m['press']->cats))) ? $cat_id : '';

if ($cat_id=='') {
  $dblock_content = " cat_id не указан ";
}
else {
  $ar = $sv->m['press']->item_list("`status_id`='1' AND `cat_id`='".$db->esc($cat_id)."'", "`date` DESC", 0, 1);
  $tr = array();
  foreach($ar['list'] as $d) {
    $date = $std->time->format($d['date'], 0.6, 1);
  $tr[] = <<<EOD
  
 <div id="smi_{$d['id']}" class="FlexOpen">
            <div class="bOpen">
            <div class="flexHeader clearFix" onclick="return collapseBox(smi_{$d['id']}, this, 0.90, 0.50, 0, 1)" onfocus="blur()">
            <div class="sec_header">{$d['title']} ({$date})</div>
            </div>
        </div>
   
        <div class="c" style="display: none;">

        <div class="flexBox clearFix">
        {$d['text']}        
   </div></div></div>
     
EOD;
  
  }
  
  
  $dblock_content = implode("\n", $tr);
}

?>