<?php

/**
 * Дерево подразделов
 * @param int $root_id
 * 
 */

$root_id = (isset($dblock_vars[0])) ? intval($dblock_vars[0]) : 1;
$need_title = (isset($dblock_vars[1]) && $dblock_vars[1]) ? 1 : 0;

$tree = $sv->m['page']->build_tree($root_id, 1, 1, 0);
if (count($tree)>0) {
  $root = $tree[0];
  $tr = array();
  
 
  foreach($tree as $d) {
    if ($d['step']<1 || $d['status_id']!=1) continue;
    
    $pad = ($d['step']==2) ? " style='padding-left:10px; font-size: 90%;'" : "";
    $dash = ($d['step']==2) ? "-&nbsp;" : "";    
    $b = ($sv->view->page['id']==$d['id']) ? "<b>" : "";
    
    $s = ($sv->view->page['id']==$d['id']) ? "" : "-a";
    $link = ($sv->view->page['id']==$d['id']) ? "" : "<a href='{$d['url']}/'>";
    
    if ($d['step']>1) {
      $tr[] = "<div class='sublink{$s}'>{$link}{$d['title']}</a></div><p style='line-height: 0.4em;'>";
     
    }
    else {
      $tr[] = "<div class='link{$s}'>{$link}{$d['title']}</a></div><p style='line-height: 0.8em;'>";
    }
    
  }
 
  $t = ($need_title) ? " <div class='title'>{$root['title']}</div>" : "";
  $dblock_content = "
  {$t} 
  <br> ".implode("", $tr);
  
}
else {
  $dblock_content = "--список-пуст--";
}

?>