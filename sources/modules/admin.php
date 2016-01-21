<?php

class admin {
  
function auto_run() {
  global $sv, $db, $std;
  
  /*
  $sv->load_model('blog');
  $ret['new_blogs'] = $sv->m['blog']->item_list("`approved`='0'", "created_at DESC", 0, 1);
  */
  $sv->load_model('session');
  $sv->m['session']->clear_guests();  
  
  
  $db->q("SELECT 0 FROM {$sv->t['logs']} WHERE `type`='login'", __FILE__, __LINE__);
  $ret['pl'] = $pl = $std->pl($db->nr(), 10, $sv->_get['page'], u($sv->act)."&page=");
 
  $ar = array();
  $db->q("  SELECT l.* FROM {$sv->t['logs']} l
            WHERE `type`='login'
            ORDER BY id DESC {$pl['ql']}");
  
  while ($d = $db->f()) {
    $d['f_time'] = $std->time->format($d['created_at'], 0.5, 1);
    $ar[] = $d;
  }
  
  $ret['list'] = $ar;
  
  $exp = $sv->post_time - 60*15;
  $ar = array();
  $db->q("SELECT * FROM {$sv->t['sessions']} WHERE time>'{$exp}' ", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $d['f_time'] = $std->time->format($d['time'], 0.5);
    $ar[] = $d;
  }
  
  $ret['online'] = $ar;
  
  
  // counter
  $sv->init_class('counter');
  $sv->counter->url = $sv->vars['site_url']."sources/counter/";
  $ret['selector'] = $sv->counter->selector(2008, 2030, 0, '/admin/?admin');
  
  
  return $ret;
}
  
  
  
}
?>