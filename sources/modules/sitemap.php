<?php

class sitemap {
   
  var $tr = array();
  var $level = 0;
  
  var $parents = array();
  var $ar = array();
function auto_run()   {
  global $sv, $std, $db;
  
    
  $sv->load_model('page');
 
  $root = $sv->m['page']->init_root();
  
  /*
  $list = $sv->m['page']->item_list("parent_id='{$root['id']}'", "title ASC");
  */
  
  $ar = array();
  $parents = array();
  $db->q("SELECT p.id, p.parent_id, p.title, u.url 
          FROM {$sv->t['pages']} p
          INNER JOIN {$sv->t['urls']} u ON (u.page=p.id AND u.primary='1')
          WHERE p.status_id='1' AND p.sitemap='1' ORDER BY p.title", __FILE__, __LINE__);
  while($d = $db->f()) {
    $ar[$d['id']] = $d;
    $parents[$d['parent_id']][] = $d['id'];    
  }
  
  $this->parents = $parents;
  $this->ar = $ar;
  
  $this->tr = array();
  $this->level = 0;
  
  
  foreach($parents[null] as $parent => $child) {
    $this->level++;
    $d = $this->ar[$child];
    $d['url'] = ($d['url']=='/') ? "" : $d['url'];
    $this->tr[] = "<div class='level{$this->level}'><a href='{$d['url']}/'>{$d['title']}</a></div>";
    $this->parse_childs($child);
    
    $this->level--;    
  }
   
  $css = "
  
<style>
#sitemap div {
  padding: 3px;
}
#sitemap .level1 {   font-size: 100%; }
#sitemap .level2 {   padding-left: 20px;  }
#sitemap .level3 {   padding-left: 40px; } 
#sitemap .level4 {   padding-left: 50px; font-size: 95%;}
#sitemap .level5 {   padding-left: 70px; font-size: 90%; }
#sitemap .level6 {   padding-left: 80px; font-size: 85%; }
#sitemap .level7 {   padding-left: 85px; font-size: 85%;  }
#sitemap .level8 {   padding-left: 90px; font-size: 85%;   }
#sitemap .level9 {   padding-left: 95px; font-size: 85%;   }
</style>
  
  ";
  $ret['html'] = "<div id='sitemap'>".$css.implode("\n", $this->tr)."</div>";
  
  $ret['content'] = $sv->m['page']->get_content($sv->view->page['id']);
  return $ret;
}

function parse_childs($id) {
  
  if (!isset($this->parents[$id])) return false;
  $this->parents[$id] = array_unique($this->parents[$id]);
  
  foreach($this->parents[$id] as $parent => $child) {
    $this->level++;
     $d = $this->ar[$child];
     $pre = ($this->level<=3) ? "" : "";
    
     $d['url'] = ($d['url']=='/') ? "" : $d['url'];
    $this->tr[] = "<div class='level{$this->level}'>{$pre}<a href='{$d['url']}/'>{$d['title']}</a></div>";
    $this->parse_childs($child);
    
    $this->level--;    
  }
  
}


}


?>