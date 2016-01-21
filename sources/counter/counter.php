<?
/**
 * Revision: 2, date 27.06.08
 */
require('counter_config.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
$sid = session_id();

$vars['v_day']++;

$exp_online = $post_time - 60*15;
mysql_query("DELETE FROM {$t_online} WHERE `time`<'{$exp_online}'");


if ( $post_date != $vars['c_date'] ) {
  $res = mysql_query("SELECT 0 FROM {$t_days} WHERE `date`='{$post_date}'");
  
  // updating prev date
  if (mysql_num_rows($res)>0) {
    mysql_query("UPDATE {$t_days} SET `hosts`='{$vars['h_day']}', `views`='{$vars['v_day']}' WHERE `date`='{$vars['c_date']}'");
  }
  else {
		mysql_query("INSERT INTO {$t_days} SET `hosts`='{$vars['h_day']}', `views`='{$vars['v_day']}', `date`='{$vars['c_date']}'");
  }
  
	$vars['v_day'] = 1;
	$vars['h_day'] = 1;
	$vars['c_date']  = $post_date; 
}

$res = mysql_query("SELECT * FROM {$t_online} WHERE `sid`='{$sid}'");
if (mysql_num_rows($res)>0) {
	mysql_query("UPDATE {$t_online} SET `time`='{$post_time}' WHERE `sid`='{$sid}'");
}
else {
	mysql_query("INSERT INTO {$t_online} SET `time`='{$post_time}', `sid`='{$sid}', `ip`='{$current_ip}'");
	$vars['h_day']++;
}

mysql_query("UPDATE {$t_vars} SET `v_day`='{$vars['v_day']}', `h_day`='{$vars['h_day']}', `c_date`='{$vars['c_date']}'");


?>