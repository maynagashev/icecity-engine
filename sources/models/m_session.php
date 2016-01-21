<?php
/*
ALTER TABLE `sessions` CHANGE `location` `location` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `sessions` ADD `module` varchar(255) not null default '' AFTER `hits`;
ALTER TABLE `sessions` ADD INDEX ( `module` );
*/
class m_session extends class_model {
  
  var $tables = array(
    'sessions' => "
      `id` bigint(20) NOT NULL auto_increment,
      `sid` varchar(255) NOT NULL default '0',
      `group_id` int(11) NOT NULL default '0',
      `account_id` int(11) NOT NULL default '0',
      `time` int(11) NOT NULL default '0',
      `time_start` int(11) NOT NULL default '0',
      `login` varchar(255) null,
      `ip` varchar(20) null,
      `agent` varchar(255) null,
      `lastact` varchar(255) default '',
      `hits` int(11) NOT NULL default '0',
      `module` varchar(255) NOT NULL default '',
      `location` varchar(255) not null default '',
      `foreign_pc` tinyint(1) not null default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,  
      PRIMARY KEY  (`id`),
      KEY `sid` (`sid`),
      KEY `time` (`time`),
      KEY `user` (`account_id`,`group_id`),
      KEY (`module`)
    "
  );
    
	var $sid = 0;
	var $session_name = '';
	
  var $agent;

  var $bruteforce_timeout = 15; // min
  var $bruteforce_count = 100;  // for timeout
    
  /**
   * удалять сессиии в других броузерах?
   *
   * @var unknown_type
   */
  var $remove_other_sessions = 0;
  
  /**
   * по умолчанию настройки для админов
   *
   * @var int
   */
  
  var $cookie_time = 31536000;
  
  /**
   * Время если чужой компьютер
   *
   * @var int
   */
  var $foreign_time = 10800;
  
  /**
   * Время хранения гостевых анонимных сессий
   *
   * @var unknown_type
   */
  var $guest_time = 10800;
  
  var $per_page = 50;
  
