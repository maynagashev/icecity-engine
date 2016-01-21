<?php

/**
  Универсальнные аттачи, with optional thumb
  Используются в редакторе страниц, новостях и т.д.
 */


/*
Example

  //attaches 
  $sv->load_model('attach');
  $sv->m['attach']->action_url = $d['edit_url'];
  $ret['attach'] = $sv->m['attach']->init_object('post', $d['id'], $this->user['id']);

  //main module submit rewriting  
  if ($sv->m['attach']->code!='iframe')  {
    $this->submit_call = "submit_post";
    $s = $this->init_submit();
  }
  
  
*/

class m_attach extends class_model {
  
var $tables = array(
  'attaches' => "
    `id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    `filename` VARCHAR( 255 ) NULL ,
    `page` VARCHAR( 255 ) NULL,
    `object` int(11) not null default '0',
    `uid` int(11) not null default '0',
    `hash` varchar(255) null,
    `dir` varchar(255) null,
    `savename` varchar(255) null,
    `ext` varchar(255) null,
    `mime` varchar(255) null,
    `size` int(11) not null default '0',
    `is_image` tinyint(1) not null default '0',
    `w` int(11) not null default '0',
    `h` int(11) not null default '0',
    
    `thumb` varchar(255) null,
    
    `ip` VARCHAR( 255 ) NULL ,
    `agent` varchar(255) null,
    `refer` varchar(255) null,
    
    `created_at` DATETIME NOT NULL ,
    `created_by` INT( 11 ) NOT NULL ,
    `updated_at` DATETIME NOT NULL ,
    `updated_by` INT( 11 ) NOT NULL ,
    
    KEY ( `page` , `object` ),
    KEY ( `is_image` ),
    KEY ( `uid` )  
  "
);

var $page = "";
var $object = 0;  
var $uid = 0;
var $ident = "";

var $dir = "uploads/attaches/";
var $url = "uploads/attaches/";

var $icons_dir = "i/icons/";
var $icons_url = "i/icons/";

var $ext_ar = array(
  'gif', 'png', 'jpg', 'jpeg', 
  'rar', 'zip', 'gz',
  'pdf', 'doc', 'xls', 'ppt',
  'mp3', 'avi', 'flv'
  );
var $img_ext_ar = array(
  'gif', 'png', 'jpg', 'jpeg'
);

//default thumb size
var $d_w = 220;
var $d_h = 165;
var $max_w = 2000;
var $max_h = 2000;

var $per_page = 20;  
var $article = null; //article info 
var $fixed_keys = array();

var $action_url = "";
var $iframe_url = "";
var $insert_format = "html";

function __construct() {
  global $sv;
  
  $this->t = $sv->t['attaches'];  
  $this->dir = PUBLIC_DIR.$this->dir;
  $this->url = PUBLIC_URL.$this->url;
    
  $this->icons_dir = PUBLIC_DIR.$this->icons_dir;
  $this->icons_url = PUBLIC_URL.$this->icons_url;
    
  $this->init_field(array(
  'name' => 'filename',
  'title' => 'Filename',
  'type' => 'varchar',  
  'len' => '50',
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));   
  $this->init_field(array(
  'name' => 'page',
  'title' => 'Раздел',
  'type' => 'varchar',
  'len' => '11',
  'show_in' => array('default'),
  'write_in' => array('edit')  
  ));    
  $this->init_field(array(
  'name' => 'object',
  'title' => 'Родительский объект',
  'type' => 'int',  
  'len' => '5',
  'default' => '',
  'show_in' => array('default'),
  'write_in' => array('edit')
  ));   
  $this->init_field(array(
  'name' => 'hash',
  'title' => 'Хэш',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('edit', 'remove')
  ));   
  $this->init_field(array(
  'name' => 'dir',
  'title' => 'Папка',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('edit', 'remove')
  ));     
  $this->init_field(array(
  'name' => 'savename',
  'title' => 'Файл',
  'type' => 'varchar',
  'len' => '50',
  'input' => 'file',
  'show_in' => array('edit', 'remove'),
  'write_in' => array('create')
  ));               
  $this->init_field(array(
  'name' => 'ext',
  'title' => 'Расширение',
  'type' => 'varchar',
  'len' => '10',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));    
  $this->init_field(array(
  'name' => 'mime',
  'title' => 'MIME тип файла',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));            
  $this->init_field(array(
  'name' => 'size',
  'title' => 'Размер файла в байтах',
  'type' => 'int',
  'len' => '12',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));
      
  $this->init_field(array(
  'name' => 'is_image',
  'title' => 'Изображение?',
  'type' => 'boolean',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
  $this->init_field(array(
  'name' => 'w',
  'title' => 'Ширина',
  'type' => 'int',
  'len' => '12',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));      
  $this->init_field(array(
  'name' => 'h',
  'title' => 'Высота',
  'type' => 'int',
  'len' => '12',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));

  $this->init_field(array(
  'name' => 'thumb',
  'title' => 'Thumb',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('remove'),
  'write_in' => array('edit')
  ));  
}

//controllers 
function c_iframe() {
   
}

/**
 * Upload
 *
 * @param unknown_type $err
 * @param unknown_type $errm
 * @param unknown_type $n
 * @return unknown
 */
function sc_iframe($n) {
  global $sv, $std;

  $err = 0;
  $name = $this->ident;  
  $p = array();
  $p['hash'] = $std->text->random_t();
  $p['dir'] = $std->text->split_dir($p['hash']);
  
  $dir = $this->dir.$p['dir']."/";
  $edir = $this->dir.$p['dir'];
  
  if (!$err) {    
    $file = $std->file->check_upload($name, $this->ext_ar, $dir);      
    $err = ($file['err']) ? true : $err;
    $this->errm($file['errm'], $err);
  }  
  
  if (!$err) {    
    $p['page'] = $this->page;
    $p['object'] = $this->object;
    $p['uid'] = $this->uid;
    $p['savename'] = $file['savename'];
    $p['filename'] = $std->text->cut($file['filename'], 'cut', 'cut');
    $p['ext'] = $file['ext'];
    $p['mime'] = $file['mime'];
    $p['is_image'] = (in_array($p['ext'], $this->img_ext_ar)) ? 1 : 0;
  }
  

  if (!$err) {    
    if (!file_exists($edir) || !is_dir($edir)) {
      mkdir($edir, 0775, 1);
      chmod($edir, 0775);
      if (!file_exists($edir) || !is_dir($edir)) {
        $err = 1;
        $this->errm("Не удалось создать директорию для размещения файла: <b>{$edir}/</b>", $err);
      }
    }
  }  
  if (!$err && !is_writeable($edir)) {
    $err = 1;
    $this->errm("Папка для сохранения не доступна для записи: <b>{$edit}</b>", $err);
  }
  
  
  if (!$err) {  
    if (move_uploaded_file($file['tmp_name'], $file['savepath']))	{	
    
      $p['size'] = filesize($file['savepath']);
      if ($p['is_image']) {      
        $img = getimagesize($file['savepath']);      
        $p['w'] = $img[0];      
        $p['h'] = $img[1];   
      }
      
      $this->errm("Файл успешно загружен.", $err);  
    }
    else {
      //$this->v_err = true;
      $this->errm("Не удалось переместить файл из временной папки: {$file['tmp_name']} &rarr; {$file['savepath']}", $err);
      return false;
    }
  }
  else {
    $this->errm("Ошибка работы с файлами.", $err);
  }
  
  
  
  if (!$err && $p['is_image']) {  
    if (isset($n['make_thumb']) && $n['make_thumb']= 'on') {
      $r = $this->make_thumb($p, $n);        
      if (!$r['err']) {
        $p['thumb'] = $r['savename'];
      }
    }
  }
  
  if (!$err) {
    $p['ip'] = $sv->ip;
    $p['agent'] = $sv->user_agent;
    $p['refer'] = $sv->refer;
    
    $af = $this->insert_row($p);
    if (!$af) {
      $err = 1;
      $this->errm("Ошибка работы с базой данных, не удалось добавть новую запись, сообщите администратору.", $err);
      $r = $this->remove_files($p);
    }
    else {
      $this->errm("Файл <b>{$p['filename']}</b> успешно загружен, не забудьте вставить код для показа файла в сообщение.", $err);
    }
    
  }
  
  
  
  $ret = array(
  'err' => $err,
  'v' => array());
  return $ret;
}


//parsers
function parse($d) {
  global $std;
  
  $d['dir_url'] = $this->url.$d['dir']."/";
  $d['dir_path'] = $this->dir.$d['dir']."/";
  
  $d['open_url'] = $d['dir_url'].$d['savename'];
  $d['thumb_url'] = ($d['thumb']!='') ? $d['dir_url'].$d['thumb'] : "";
  
  $d['icon_path'] = $this->icons_dir.$d['ext'].".gif";
  $d['icon_url'] = $this->icons_url.$d['ext'].".gif";
  $d_icon = $this->icons_url."attach.gif";
  $d['icon'] = (file_exists($d['icon_path'])) ? $d['icon_url'] : $d_icon;
  $d['icon_img'] = "<img src='{$d['icon']}' width=16 height=16 border=0>";
  
  $d['f_size'] = $std->act_size($d['size']);
  
  
  if ($d['thumb']!='') {
    $d['thumb_path'] = $d['dir_path'].$d['thumb'];
    $d['thumb_size'] = filesize($d['thumb_path']);
    $d['thumb_f_size'] = $std->act_size($d['thumb_size']);
    
    $img = getimagesize($d['thumb_path']);      
    $d['thumb_w'] = $img[0];      
    $d['thumb_h'] = $img[1];   
  }
  return $d;
}

//sub actions
function get_parsed_list() {
  global $sv;
  
  $ret = $this->item_list("`page`='{$this->page}' AND `object`='{$this->object}'", "`filename` ASC", 0, 1);   
  
  return $ret;
}

function unlink_file($id) {
  global $sv, $std, $db;
  
  $err = 0;
  
  $id = intval($id);
  $d = $this->get_item($id, 1);
  if ($d===false) {
    $err = 1;
    $this->errm("Файл не найден, возможно он уже удален.", $err);
  }
  
  /*
  if (!$err) {
    // user access
    if ($d['uid']!=$this->uid) {
      $err = 1;
      $errm[] = "Удаление чужих файлов запрещено (f{$d['uid']}!=c{$this->uid}).";
    }
  }
  */
  if (!$err) {
    // edit area access
    if ( ($d['page']!=$this->page) || ($d['object']!=$this->object)) {
      $err = 1;
      $this->errm("Ошибка зоны доступа, не совпадают переменные инициализации, сообщите администратору.", $err);
    }
  }
  
  //removing
  if (!$err) {
    $r = $this->remove_files($d);
    $err = ($r['err']) ? 1 : $err;
  }
  if (!$err) {
    $af = $this->remove_row($d['id']);
    $this->errm("Запись <b>{$d['filename']}</b> успешно удалена из базы [{$sv->date_time}]", $err);
  }
  
  return array('err' => $err);
}

function remove_files($d) {
  
  $err = 0;
  
  $dir = $this->dir.$d['dir']."/";
  
  $files = array();
  $files[] = $dir.$d['savename'];
  if ($d['thumb']!='') {
    $files[] = $dir.$d['thumb'];
  }            

  foreach($files as $fn) {
    if (!file_exists($fn)) continue;
    unlink($fn);
    if (file_exists($fn)) {
      $err = 1;
      $this->errm("Не удалось физически удалить файл <b>{$fn}</b>", $err);
    }
    else {
      $this->errm("Файл <b>".basename($fn)."</b> удален.", $err);
    }
  }
      
  return array('err' => $err);
}

function make_thumb($d, $n) {
  global $sv, $std, $db;
  $err = 0;
  
  $t_w = (isset($n['w'])) ? intval($n['w']) : $this->d_w;
  $t_w = ($t_w<1 || $t_w>$this->max_w) ? $this->d_w : $t_w;  
  $t_h = (isset($n['h'])) ? intval($n['h']) : $this->d_h;
  $t_h = ($t_w<1 || $t_h>$this->max_h) ? $this->d_h : $t_h;
  
  $dir = $this->dir.$d['dir']."/";
  
  $src = $dir.$d['savename'];
  $savename = "thumb_".$d['savename'];
  $target = $dir.$savename;
  
  if (!$err) {
    $std->resize->verbose = 0;
    if ($std->resize->auto_fixed($src, $target, $t_w, $t_h))  {
      $this->errm("Создана копия изображения размером: <b>{$t_w}x{$t_h}</b> px.", $err);    
    }    
    else {
      $err = 1;
      $this->errm(implode($std->resize->last_session), $err);
      $savename = "";
    }
  }
  
  
  return array('err' => $err, 'savename' => $savename);
}

// stuff
/**
 * Основная и пока единственная функция для подключения аттачей 
 * (формирует форму либо список, в заисимости от $sv->_get['attach_iframe'])
 * перед вызовом должен быть задан this->action_url по которому открывается фрейм, 
 * лучше всего сделать отдельный контроллер родительского объекта типа c_attaches 
 * и из него вызывать эту функцию
 *
 * @param unknown_type $page
 * @param unknown_type $object
 * @return unknown
 */
function init_object($page, $object, $uid = 0) {
  global $sv, $std, $db;
  
  $this->page = addslashes(preg_replace("#[^a-z0-9\_\-]#si", "", $page));
  $this->object = intval($object);
  $this->uid = intval($uid);
  
  $this->ident = "attach_".$this->page."_".$this->object;
  $this->iframe_url = $this->action_url."&attach_iframe=1";
  
  if (isset($sv->_get['attach_iframe']) && $sv->_get['attach_iframe']==1) {
    
    // submit check
    $this->code = 'iframe';
    $this->init_controllers();
    $s = $this->init_submit();    
   
    $ret = $this->show_iframe_src($s);
  }
  else {
    //standart
    $ret = $this->get_parsed_list();
    
    $ret['form'] = $this->upload_form();
    $ret['iframe'] = $this->iframe_code();
  }
  
  return $ret;
}

function upload_form() {
  global $sv, $std, $db;
  
  $err =  ($this->iframe_url=='') ? $std->err_box(1, array('attach-><b>iframe_url</b> not specified')) : "";
  
  $img_ext = implode(", ", $this->img_ext_ar);
  $all_ext = implode(", ", $this->ext_ar);
  
  $ret = "

{$err}
<style>
.attach-thumb-box td { padding: 2px 10px; font-size: 80%;}
</style>
<script language='JavaScript'>
function reset_file_field() {
  $('input[@type=file]').val('');
}
</script>

  <form action='{$this->iframe_url}' method='POST'  enctype='multipart/form-data'
        target='{$this->ident}' style='margin:0; padding:0;'>  
  <input type=hidden name='MAX_FILE_SIZE' value='999999000'>
  <input type='hidden' name='new[todo]' value='upload'>
  <table class='attach-form' cellpadding=3 cellspacing=0 border=0>
    <tr>
      <td><input name='{$this->ident}' type='file' size=20></td>      
    </tr>
    <tr>
      <td>Разрешены: <b>{$all_ext}</b>.</td>
    </tr>
    <tr>
      <td>
        <table>
          <tr><td><input type='checkbox' name='new[make_thumb]' 
           onclick=\" if (this.checked) $('.attach-thumb-box input').attr('disabled', false); else $('.attach-thumb-box input').attr('disabled', true);\"></td>
              <td>создать уменьшенную копию</td></tr>
        </table>
       
        <table class='attach-thumb-box' bgcolor='#f6f6f6'>        
          <tr><td colspan='2'>доступно только для изображений: <b>{$img_ext}</b></td></tr>
          <tr><td>Ширина&nbsp;(px)</td><td><input type='text' name='new[w]' value='{$this->d_w}' size='5' disabled style='text-align:center;'></tr>
          <tr><td>Высота&nbsp;(px)</td><td><input type='text' name='new[h]' value='{$this->d_h}' size='5' disabled style='text-align:center;'></tr>
        </table>
       
      </td>
    </tr>
    <tr>
      <td align='center'><input type='submit' value='Загрузить файл' onClick=\"this.form.submit(); setTimeout('reset_file_field()', 3000);\"></td>
    </tr>
  </table>
  </form>
  
  
  ";
  
  return $ret;
}

function iframe_code() {
  global $sv, $std, $db;
   
  $ret = "
<iframe name='{$this->ident}' src='{$this->iframe_url}' height='250' style='width:100%;' frameborder=0></iframe>";
  
  return $ret;
}

function show_iframe_src($s) {
  global $sv, $std, $db;
  
  $err = $s['err'];


  
  
  if (isset($sv->_get['unlink'])) {
    $r = $this->unlink_file($sv->_get['unlink']);
    $err = ($r['err']) ? 1 : $err;
  }
  
  // hack
  $errm = array();   
  foreach($sv->msgs as $m) {
    $errm[] = $m['text'];
  }  
  $err_box = $std->err_box($err, $errm);
  $err_box = ($err_box!='') ? "<div style='margin: 5px 0;'>{$err_box}" : "";
  
  

  
  $files = $this->get_parsed_list();
  $tr = array();
  foreach($files['list'] as $d) {
    
    $codes = array();  
    //bbcode
    if ($this->insert_format=='bbcode') {
      $codes[] = "<a href=\"javascript: parent.markitup_insert('[url={$d['open_url']}]', '[/url]', '{$d['filename']}');\">ссылка</a>";  
      if ($d['is_image']) {
        $codes[] = "<a href=\"javascript: parent.markitup_insert('[img]', '[/img]', '{$d['open_url']}');\">картинка</a>";
      }
    }
    // html
    else {
      $codes[] = "<a href=\"javascript: parent.markitup_insert('<a href=\\'{$d['open_url']}\\'>', '</a>', '{$d['filename']}');\">ссылка</a>";  
      if ($d['is_image']) {
        $codes[] = "<a href=\"javascript: parent.markitup_insert('<img src=\\'', '\\' border=0>', '{$d['open_url']}');\">картинка</a>";
      }      
    }
    
    if ($d['ext']=='flv') {
      $codes[] = "<a href=\"javascript: parent.markitup_insert('[video]', '[/video]', '{$d['open_url']}');\">видео</a>";
    }

        
    $codes_row = implode("&nbsp;&mdash;&nbsp;", $codes);
  
    
    $tr[] = "
      <tr bgcolor=white>
        <td width='1%'>{$d['icon_img']}</td>
        <td><a href='{$d['open_url']}' target=_blank>{$d['filename']}</a></td>
        
        <td align=left>
        {$codes_row}        
        </td>
        <td align=center>{$d['f_size']}</td>
        
        <td align=center><a href='{$this->iframe_url}&unlink={$d['id']}' style='color:red;' 
        onclick=\"if (!confirm('Вы действительно хотите удалить файл?')) return false;\">удалить</a></td>
      </tr>      
    ";
    
    // дополнительная строка с thumb 
    if ($d['thumb']!='') {
        
      $codes = array();  
      if ($this->insert_format=='bbcode') {
        $codes[] = "<a href=\"javascript: parent.markitup_insert('[url={$d['thumb_url']}]', '[/url]', '{$d['thumb']}');\">ссылка</a>";  
        $codes[] = "<a href=\"javascript: parent.markitup_insert('[img]', '[/img]', '{$d['thumb_url']}');\">картинка</a>";
        $codes[] = "<a href=\"javascript: parent.markitup_insert('[url={$d['open_url']}][img]', '[/img][/url]', '{$d['thumb_url']}');\">превью</a>";
      }
      else {
        $codes[] = "<a href=\"javascript: parent.markitup_insert('<A href=\\'{$d['thumb_url']}]\\'>', '</a>', '{$d['thumb']}');\">ссылка</a>";  
        $codes[] = "<a href=\"javascript: parent.markitup_insert('<img src=\\'', '\\' border=0>', '{$d['thumb_url']}');\">картинка</a>";
        $codes[] = "<a href=\"javascript: parent.markitup_insert('<a href=\\'{$d['open_url']}\\' target=_blank><img src=\\'', '\\' border=0></a>', '{$d['thumb_url']}');\">превью</a>";        
      }
      $codes_row = implode("&nbsp;&mdash;&nbsp;", $codes);      
      $tr[] = "
      
      <tr bgcolor=white>
        <td width='1%'>&uarr;</td>
        <td><a href='{$d['thumb_url']}' target=_blank>{$d['thumb']}</a></td>        
        <td align=left>
        {$codes_row}        
        </td>
        <td align=center>{$d['thumb_f_size']}</td>        
        <td align=center>{$d['thumb_w']}x{$d['thumb_h']}</td>
      </tr>   
            
      ";
    }
  }
  
  if (count($tr)<=0) {
    $tr[] = "<tr bgcolor=white><td colspan='5' align=center style='color: gray;padding: 10px;'>Список файлов пуст, используйте форму справа для того чтобы загрузить файлы.</i></td></tr>";
  }
  
  
  $html = "<table width='100%' cellpadding='3' cellspacing=1 bgcolor='#efefef' style='margin: 5px 0;'>
  <tr bgcolor='#f6f6f6'>
    <td>&nbsp;</td>
    <tD><small>Название файла</td>
    <td align=center><small>Вставка кода в текст [&nbsp;<A href='#' onclick=\"document.getElementById('hint').style.display='block';return false;\">?</a>&nbsp;]
       <div id='hint' style='display:none;'>(установите курсор в том месте текста, в которое нужно вставить код, затем щелкните по соответствующей ссылке)</div></td>
    <td align=center><small>Размер</td>
    <td align=center><small>Опции</td>
  </tr>
          ".implode("\n", $tr)."</table>";
$ret = "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><title>{$this->ident}</title></head>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'>
<link rel='stylesheet' type='text/css' href='/css/admin_style.css'>
<body topmargin='0' style='background:none;' leftmargin='0' marginwidth='0' marginheight='0'>
{$err_box}
{$html}
</body>
</html>
";
  echo $ret; exit();
}

/**
 * Обработка текста со вставленными файлами
 *
 * @param unknown_type $t
 * @return unknown
 */
function process_text($t, $filter_id = 'default') {
  
   // video
  $t = preg_replace_callback("#\[video\]([^\[]+)\[/video\]#si", array($this, "replace_video_code"), $t);

  return $t;
}

/**
 * вспомогательная функция для process_text
 *
 * @param unknown_type $m
 * @return unknown
 */
function replace_video_code($m) {
  global $sv;
  
  $url = $m[1];
  
$ret = <<<EOD

<div id='player'><embed src="/i/stuff/mediaplayer.swf" 
width="455"
height="370"
allowscriptaccess="never"
allowfullscreen="true"
flashvars="width=455&height=370&file={$url}&bufferlength=3&autoscroll=true&screencolor=0xefefef" 
/></div>


EOD;

  return $ret;
}

//eoc
}

?>