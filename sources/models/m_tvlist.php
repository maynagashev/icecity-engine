<?php

/*
список каналов - норком
*/

class m_tvlist extends class_model {

  var $tables = array(
    'tvlist' => "
      `id` int(10) NOT NULL auto_increment,
      `title` varchar(255) null,
      `text` text null,
      `image` varchar(255) null,
      `packs` text null,
      `votes` bigint(20) not null default '0',
      `rating` double(20,6) not null default '0',  
      `place` int(11) null,
      
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      
      PRIMARY KEY  (`id`),
      KEY (`place`)    
    "
  );
   
  var $url = "/cat/tv/channels.html";
  
  var $img_dir = "tvlist/";
  var $img_url = "tvlist/";
  var $ext_ar = array('gif', 'png', 'jpg');

  var $per_page = 40;

  function m_tvlist() {
    return $this->__construct();
  }
  
function __construct() {
  global $sv;  
  
  $this->t = $sv->t['tvlist'];
  
  if (defined('FILES_DIR')) {
    $this->img_dir = FILES_DIR.$this->img_dir;
    $this->img_url = FILES_URL."/".$this->img_url;
  }
 
  
    
  $this->init_field(array(
  'name' => 'place',
  'title' => 'Сортировка',
  'type' => 'int',  
  'len' =>  10,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')  
  ));    
    
  $this->init_field(array(
  'name' => 'title',
  'title' => 'Заголовок',
  'type' => 'varchar',  
  'len' =>  60,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit'),
  'unique' => 1
  ));  

    
  $this->init_field(array(
  'name' => 'text',
  'title' => 'Описание',
  'type' => 'text',  
  'len' => 80,
  'show_in' => array('default', 'remove'),
  'write_in' => array('create', 'edit')
  ));  
  
  $this->init_field(array(
  'name' => 'image',
  'title' => 'Логотип',
  'type' => 'varchar',
  'input' => 'file',
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit')
  )); 
  
  $this->init_field(array(
  'name' => 'image_view',
  'title' => 'Препросмотр файла',
  'show_in' => array('default', 'edit'),
  'virtual' => 'image'
  ));      
  
    
  $this->init_field(array(
  'name' => 'packs',
  'title' => 'Идентификаторы пакетов',
  'type' => 'varchar',  
  'input' => 'multiselect',
  'len' =>  60,
  'show_in' => array('remove'),
  'write_in' => array('create', 'edit'),
  'belongs_to' => array('table' => 'tvpacks', 'field'=> 'id', 'return' => 'title')
  ));  
  
  $this->init_field(array(
  'name' => 'packs_view',
  'title' => 'Пакеты',
  'show_in' => array('default', 'edit'),
  'virtual' => 'packs'
  ));      
  


   
}


//validations
function v_packs($val) {
    
  $ret = implode(",", $val);
  return $ret;
}

//upload image
function v_image($val) {
  global $sv, $std, $db;
  
  if ($this->v_err) return false;
  $err = 0;
  $name = "image";

  $c_file = (isset($this->d[$name])) ? $this->d[$name] : "";
  $dir = $this->img_dir;
  
  if (!$err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $dir, 0);      
    if ($file===false) {
      // не указан
      return $c_file;
    }
    $err = ($file['err']) ? true : $err;
    $this->v_errm = array_merge($this->v_errm, $file['errm']);
  }  
  
  
  if (!$err) {   
    // удаляем старый если был
    $r = $this->remove_files($c_file);
    $err = ($r['err']) ? 1 : $err;
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }
  
  if (!$err) {  
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
      $this->v_errm = array_merge($this->v_errm, $r['errm']);      
      $this->v_errm[] = "Файл успешно загружен.";  
    }
    else {
      //$this->v_err = true;
      $this->v_errm[] = "Не удалось переместить файл из временной папки: 
          {$file['tmp_name']} &rarr; {$file['savepath']}";     
      return false;
    }
  }
  else {
    $errm[] = "Ошибка работы с файлами сообщите администратору.";
  }
 
  return $file['savename'];

}

