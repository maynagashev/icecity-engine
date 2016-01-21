<?php
class mysql {
  
  var $fatal_errors = 1;  
	var $errors = array();
	var $file = "";
	var $vars = array(
	  'host' => 'localhost',
	  'user' => 'root',
	  'pass' => '',
	  'prefix' => '',
	  'debug' => 0,
	  'charset' => 'utf8', // кодировка устанавливаемая после соединения, если false то не устанавливается
	  'tables' => false,   // если false используется массив из $sv->tn,
	  'log_changes' => 0,
	);
	var $form = 0;
	var $connect_id	= 0;
	var $query_id = 0;
	var $query_count = 0;
	var $record_row = 0;
	var $default_config = "mysql_cfg.php";
	var $last_query = "";
	
	var $old_compatible = 0;
	
	/**
	 * $db->log_changes = 1;
      $db->changes_ignore_pattern = "(accounts|sessions|adv_show)"; 
	 *
	 * @var unknown_type
	 */
	
	var $log_changes = 0;
	var $changes_ignore_pattern = "(update|delete\s*from)\s*(accounts|sessions|adv_show|urls)";
	
function __construct($file = '') {
  $this->mysql($file);
}
	
function mysql($file = '') 	{
		
	$this->file = ($file!='') ? $file : $this->default_config;	
  $this->log = array();
  
	
	$this->errors = array();

	if (!file_exists($this->file)) 	{
		$this->update_file($this->vars);
	}
	else 	{
		require_once($this->file);
    
		if (!isset($db_init_vars['host'])){   $this->errors[] = "Не задан адрес базы данных."; }
		if (!isset($db_init_vars['user'])){   $this->errors[] = "Не задан пользователь для подключения к базе"; }
		if (!isset($db_init_vars['pass'])){   $this->errors[] = "Не задан пароль для подключения к базе"; }
		if (!isset($db_init_vars['dbname'])){ $this->errors[] = "Не указано название базы для работы."; }
    if (!isset($db_init_vars['prefix'])){ $db_init_vars['prefix'] = $this->vars['prefix']; }
    if (!isset($db_init_vars['debug'])) { $db_init_vars['debug'] = $this->vars['debug']; }
    if (!isset($db_init_vars['charset'])) { $db_init_vars['charset'] = $this->vars['charset']; }
    if (!isset($db_init_vars['tables'])) { $db_init_vars['tables'] = $this->vars['tables']; }
    if (!isset($db_init_vars['log_changes'])) { $db_init_vars['log_changes'] = $this->vars['log_changes']; }
    
    
		if (count($this->errors)==0) {
			$this->vars = $db_init_vars;
		}			
		else {
			echo $this->error_report();
      die(); 
		}
	}


}


/**
 * Connect
 *
 */
function connect() 	{
	
  $err = 0;
  $errm = array();
	
	// Connect ===========================
	
	if ( ! $this->connect_id = mysql_connect( $this->vars['host'],$this->vars['user'],$this->vars['pass'] ) ) {
	  $err = 1;
		$this->errors[] = "немогу подключиться к серверу {$this->vars['user']}@{$this->vars['host']} (pass_size:".strlen($this->vars['pass']).")";	
		$errm[] = "mySQL error: ".mysql_error();
		$errm[] = "mySQL error code: ".mysql_errno();
		$errm[] = "Date: ".date("l dS of F Y h:i:s A");		
		
		$this->errors[]="<textarea rows=\"10\" cols=\"60\" style='font-size:12px;'>".htmlspecialchars(implode("\n", $errm))."</textarea>";
	}
	
	// Select DB =========================
	if (!$err) {
  	if ( ! mysql_select_db($this->vars['dbname'], $this->connect_id) && $this->form!='1' )	{
  		$this->errors[] = "mySQL не может обнаружить базу данных с названием '".$this->vars['dbname']."'. Проверьте введённое Вами название базы SQL.";
      $err = 1;
  	}
	}
  
  if ($err == 1 && $this->fatal_errors) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $this->error_report();
    die();
  }
  else {
    // устанавливаем кодировку если задана
    if (isset($this->vars['charset']) && $this->vars['charset']) {
      $this->q("SET NAMES `{$this->vars['charset']}`", __FILE__, __LINE__);
    }
  }
  
}

/**
 * Close
 *
 */
function close() {
  mysql_close($this->connect_id);
  $this->connect_id = 0;
}

/**
 * Update file
 *
 * @param unknown_type $vars
 */
