<?php
/**
 * Авторизация для админки и публичной части.
 *
 */

class log {
    
  var $log = 1;  
  var $type = "log";
  
  /**
   * урлы по умолчанию - для админки
   *
   * @var unknown_type
   */
  var $return_url_success = "./";
  var $return_url_err = "./?auth";
  
function auto_run() {
  global $sv, $std, $smarty;    
  
  
  $sv->load_model('session');
  $sv->load_model('account');     
  if ($this->log) {
   $sv->load_model('logrecord');
  }
  
  $this->log = $sv->m['session']->make_log;
  
  $this->init_return_url();
  
  $this->check_new_field();
  
  // public rewrite action
  if (isset($sv->_get['action']) && in_array($sv->_get['action'], array('in', 'out'))) {
    $sv->code = $sv->_get['action'];
  }
    
  switch($sv->code) {
  	case "in":
  		$ar = $this->login();		
  	break;
  	case "out":
  		$ar = $this->logout();		
  	break;
  	default:
  	  // отображаем форму из log.tpl
  	break;
  }
  
  if ($sv->code=='in' || $sv->code=='out') {
    $smarty->assign('ar', $ar);          
    $smarty->display("parts/splash.tpl");    
    exit();
  }
    
  $ret['return_url'] = $this->return_url_success;
  return $ret;
}
  
function init_return_url() {
  global $sv, $std;
  
  $this->return_url_success = $sv->view->return_url_success;  
  $this->return_url_err = $sv->view->return_url_err;  
    
  if (isset($sv->_get['return_url'])) {   
    $this->return_url_success = $std->text->escape_url($sv->_get['return_url'], 1);
  }
}

function login() {
	GLOBAL $db, $sv, $std;

	// check for bruteforce	
	if ($this->log) {
	  $exp = $sv->post_time - 60 * $sv->m['session']->bruteforce_timeout;
	  $r = $sv->m['logrecord']->item_list("`ip`='{$sv->ip}' AND `type`='login' AND `status`='0' AND `time`>'{$exp}'", "", 0);
	  if ($r['count']>$sv->m['session']->bruteforce_count) {	    
	    $str = (isset($_POST['login'])) ? " [ login = {$_POST['login']} ]" : "";
	    $str .= (isset($_POST['password'])) ? " [ password = ".$std->text->mask_password($_POST['password'])." ]" : "";	    
	    $sv->m['logrecord']->write_log('login_block', $str, 0);	  
	   die("Слишком много неудачных попыток авторизации с вашего IP, попробуйте войти попозже, либо свяжитесь с администратором.");
	  }
	}
	
	if (!isset($_POST['login']) || !isset($_POST['password'])) {	  
	  if ($this->log) {
	    $sv->m['logrecord']->write_log('login', "Не заданы логин или пароль при входе.", 0);	  
	  }	  
	  die("Не заданы параметры входа.");
	}

    
	$login = $std->text->cut($_POST['login'], 'allow', 'mstrip');
	$mlogin = $std->text->mask($login);
	$elogin = addslashes($login);
	
	$password = $std->text->cut($_POST['password'], 'allow', 'mstrip');
	$mpassword = $std->text->mask_password($password);
	$hash = $sv->m['account']->password_hash($password);
	
  $d = $sv->m['account']->get_item_wh("`login`='{$elogin}' AND `password`='{$hash}' AND `active`='1'", 1);
  if ($d) {	  
    
		$sv->m['session']->auth_user($d['id'], $d);
		
    $msg = "Вы вошли как ".$mlogin;
	  $out['url'] = $this->return_url_success;
	  
	}
	else {
	  
		$msg = "Неправильная комбинация имя - пароль...";  			
		$out['url'] = $this->return_url_err;
		
    if ($this->log) {       
      $sv->m['logrecord']->write_log('login', "Неправильный пароль <b>{$mpassword}</b> для <b>{$mlogin}</b>", 0);
    }      
	}

	$out['msg'] = $msg;
	

  return $out;
}



function logout() {
	GLOBAL $db, $sv;

  $uid = (isset($sv->user['session']['account_id'])) ? intval($sv->user['session']['account_id']) : 0;
	  	
	if ($this->log) { 
	  $sv->m['logrecord']->write_log('logout', "Выход <b>{$sv->user['session']['login']}</b> [{$sv->user['session']['group_id']}]", 1);	
	}
			
	$db->q("DELETE FROM {$sv->m['session']->t} WHERE sid='{$sv->m['session']->sid}'", __FILE__, __LINE__);
	session_destroy();
	
	$msg = "Вы вышли...";
	
	$out['msg'] = $msg;
	$out['url'] = $this->return_url_success;


return $out;
}  

function check_new_field() {
  global $sv, $db;
  
  $t = $sv->m['session']->t;
  $ar = $db->fields_list($t);
  if (!in_array('foreign_pc', $ar)) {
    $db->q("ALTER TABLE `{$t}` ADD `foreign_pc` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `location`", __FILE__, __LINE__);
  }
  
}

}  
    
?>