function remove_files($file) {
  
  $err = false;
  $errm = array();  
  if ($file=='') {    
    return array('err' => $err, 'errm' => $errm);
  }  
  $dir = $this->img_dir;
  
  $original = $dir.$file;
  if (!file_exists($original)) {
    $errm[] = "Оригинал фото не найден: {$original}";
  }
  else {
    if (!unlink($original)) {
      $err = 1;
      $errm[] = "Не удалось удалить оригинал фото: {$original}";
    }
  }
 
  $ret['err'] = $err;
  $ret['errm'] = $errm;  
  return $ret;
}


//callbacks
function df_packs_view($val) {
  global $db, $sv;
  
  $ar = $this->parse_multi_value($this->d['packs']);
  $in = $db->parse_in($ar, 1);
  
  $tr = array();
  $q = "SELECT * FROM {$sv->t['tvpacks']} WHERE id IN ({$in})";
  $r = $db->q($q, __FILE__, __LINE__);
  while($d = $db->f()) {
    $tr[] = "<div style='text-align:left;margin-bottom:5px;font-size: 80%;'><a href='".u('tvpacks', 'edit', $d['id'])."'>{$d['title']}</a></div>";
  }  
  $ret = "<div style='text-align:left:'>".implode("\n", $tr)."</div>";
  return $ret;
}
function vcb_packs_view($val) {
  return $this->df_packs_view($val);
}


function vcb_image_view($val) {
  global $db;  
  
  $fn = $this->d['image'];
  $path = $this->img_dir.$fn;
  $url = $this->img_url.$fn;

  $checkbox = "<div style='padding: 5px 0 0 0;'><input type='checkbox' name='new[remove_file]'>&nbsp;удалить</div>";
  
  if ($fn=='') {
    $ret = "отсутствует";
    $exists = 0;
  }
  elseif (file_exists($path)) {
    $exists = 1;    
    $ret = "<a href='{$url}' target=_blank><img src='{$url}' border='0'><br>{$fn}</a>{$checkbox}";
  }
  else {
    $exists = 0;
    $ret = ($fn=='') ? "не загружен" : "{$fn} - файл не найден{$checkbox}";
  }
  
  
  return $ret;
}
function df_image_view($val) {  
  $ret = ($val!='') ? "<a href='{$this->img_url}{$val}' target=_blank><img src='{$this->img_url}{$val}' border='0'></a>" : "<span style='color:red;'>нет файлов</span>";
  return $ret;
}

// pre post actions
function before_update() {
  global $sv, $db;
  
  
  $file = $this->d['image'];  
  if (isset($this->n['remove_file']) && $this->n['remove_file']=='on' && $file!='') {
    $r = $this->remove_files($file);    
    $db->q("UPDATE {$this->t} SET image='' WHERE id='{$this->current_record}'", __FILE__, __LINE__);
    $this->d['image'] = '';    
    $this->v_errm = array_merge($this->v_errm, $r['errm']);
  }  
}

function after_create($p, $err) {
  global $sv, $std, $db;
  
  if ($err) return false;  
  //if ($d['packs']!=$p['packs']) return false;  
  $r = $this->sync_item_in_packs($this->current_record, $p['packs']);
  
  
  return $r;
}
function after_update($d, $p, $err) {
  global $sv, $std, $db;
  
  if ($err) return false;
  
  //if ($d['packs']!=$p['packs']) return false;
  
  $r = $this->sync_item_in_packs($d['id'], $p['packs']);
  
  
  return $r;
}

function parse($d) {
  
  $d['img_url'] = ($d['image']!='') ? $this->img_url.$d['image'] : "";
  $d['img_src'] = ($d['img_url']!='') ? "<img src='{$d['img_url']}' border='0'>" : "";
  
  $d['f_rating'] = number_format($d['rating'], 1);
  return $d;
}

