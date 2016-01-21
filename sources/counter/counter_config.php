<?
/**
 * Revision: 2, date 27.06.08
 */

require("../../mysql_cfg.php");
$installed = 0;
$db_prefix="counter_";

$script_name  = basename(getenv("SCRIPT_NAME"));
$current_ip	  = getenv("REMOTE_ADDR");
$post_time    = time();
$post_date    = date("Y-m-d", $post_time);

$db_prefix = (!isset($db_prefix)) ? "" : $db_prefix;

$t_online = $db_prefix."online";
$t_vars   = $db_prefix."vars";
$t_days   = $db_prefix."days";


if(!mysql_connect(  $db_init_vars['host'],  $db_init_vars['user'],  $db_init_vars['pass'])) { 
  die(mysql_error());
}
mysql_select_db($db_init_vars['dbname']);

if (!$installed){

	// INSTALLING TABLES 
	
	// online sessions list (15min)
	mysql_query("
	 CREATE TABLE IF NOT EXISTS {$t_online} (
	 `id` int(11) AUTO_INCREMENT, 
	 `ip` varchar(250), 
	 `time` int(11), 
	 `sid` varchar(255),
	 PRIMARY KEY (id),
	 KEY (`sid`),
	 KEY (`time`)
	 )");

  // day stats
	mysql_query("
	 CREATE TABLE IF NOT EXISTS {$t_days} (
	   `id` int(11) AUTO_INCREMENT, 
	   `date` date, 
	   `hosts` int(11) NOT NULL default '0', 
	   `views` int(11) NOT NULL default '0',
	   PRIMARY KEY (`id`)
	   )");
	
	
	// counter vars
	mysql_query("
	 CREATE TABLE IF NOT EXISTS {$t_vars} (
	   `id` int(11) AUTO_INCREMENT, 
	   `v_day` int(11) not null default '0', 
	   `h_day` int(11) not null default '0',
	   `c_date` date,
	   PRIMARY KEY (`id`)
	   )");
		

	
}

// read vars
$res = mysql_query("SELECT * FROM {$t_vars}");
$s = mysql_num_rows($res);

// sync
if ($s!=1){
  if ($s>1) {      
    mysql_query("DELETE FROM {$t_vars}");
  }
  mysql_query("INSERT INTO {$t_vars} SET `c_date`='{$post_date}'");    
  $res = mysql_query("SELECT * FROM {$t_vars}");
}
$vars = mysql_fetch_assoc($res);

?>