function update_file($vars) {

	$file_string = "<?php\n";
	
	foreach( $vars as $k => $v ) 	{
		if ($k!=""){$file_string .= '$db_init_vars['."'".$k."'".']'."\t\t\t=\t'".$v."';\n";};
	}
	
	$file_string .= "\n".'?'.'>';  
	
	if ( $fh = fopen($this->file, 'w' ) ) 	{
		fwrite($fh, $file_string, strlen($file_string) );
		fclose($fh);
	}
	else	{
		die('немогу открыть для записи '.$this->file);
	}

}

/**
 * Error report
 *
 * @return unknown
 */
function error_report() {
	
	$i=0; $list="";
	foreach($this->errors as $er) 	{
		$i++;
		$list.="<tr><td valign=top><small>{$i}.</td><td><small>{$er}</td></tr>";
	
	}

	$out = "		
	<b><small>Errors:</b></small>
	<table width=300>
	$list
	</table>
	<hr>
	
	";

	return $out;
}

/**
 * Query
 *
 * @param unknown_type $the_query
 * @param unknown_type $file
 * @param unknown_type $line
 * @return unknown
 */
function q($the_query, $file = __FILE__, $line=__LINE__) {
  global $sv;
  
  $this->last_query = $the_query;
  
	$t1 = microtime();
  $this->query_id = mysql_query($the_query, $this->connect_id);
  $t2 = microtime();
  
  $log = array('query'=>$the_query, 'file'=>$file, 'line'=>$line, 'time'=>$this->exec_time($t1, $t2));

  if ($this->query_id && preg_match("#^\s*select#msi", $the_query)) {   
    $log['rows'] = mysql_num_rows($this->query_id);      
  }
  
  // фиксация изменений в базе
  if ($this->vars['log_changes'] && $this->query_id && preg_match("#^\s*(update|delete)#si", $the_query)) { 
    if ($this->changes_ignore_pattern == '' || !preg_match("#{$this->changes_ignore_pattern}#si", $the_query))   {
      $log_text = "---\n{$sv->ip} [{$sv->user['session']['account_id']} = {$sv->user['session']['login']}] {$sv->date_time} {$sv->request_uri}\n{$the_query}\n---\n";
      ec($log_text, 0, 1);
    }
  }  
  
  $this->log[] = $log;
  
  if (! $this->query_id ) {              
    if ($this->vars['debug']) { 
      $this->show_warn("<b>$file: $line</b><br>".mysql_error());
    }
    else {
      $errm = "<b style='color:red;'>Ошибка запроса mySQL</b> 
            &mdash; включите отладочный режим для просмотра подробностей.";
      if ($this->fatal_errors) {
        die($errm);
      }
      else {
        $this->show_warn($errm);
      }
    }
  }		
  
  $this->query_count++;
    
  return $this->query_id;
}


/**
 * Query synonim
 *
 * @param unknown_type $the_query
 * @param unknown_type $file
 * @param unknown_type $line
 * @return unknown
 */
function query($the_query, $file = __FILE__, $line=__LINE__) {
   return $this->q($the_query, $file, $line);	
}  

/**
 * Fetch
 *
 * @param unknown_type $query_id
 * @return unknown
 */
function fetch_row($query_id = "") {
  
	if ($query_id == "") 	{
		$query_id = $this->query_id;
	}
	
  $this->record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
  
  return $this->record_row;    
}
/**
 * Fetch 2
 *
 * @param unknown_type $query_id
 * @return unknown
 */
function f($query_id = "") { 

	if ($query_id == "")	{
		$query_id = $this->query_id;
	}
	
  $this->record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
  
  return $this->record_row;    
}
/**
 * Fetch and stripslashes - deprecated!
 *
 * @param unknown_type $query_id
 * @return unknown
 */
function fs($query_id = "") {    
	if ($query_id == "")	{
		$query_id = $this->query_id;
	}    	
  
  $this->record_row = mysql_fetch_array($query_id, MYSQL_ASSOC);
  foreach ($this->record_row as $k=>$v) {$this->record_row[$k] = stripslashes($v);}
  
  return $this->record_row;        
}    

/**
 * Num rows
 *
 * @return unknown
 */
function get_num_rows() {
    return mysql_num_rows($this->query_id);
}
function nr() {
    return mysql_num_rows($this->query_id);
}	

/**
 * Affected rows
 *
 * @return unknown
 */
function af() {
    return mysql_affected_rows();
}	    
/**
 * Insert id
 *
 * @return unknown
 */
function insert_id() {
  return mysql_insert_id($this->connect_id);
}

/**
 * Создание таблицы
 *
 * @param string $tn
 * @param string $sql_fields
 */
