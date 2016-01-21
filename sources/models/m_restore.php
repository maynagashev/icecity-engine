<?php

/*
Example

function c_recovery() {
  global $sv;
  
  $sv->load_model('restore');
  $sv->m['restore']->table = 'blogs';
  $sv->m['restore']->activate_url = $this->url.$sv->code."/?key=";

  
  if (isset($sv->_get['key']))  {
    $sv->_post['new']['key'] = $sv->_get['key'];   
    $ret['todo'] = 'activate';
  }
  else {
    $ret['todo'] = 'sendemail';
  }
  
  $s = $this->init_submit();
  
	    
  
  $ret['s'] = $s;
  return $ret;
}
function sc_recovery($err, $errm, $n) {
  global $std, $sv, $db;

 
  if (isset($n['key'])) {
    $ret['show_form'] = 1;
    
    //activate     
    $key = $std->text->cut($n['key'], 'allow', 'mstrip');
    
    // check key
    $restore = $sv->m['restore']->check_key($key, 1);
    
    $err = ($restore['err']) ? 1 : $err;
    $errm = array_merge($errm, $restore['errm']);
        
    if (!$err) {
      $user = $this->get_item($restore['d']['uid']);
      if ($user===false) { 
        $err = 1;
        $errm[] = "Пользователь не найден в нашей базе данных, 
        возможно он был удален, либо ошибка восстановления, сообщите администратору";
      }
      else {          
        $ret['key'] = $key;
        $ret['user'] = $user;
      }
    }
    if ($err) {
      $ret['show_form'] = 0;
    }
    
    // on submit
    if (!$err && (isset($n['pass']) && isset($n['pass2']))) {
      if ($n['pass']!==$n['pass2']) {
        $err = 1; 
        $errm[] = "Введенные пароли не совпадают.";
      }
      else {
        $n['pass'] = $std->text->cut($n['pass'], 'allow', 'mstrip');
        $r = $this->set_new_password($n['pass'], $user['id']);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
        
      }
      
      if (!$err) {
        $r = $sv->m['restore']->set_used_key($key);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
      }
      
      if (!$err) {
        $ret['show_form'] = 0;
      }
    }
    
    
    
  }
  else {        
    // send email
      switch($n['use']){
        case 'login':
          $str = $std->text->cut($n['login'], 'allow', 'madd');
          $d = $this->get_item_wh("`login`='{$str}'");
          if ($d===false) { 
            $err = 1;
            $errm[] = "Логин не найден в нашей базе данных.";
          }
        break;
        case 'email':
          $str = $std->text->cut($n['email'], 'allow', 'madd');
          $d = $this->get_item_wh("`email`='{$str}'");
          if ($d===false) { 
            $err = 1;
            $errm[] = "Такой адрес не найден в нашей базе данных.";
          }
        break;
        default:
          $err = 1;
          $errm[] = "Неверно заданы параметры.";
      }
      
    
      if (!$err) {
        $r = $sv->m['restore']->send_key($d['email'], $d['id'], $d['login']);
        $err = ($r['err']) ? 1 : $err;
        $errm = array_merge($errm, $r['errm']);
      }
  }    
 	
	$ret['err'] = $err;
	$ret['errm'] = $errm;
	$ret['v'] = $v;
	
	return $ret;
}



*/


class m_restore extends class_model {

  var $tables = array(
    'restore' => "
      `id` int(11) NOT NULL auto_increment,
      `uid` int(11) NOT NULL default '0',
      `key` varchar(255) default NULL,
      `time` int(11) NOT NULL default '0',
      `email` varchar(255) default NULL,
      `status_id` tinyint(3) NOT NULL default '0',
      `ip` varchar(255) default NULL,
      `login` varchar(255) default NULL,
      `activate_ip` varchar(255) default NULL,
      `table` varchar(255) default null,
      `created_at` datetime null,
      `created_by` int(11) not null default '0',
      `updated_at` datetime null,
      `updated_by` int(11) not null default '0',  
      PRIMARY KEY  (`id`),
      KEY (`table`)    
    "
  );
    
  var $table = 'account';
  var $activate_url = "?restore_key=";
    
  var $status_ar = array(
0 => 'Запрос отправлен',
1 => 'Запрос подтвержден'
  
  );
  
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['restore'];
  $this->per_page = 50;    
  $this->activate_url = $sv->vars['site_url'].$this->activate_url;
  