//stuff
function sync_item_in_packs($item_id, $pack_str) {
  global $sv, $std, $db;
  

  $err = 0;
  $errm = array();
  
  $item_id = intval($item_id);   
  $sv->load_model('tvpack');
  
  // existed in packs
  $e_packs = explode(",", $pack_str);
  foreach($e_packs as $k=>$v) {
    $e_packs[$k] = intval(trim($v));
    if ($e_packs[$k]<=0) unset($e_packs[$k]);
  }
  
  $ar = $sv->m['tvpack']->item_list("", "", 0, 1);
  foreach($ar['list'] as $pack) {
    $r = (in_array($pack['id'], $e_packs)) ? $this->add_to_pack($item_id, $pack) : $this->remove_from_pack($item_id, $pack);
    $errm = array_merge($errm, $r['errm']);
  }

  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  
  return $ret;
}

function add_to_pack($item_id, $pack) {
  global $sv, $std, $db;
  $errm = array();

  //read
  $items_ar = $pack['items_ar'];    
  
  //actions
  if (!in_array($item_id, $items_ar)) {
    $items_ar[] = $item_id;
  }    
  
  //save
  $items = implode(",", $items_ar);
  $sv->m['tvpack']->update_row(array('items'=>$items), $pack['id']);
  $errm[] = "Канал синхронизирован с пакетом <b>{$pack['title']}</b> = {$items}.";

  $ret['errm'] = $errm;  
  return $ret;  
}

function remove_from_pack($item_id, $pack) {
  global $sv, $std, $db;

  $errm = array();
  
  //read
  $items_ar = $pack['items_ar'];    
  
  //actions
  $need_act = 0;
  
  $new_ar = array();
  foreach($items_ar as $id) {
    if ($id!=$item_id) {
      $new_ar[] = $id;
    }
    else {
      $need_act = 1;
    }
  }
  
  if ($need_act) {
    //save
    $items = implode(",", $new_ar);
    $sv->m['tvpack']->update_row(array('items'=>$items), $pack['id']);
    $errm[] = "Канал удален из пакета <b>{$pack['title']}</b>, остались в пакете = {$items}.";
  }

  
  $ret['errm'] = $errm;  
  return $ret;  
}


// PUBLIC addon
function dblock() {
  global $sv, $std, $db;
  
  $sv->vars['js'][] = "jquery-1.2.3.pack.js";
  $sv->vars['js'][] = "jquery.MetaData.js";
  $sv->vars['js'][] = "jquery.rating.js";
  $sv->vars['styles'][] = "jquery.rating.css";
  
  
  $orders = array(
    'place' => 'place ASC, title ASC', 
    'title' => 'title ASC', 
    'votes' => 'votes DESC, rating DESC, title ASC', 
    'rating' => 'rating DESC, votes DESC, title ASC'
    
    );
  $order_keys = array_keys($orders);  
  $order_key = (isset($sv->_get['order']) && in_array($sv->_get['order'], $order_keys)) ? $sv->_get['order'] : "title";
  $order = $orders[$order_key];
  $ar = $this->item_list("", "{$order}", 0, 1);
  
  $tr = array();
  
  foreach($ar['list'] as $d) {
  $name = "tvlist-{$d['id']}-rating";
  
  $s = array(
  1 => '',
  2 => '',
  3 => '',
  4 => '', 
  5 => ''
  );
  
  $rating = round($d['rating']);
  $s[$rating] = " checked=\"checked\"";
  
$tr[] = <<<EOD

<form action='http://www.norcom.ru/cat/tv/channels.html' method='post' enctype='multipart/form-data'>   
    <tr valign=top>
      <td width='1%' style='padding:5px;'>{$d['img_src']}</td>
      <td style='padding-bottom: 10px;'>
        <b>{$d['title']}</b>
        <div>{$d['text']}</div>    
      </td>
      <td valign=middle style='padding-left: 10px;' nowrap width='160'>

    <input class="star" type="radio" name="{$name}" value="1"{$s[1]}/>
    <input class="star" type="radio" name="{$name}" value="2"{$s[2]}/>
    <input class="star" type="radio" name="{$name}" value="3"{$s[3]}/>
    <input class="star" type="radio" name="{$name}" value="4"{$s[4]}/>
    <input class="star" type="radio" name="{$name}" value="5"{$s[5]}/>


<div class='rating-value' id='{$name}_tip'>
Рейтинг: {$d['f_rating']} / Голосов: {$d['votes']}.
</div>
  
      </td>
    </tr>
</form>     
          
EOD;

    
  }
  
  $trs = implode("\n", $tr);
  
  //links ==================
  $links_ar = array(
  'title' => "названию",
  'votes' => "количеству голосов",
  'rating' => "рейтингу"  
  );
  $tar = array();
  foreach($links_ar as $k=>$v) {
    $s = ($order_key==$k) ? "<b>" : "";
    $tar[] = "{$s}<a href='{$this->url}&order={$k}'>{$v}</a></b>";
  }
  $links = implode(" | ", $tar);
  
  
  $ret = <<<EOD
<style>
div.rating-value {
  width:150px; clear: both;
  font-size: 90%; 
  color: gray;
  padding-top: 5px;
}
</style>
<script language='JavaScript'>
$(function(){ 
  var c_input = "";
  $('input[@type=radio].star').rating({
    callback: function(value, link){
      c_input = this.name;
      $('#'+c_input+'_tip').html("Загрузка...");
      $.post("/post.php",
        { act: "tvlist", id: this.name, value: value },
        function(data){
          $('#'+c_input+'_tip').html(data);
        }
      );

    }
  }); 
});
</script>
        

<table width='100%' border=0 cellspacing=8>
<tr><td colspan=2 style='padding-bottom: 20px;'>Сортировать по: &nbsp; {$links}.</td>
  <td style='color:green;padding-left: 10px; font-size: 120%;'>Голосовать&nbsp;&darr;</b></td>  
</tr>
{$trs}</table>
  
  
EOD;

  
  return $ret;
}