function create_table($tn, $sql_fields) {

  $charset = (isset($this->vars['charset']) && $this->vars['charset'])  ? "DEFAULT CHARSET={$this->vars['charset']}" : "";

  $addon = ($this->old_compatible) ? "" : "ENGINE=MyISAM  {$charset}";
  $q = "
  CREATE TABLE IF NOT EXISTS `{$tn}` (
  {$sql_fields}
  )
  {$addon}
  ";
  
  $this->q($q, __FILE__, __LINE__);
  
}

/**
 * Error handler
 *
 * @param unknown_type $the_error
 */
function fatal_error($the_error="") {
	$the_error .= "\n\nmySQL error: ".mysql_error()."\n";
	$the_error .= "\nmySQL error code: ".$this->error_no."\n";
	$the_error .= "\nDate: ".date("l dS of F Y h:i:s A");
	
	$out = "<html><head><title>Ошибка при работе с БД</title>
		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
		   &nbsp;<br><br><blockquote><b>Обнаружена ошибка при работе с базой данных.</b><br>
		   Вы можете попробовать обновить данную страницу <a href=\"javascript:window.location=window.location;\">здесь</a>.
		   <br><br><b>Описание ошибки:</b><br>
		   <form name='mysql'><textarea rows=\"15\" cols=\"60\" style='font-size:12px;padding:15px;'>".htmlspecialchars($the_error)."</textarea></form></blockquote></body></html>";
		   

    echo($out);
    die("");
}


/**
 * Custom exec time
 *
 * @param unknown_type $t1
 * @param unknown_type $t2
 * @return unknown
 */
function exec_time($t1, $t2) {
  
  list($m1,$s1)=explode(" ",$t1);
	list($m2,$s2)=explode(" ",$t2);
	$time=($s2-$s1)+($m2-$m1);
	$time=round($time,4);

  return $time;
}


/**
 * Escaping
 * @param unknown_type $str
 * @return unknown
 */
function esc($str) {
  return mysql_real_escape_string($str, $this->connect_id);
}  

/**
 * Addslashes
 *
 * @param unknown_type $str
 * @return unknown
 */
function ad($str) {
  return addslashes($str);
}

/**
 * get SET string from AR
 *
 * @param unknown_type $p
 * @return unknown
 */
function parse_set($p, $use_addslashes = 0) {
  $s = array();
  foreach($p as $k=>$v) {
    $v = ($use_addslashes) ? addslashes($v) : $this->esc($v);
    $s[] = "`{$k}`='{$v}'";        
  }
  return implode(", ", $s);
}

/**
 * get IN string from AR
 *
 * @param unknown_type $p
 * @return unknown
 */
function parse_in($p, $use_addslashes=0) {
  $s = array();
  foreach($p as $k=>$v) {
    $v = ($use_addslashes) ? addslashes($v) : $this->esc($v);
    $s[] = "'{$v}'"; 
  }
  if (count($s)<=0) {
    $s[] = "'0'";
  }
  return implode(", ", $s);
}

/**
 * Fields in table
 *
 * @param unknown_type $table
 * @return unknown
 */
function fields_list($table) {
  
  $f = mysql_list_fields($this->vars['dbname'], $table, $this->connect_id);
  $cols = mysql_num_fields($f);
  $ar = array();
  for ($i = 0; $i< $cols; $i++) {
    $ar[] = mysql_field_name($f, $i);
  }
  
  return $ar;
}


function show_warn($text) {  
  echo "
  <div style='background-color:#f6f6f6;border:1px dashed black;margin:10px;padding:10px;color:black;'>{$text}</div>";
}



// REMOTE CONNECTIONS
/**
 * Remote Connect
 *
 */
function open_rc($host, $user, $pass, $dbname = "") 	{
	
  $err = 0;
  $errm = array();
	

	if ($rc = mysql_connect( $host, $user, $pass) ) {
    $this->tmp_connect_id = $this->connect_id;
    $this->connect_id = $rc;	  
    $this->rc_active = 1;    
	}
	else {
	  $err = 1;
	  $errm[] = $this->last_errm = "Can't connect to remote host {$user}@{$host}";
	}	
	
	if (!$err) {
	  $this->q("SET character_set_connection=cp1251, character_set_client=cp1251, character_set_results=cp1251");
	}
		
	if (!$err && $dbname!='') {	  
  	if (!mysql_select_db($dbname, $this->connect_id))	{  	
  		$errm[] = $this->last_errm = "mySQL не может обнаружить базу данных с названием '{$dbname}'. Проверьте введённое Вами название базы SQL.";      
  		echo $this->last_errm;
  	}
	}
  	  
	$ret = ($err) ? false : true; 
	return $ret;
}


function close_rc() {
  if (!$this->rc_active) {
    return false;
  }
  else {
    mysql_close($this->connect_id);
    $this->connect_id = $this->tmp_connect_id;
    $this->rc_active = 0;
  }
}

//eoc
}




?>