  var $make_log = 1;
  
function __construct() {
  global $sv;  
  
	
	$this->cookie_time	= 60*60*24*365;
	$this->foreign_time = 60*60*3;
	$this->guest_time = 60*60*3;
	
  $this->t = $sv->t['sessions'];
 
    
  $this->init_field(array(
  'name' => 'sid',
  'title' => 'Session ID',
  'type' => 'varchar',
  'size' => '255',
  'len' => '50',
  'default' => '',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit') 
  ));  
  
  $this->init_field(array(
  'name' => 'group_id',
  'title' => 'Group ID',
  'type' => 'int',
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'), 
  'selector' => 1
  ));  
  
  $this->init_field(array(
  'name' => 'account_id',
  'title' => 'Account ID',
  'type' => 'int',
  'len' => '10',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));    
  
  $this->init_field(array(
  'name' => 'time',
  'title' => 'time',
  'type' => 'int',
  'input' => 'time',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
    
  
  $this->init_field(array(
  'name' => 'time_start',
  'title' => 'time_start',
  'type' => 'int',
  'input' => 'time',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));      
  
  $this->init_field(array(
  'name' => 'login',
  'title' => 'login',
  'type' => 'varchar',
  'len' => 30,  
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit'),
  'selector' => 1
  ));      
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'ip',
  'type' => 'varchar',
  'len' => 30,  
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));        
  $this->init_field(array(
  'name' => 'agent',
  'title' => 'agent',
  'type' => 'varchar',
  'len' => 80,  
  'show_in' => array( 'remove'),
  'write_in' => array('edit')
  ));        
  $this->init_field(array(
  'name' => 'lastact',
  'title' => 'lastact',
  'type' => 'varchar',
  'len' => 80,  
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
  $this->init_field(array(
  'name' => 'foreign_pc',  
  'title' => 'Чужой компьютер?',
  'type' => 'boolean',  
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'selector' => 0
  ));  
  
  $this->init_field(array(
  'name' => 'module',  
  'title' => 'Модуль',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'selector' => 1
  ));    
  $this->init_field(array(
  'name' => 'location',  
  'title' => 'Местонахождение',
  'type' => 'varchar',  
  'len' => 20,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
      
}

//parsers
function parse($d) {
  global $std;
  
  $d['f_time'] = $std->time->format($d['time'], 0.5, 0);
  
  return $d;
}

// stuff
/**
 * Starting new session 
 * 1. init vars
 * 2. call authorize
 *
 */
function start($auth = 1, $update_session = 0)  {
	GLOBAL $db, $sv;

	$expired_time	= $sv->post_time - $this->cookie_time;			
	$this->remove_rows_wh("`time`<'{$expired_time}'");
  
	$this->session_name = session_name();	
	session_set_cookie_params($this->cookie_time, "/", null, null, true);
	session_start();

	$this->sid = session_id();
	
	if ($auth) {
    $this->authorize($update_session);
	}
}

function authorize($update_session = 0) {
	GLOBAL $db, $sv;

	$sv->load_model('account');
	
	$db->q("SELECT * FROM {$this->t} WHERE `sid`='{$this->sid}'", __FILE__, __LINE__);
	$s_count = $db->nr();

	// если сессии найдены
	if ($s_count>=1) {
	  $d = $db->f();						

	  // если сессий больше одной
	  if ($s_count>1) {	
      $this->remove_items_wh("`id`<>'{$d['id']}'");
	  }
	}
	// убираем лишние
  elseif ($s_count<1) {
    $this->create_guest_session($this->sid);	
	  $db->q("SELECT * FROM {$this->t} WHERE sid='{$this->sid}' ", __FILE__, __LINE__);
    $d = $db->f();
	}

	// если чужой компьютер
	if ($d['foreign_pc']) {
	  // ставим куки до конца сессии
	  setcookie($this->session_name, $this->sid, 0, "/", null, null,  1);
	  
	  // удаляем сессию если слишком долго висим (чужой компьютер)
	  $expire = $sv->post_time-$this->foreign_time;
	  if ($d['time']<$expire) {
	    $this->session_expired();
	  }
	}
	// если домашний комп
	else {
	  //принудительно ставим стандартный срок жизни
	  setcookie($this->session_name, $this->sid, time()+$this->cookie_time, "/", null, null,  1);
	}
	
		
	$aid = intval($d['account_id']);
  $ip = $d['ip'];
  
	$sv->user['session'] = $d;	
	$sv->session_id = $d['sid'];

			
	// UPDATING TABLES	

	if ($aid>0) {
  	  
  	  $sv->user['account'] = $sv->m['account']->get_item($aid, 1);
  	  if (!$sv->user['account']) {
  	    $this->session_expired();  
  	  }  	  
  	 
  	  if ($this->remove_other_sessions) {
  	    $this->remove_items_wh("`sid`<>'{$this->sid}' AND `account_id`='{$aid}'");
  	  }
  		  	  
  	  // account last visit check  	
    	$p = array(
        'last_time' => $sv->post_time,
        'last_ip' => $sv->ip,
        'last_agent' => $sv->user_agent,
        'last_request' => $sv->request_url,
        'last_refer' => $sv->refer
    	);
    	
  	  if ($sv->post_time - $sv->user['account']['last_time'] > 60*15 ) {
    	  $last_visit = date("Y-m-d H:i:s", $sv->user['account']['last_time']);  	 
    	  $sv->user['session']['last_visit'] = $last_visit;
    	  $p['last_visit'] = $last_visit;
    	}
    	
    	$sv->m['account']->update_row($p, $aid);
  	}
  	
  if ($update_session) {
    $this->update_session();
  }
}

/**
 * Авторизация пользователя по указанному идентификатору
 *
 * @param unknown_type $account_id
 * @param unknown_type $d - запись из accounts
 * @return unknown
 */
function auth_user($account_id = 0, $d = false) {
  global $sv, $std;
  
  $sv->load_model('account');
  
  $d = (!$d) ? $sv->m['account']->get_item($account_id, 1) : $d;
  if (!$d) {
    $sv->view->show_err_page("session->auth_user: пользователь которого следует аторизовать не найден [account_id={$account_id}]");
  }
  
  // update account
  $p = array(
    'last_time' => $sv->post_time,
    'last_ip' => $sv->ip,
    'last_visit' => $sv->date_time
  );
  $sv->m['account']->update_row($p, $d['id']);
  		
  $foreign_pc = (isset($sv->_post['foreign_pc'])) ? 1 : 0;
  
  // update session  
  $p = array(
    'account_id' => $d['id'],
    'group_id' => $d['group_id'],
    'login' => $d['login'],
    'time_start' => $sv->post_time,
    'foreign_pc' => $foreign_pc
  );  
  $this->update_wh($p, "`sid`='{$this->sid}'");

  
  if ($this->make_log) {   
    $sv->load_model('logrecord');
    $sv->m['logrecord']->write_log('login', "{$d['login']} &mdash; {$d['f_group']}", 1);
  }       

  
  return $d;
}


function create_guest_session($sid) {
	GLOBAL $sv;
	
	$this->insert_row(
    array(
      'sid' => $sid,
      'time' => $sv->post_time,
      'time_start' => $sv->post_time,
      'ip' => $sv->ip,
      'agent' => $this->agent,
      'group_id' => 0,
      'account_id' => 0,
      'login' => '',
    )
	);

}

function session_expired() {
  global $sv, $db;
  
  $db->q("DELETE FROM {$this->t} WHERE sid='{$this->sid}'", __FILE__, __LINE__);
  session_destroy();
  
  $url = $sv->view->return_url_err;
  $url .= (preg_match("#\?[^/]*$#si", $url)) ? "&" : "?";
  $sv->view->show_err_page("Сессия устарела. <br><br>
  Необходимо <a href='{$url}return_url={$sv->view->safe_url}'>перезайти</a>.");
  
}

// STD

/**
 * Обновляем сессию когда уже известен используемый модуль
 *
 * @param unknown_type $module
 */
function update_session($module = '', $location = '') {
  global $sv;
  
  $module = ($module=='') ? $sv->act : $module;
  $location = ($location=='') ? $sv->vars['location'] : $location;
  
  $p = array(
    'time' => $sv->post_time,
    'agent' => $sv->user_agent,
    'ip' => $sv->ip,
    'lastact' => $sv->request_url, 
    'module' => $module,
    'location' => $location
    
  );
  $this->update_wh($p, "`sid`='{$this->sid}'");

}
function clear_guests() {
  $this->remove_rows_wh("`account_id`<='0' AND `time`<'{$this->guest_time}'");
} 

/**
 * @deprecated ?
 */
function set_location($location) {
  global $db;
  $this->update_wh(array('location' => $location), "`sid`='".$db->esc($this->sid)."'");
}

/**
 * @deprecated 
 */
function set_module($module) {
  global $db;
  $this->update_wh(array('module' => $module), "`sid`='".$db->esc($this->sid)."'");
}
//eoc
}  
  
?>