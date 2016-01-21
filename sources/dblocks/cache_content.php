<?php

$sv->load_model('cache');
$code = (isset($dblock_vars[0])) ? $dblock_vars[0] : '';
if ($code=='') {
  $dblock_content = " [cache_id not defined]";
}
else {
  $dblock_content = $sv->m['cache']->read($code);
}

?>