  $this->init_field(array(
  'name' => 'time',
  'title' => 'Time',
  'type' => 'int',  
  'len' => '15',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
  
  

  $this->init_field(array(
  'name' => 'f_time',
  'title' => 'F_Time',
  'virtual' => 'time',  
  'show_in' => array('default', 'remove'),
  'write_in' => array(),
  ));    
  
  
  $this->init_field(array(
  'name' => 'key',
  'title' => 'Restore Key',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  ));    
  

  $this->init_field(array(
  'name' => 'uid',
  'title' => 'User ID',
  'type' => 'int',  
  'len' => '15',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
    
  $this->init_field(array(
  'name' => 'email',
  'title' => 'Email',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    

   

  $this->init_field(array(
  'name' => 'status_id',
  'title' => 'Status ID',
  'type' => 'int',  
  'len' => '15',
  'input' => 'select',
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('list' => $this->status_ar)
  ));    
    
    
  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    

  $this->init_field(array(
  'name' => 'login',
  'title' => 'Login',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
  
  $this->init_field(array(
  'name' => 'activate_ip',
  'title' => 'Activate IP',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
  
  
  $this->init_field(array(
  'name' => 'table',
  'title' => 'Table',
  'type' => 'varchar',  
  'len' => 40,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  ));    
  
   
}

function df_f_time($t) {
  return $this->vcb_f_time($t);
}
function vcb_f_time($t) {
  global $std;  
  return $std->time->format($t, 0.5);
}

// main actions

function send_key($email, $uid = 0, $login = '') {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
   
	
	// checking email format
	if (!$err) {
  	if (!$std->text->v_email($email)) {
  	  $err = true;
  	  $errm[] = "Email не подходит под формат.";
  	}
	}
	
	
	if (!$err) {
	  $p = array();
	  $p['email'] = $email;
	  $p['key'] = $this->gen_key();
	  $p['time'] = $sv->post_time;
	  $p['ip'] = $sv->ip;
	  $p['status_id'] = 0;
	  $p['uid'] = $uid;
	  $p['login'] = $login;
	  $p['table'] = $this->table;
	}
	

  // check flood  
  if (!$err) {
    $exp = $sv->post_time - 60*15;       
    if ( $this->select_count_wh("`ip`='{$sv->ip}' AND time>'{$exp}' AND status_id='0'") > 0 ) {
      $err = 1;
      $errm[] = "Слишом частые запросы на восстановление с вашего IP, попробуйте восстановить пароль попозже (15 мин).";      
    }    
  }
  
  //sending mail
  if (!$err) {
    $sent = $this->send_mail($p['email'], $p['key'], $p['login']);
    if (!$sent) {
      $err = true;
      $errm[] = "Неудалось отправить email, возможно нет связи с интернетом, попробуйте восстановить пароль позже.";
    }
  }
  
  
	if (!$err) {
	  $this->insert_row($p);
	  
	  $iid = intval($db->insert_id());
	  
	  $af = $db->af();
	  
	  if ($af>0) {
	    $errm[] = "Дальнейшие инструкции по восстановлению пароля высланы на указанный при регистрации EMAIL, 
	    проверьте свой почтовый ящик.<br><br>
	    Если письмо долго не приходит и у вас стоит какой-нибудь спам-фильтр не забудьте проверить его содержимое.
	    ";	 	    
	  }
	  else {
	    $err = true;
	    $errm[] = "Ошибка базы данных, не удалось создать сессию восстановления. Сообщите администратору.";
	  }
	}
  
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  return $ret;
}

//stuff 


function send_mail($email, $key, $login) {
  global $sv, $std;
   
    
    $msg['title'] = "Восстановление пароля на сайте {$sv->vars['site_url']}";
    $msg['email'] = $email;    
        
    $msg['text'] = 
"
Здравствуйте, {$login}, вы только что воспользовались 
системой восстановления пороля на проекте {$sv->vars['site_title']} [ {$sv->vars['site_url']} ]. 

Если вы не делали запроса на восстановление, то просто проигнорируйте данное письмо
и не переходите по указанной ниже ссылке.

Чтобы завершить восстановление, перейдите по следующей ссылке:

-----------------------------------------------------------
{$this->activate_url}{$key}
-----------------------------------------------------------


Приятного дня!


";
   
  
   $ret = $std->mail->from_default($msg['email'], $msg['text'], $msg['title']);    
 
   
   return $ret;
}



/**
 * generate unque key
 *
 * @return unknown
 */
function gen_key() {
  
  $ret = md5(uniqid(""));
  //$ret = (strlen($ret)>12) ? substr($ret, 0, 12) : $ret;
  
  return $ret;
}


function check_key($key, $check_ip = 0) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  
  $key = addslashes($key);
  $d = $this->get_item_wh("`key`='{$key}' AND `status_id`='0'");
  
  if ($d===false) {
    $err = 1;
    $errm[] = "Ключ активации неверен, либо устарел.";
  }
  
  
  if (!$err && $check_ip) {
    if ($d['ip']!==$sv->ip) {
      $err = 1;
      $errm[] = "Ваш IP адрес изменился <b>{$d['ip']} &rarr; {$sv->ip}</b> <br>
      попробуйте заново запросить восстановление с нового IP.";
    }
  }
  
  
  
  $ret['d'] = $d;
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}


function set_used_key($key) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  
  
  $key = addslashes($key);
  $d = $this->get_item_wh("`key`='{$key}'");
  
  if ($d===false) {    
    $err = 1;
    $errm[] = "Ключ ({$id}) не найден в базе восстанвления";
  }
  else {
    
    // update key
    $p = array(
      'status_id' => 1,
      'activate_ip' => $sv->ip
    );
    $this->update_row($p, $d['id']);
    
    //update same 
    $email = addslashes($d['email']);
    $uid = addslashes($d['uid']);
    $db->q("UPDATE {$this->t} SET `status_id`='1' WHERE `email`='{$email}' AND `uid`='{$uid}'", __FILE__, __LINE__);
    
  }
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}

//eoc
}


/*

template example



<table width="100%" cellpadding="0" cellspacing="0">
  <tr valign="top">
    <td width="60%">
    <h3 class="title">Восстановление пароля</h3>
    {$ar.s.err_box}
    
    {if $ar.todo=='sendemail'}
    
      
      {if !$ar.s.submited || ($ar.s.submited && $ar.s.err)}   
      
          <div style='margin: 10px 20px;'>
          Введите свой логин для входа или email который вы указывали при регистрации, 
          на этот email придет письмо с инструкцией по восстановлению пароля.        
          </div>
          
          <form action="{$m->url}{$sv->code}/" method="post" enctype="multipart/form-data">
          <div style='margin: 20px 30px; padding: 10px;background-color: #efefef; border: 1px solid #cccccc;'>
            <table>
              <tr>
                <td>Логин: </td>
                <td><input type='text' id='use_login' name='new[login]' value='' size="30"></td>
                <td><input type='radio' value='login' name='new[use]' checked 
                onclick="$('#use_login').attr('disabled', false);$('#use_email').attr('disabled', true);"></td>
              </tr>
              <tr><td colspan="2" align="center">ИЛИ</td></tr>
              <tr>
                <td>Email: </td>
                <td><input type='text' id='use_email' name='new[email]' value='' size="30" disabled></td>
                <td><input type='radio' value='email' name='new[use]' 
                onclick="$('#use_login').attr('disabled', true);$('#use_email').attr('disabled', false);"></td>
              </tr>
              <tr><td></td><td style='padding: 10px 0 0 0;'><input type="submit" value="Далее"></td></tr>
            </table>
          </div>    
          </form>
          
      {/if}
    
    {else}
   
      {if $ar.s.show_form} 
        <div style='margin: 10px 20px;'>
        Введите новый пароль для учетной записи <b>{$ar.s.user.login}</b>:        
        </div>
           
        <form action="{$sv->m.restore->activate_url}{$ar.s.key}" method="post" enctype="multipart/form-data">
        <div style='margin: 20px 30px; padding: 10px;background-color: #efefef; border: 1px solid #cccccc;'>
          <table>
            <tr>
              <td>Новый пароль: </td>
              <td><input type='password' name='new[pass]' value='' size="30"></td>              
            </tr>
            <tr>
              <td>Подтверждение пароля: </td>
              <td><input type='password' name='new[pass2]' value='' size="30"></td>              
            </tr>          
            <tr><td></td><td style='padding: 10px 0 0 0;'><input type="submit" value="Далее"></td></tr>
          </table>
        </div>    
        </form>
      {/if}
        
    {/if}
    
    </td>
    <td width="40%" style='padding-left: 20px;'>
      {include file='blog/block-history.tpl'}     
     
    </td>
  </tr>
</table>



*/
?>