function ajax_vote($name, $val) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  
  $val = intval($val);
  if ($val<1 || $val>5) {
    $err = 1;
    $errm[] = "Неверное значение <b>{$val}</b>.";
  }
  
  
  if (preg_match("#^tvlist\-([0-9]+)\-rating$#si", $name, $m)) {
    $tvlist_id = $m[1];
    $name = addslashes($name);
  }
  else {    
    $err = 1;
    $errm[] = "Неверное название переменной.";
  }
  
  if (!$err) {
    $sv->load_model('action');  
    $exp = $sv->post_time - 60*20;
    $d = $sv->m['action']->get_item_wh("`code`='".addslashes($name)."' AND `time`>'{$exp}' AND ip='{$sv->ip}' AND agent='".addslashes($sv->user_agent)."'", 0);
    if ($d!==false) {
      $err = 1;
      $errm[] = "Отклонено, слишком много голосов c вашего IP, попробуйте позже.";
    }
  }

  if (!$err) {
    
    // insert to action
    $p = array();
    $p['code'] = $name;
    $p['time'] = $sv->post_time;
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['value'] = $val;
    $p['refer'] = $sv->refer;
   
    $sv->m['action']->insert_row($p);
    
    
    //update tvlist stats
    $r = $this->update_rating($tvlist_id, $val);    
    $err = ($r['err']) ? 1 : $err;
    $errm = array_merge($errm, $r['errm']);
    $new_rating = number_format($r['new_rating'], 1);
    $new_votes = $r['new_votes'];
  }
  
  
  if (!$err) {   
    $ret = "Рейтинг: {$new_rating} / Голосов: {$new_votes}.";
  }
  else {
    $ret = "<span style='color:red;font-size: 90%;'>".implode("<br>", $errm)."</span>";
  }
  
  return $ret;
}

function update_rating($id, $val) {
  global $sv, $std, $db;
  
  $err = 0;
  $errm = array();
  $ret = array();
  
  $d = $this->get_item($id);
  if ($d===false) {
    $err = 1;
    $errm[] = "Канал {$id} не найден.";
  }
  
  if (!$err) {
    //count     
    $sum = $d['votes']*$d['rating']+$val;
    $new_votes = $d['votes']+1;
    $new_rating = $sum/$new_votes;
    
    $ret['new_votes'] = $new_votes;
    $ret['new_rating'] = $new_rating;
    
    $p = array(
      'votes' => $new_votes,
      'rating' => $new_rating     
    );
    $this->update_row($p, $id);
  }
  
  $ret['err'] = $err;
  $ret['errm'] = $errm;
  return $ret;
}
// eoc
}

?>