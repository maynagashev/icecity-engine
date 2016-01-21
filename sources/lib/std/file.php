<?php 

class std_file {
  var $msgs = array();
  var $replacing = 0;
  
  var $icons_dir = "i/icons/";
  var $icons_url = "i/icons/";
  
var $error_codes = array(

  0=>"Файл загружен без ошибок.", 
  1=>"Првышен лимит upload_max_filesize в настройках сервера php.ini.", 
  2=>"Превышен лимит MAX_FILE_SIZE указанный в форме.",
  3=>"Файл был загржен лишь частично.",
  4=>"Файл не был указан.",
  6=>"Временная папка для загрузки не доступна." 
        
);

function __construct() {
      
  $this->icons_dir = PUBLIC_DIR.$this->icons_dir;
  $this->icons_url = PUBLIC_URL.$this->icons_url;
  
}

function std_file() 
{
	  GLOBAL $sv, $db;
     
    $this->max_threads = 8; // max
    $this->time		= $sv->post_time;
    $this->ip		= $sv->ip;
    $this->files	= $_FILES;    
    $this->gd = 2;
    $this->upload_limit = 0; //no limit
    
    $this->gurl = "http://files.blog.wlan/";
    $this->gdir = (defined("FILES_DIR")) ? FILES_DIR : "";
    
    
    $this->dir = $this->gdir;
    $this->const_dir = $this->gdir;
    
    $this->page_types = array(
    'post',
    'comments'
    
    );
    
	}  
	
	
function init() {
  global $sv;
      
    $this->types = $sv->cfg['file_types'];
    $this->t = $sv->t['images'];
    
    /* 
    ERRORS CODES
    2  - form filesize limit reached
    4  - not set (empty field)
    
    */
 
  
}



function upload($name, $type = 'default')
{
  
  global $db, $sv, $std; $ret = array(); $err = false; $errm = array();
  
 
  // if not submited that name
  if (!isset($this->files[$name])) { return false; }
  
  // if "empty field"
  if ($this->files[$name]['error']==4) { return false; }  

	//extensions array
	
	
	$ext_ar = (isset($this->types[$type]) && is_array($this->types[$type])) ? $this->types[$type] : array();
  
	
	$ret['file'] = $file = $this->files[$name];	
  
	// size limiting
	if ($this->upload_limit>0 && $file['size']>$this->upload_limit) {
	  $errm[] = "Вы не можете закачать файл размером ".$std->size_mb($file['size'])." МБ 
	  потому что свободного места всего ".$std->size_mb($this->upload_limit)." МБ. ";
	  return false;
	}

	
	
	$ret['uploaded'] = 0;  
	$ret['filename'] = $filename = $file['name'];
	$ret['ext'] = $ext = $this->extension($filename);
	$ret['type'] = $this->get_type($ext);
	
 	$save_path = $this->dir.$this->rand_name($ext, $filename);
 	$lim = 10000; $i=0;
	while (file_exists($save_path))		{ $i++;
	  if ($i>$lim) die("filename loop limit 10000 exeeded ");
   	$save_path = $this->dir.$this->rand_name($ext, $filename, 1);
  }
  $ret['savename'] = basename($save_path);
	$ret['save_path'] = $save_path;
	$ret['filesize'] = $file['size'];
  $ret['mime'] = $file['type'];
      
		
	if (!in_array($ext, $ext_ar)){	
    $this->msgs[] = "Недоспустимое расширение \"<b>$filename</b>\" {$ext} (разрешены только: ".implode(", ",$ext_ar).")<br>"; 	
		$err = true;	
  }
  
  if ($ext=='php') {
    $this->msgs[] = "Загрузка php скриптов запрещена из соображений безопасности."; 	
		$err = true;	
  }

  if (!$err) {
		if (move_uploaded_file($file['tmp_name'], $save_path))	{	
      chmod($save_path, 0644);
      $ret['chmod'] = "0644";			  
			$ret['uploaded'] = 1;
			if ($ret['type']==1) { //image
			  $x = getimagesize($save_path);
			  $ret['w'] = $x[0];
			  $ret['h'] = $x[1];
			  //resizong
			   
			  $this->make_resize($save_path, 150, 0, 1);
			  //print_r($this->msgs);
			}
			else {
			  $ret['w'] = $ret['h'] = 0;
			}
      
		}
		else 	{
			$this->msgs[] = "Файл небыл перемещен из папки темп. ".$file['tmp_name']."=>".$save_path." {$php_errormsg}";
			$err = true;
		}
  }

  $ret['msgs'] = $this->msgs;
 
	return $ret;
}

function make_resize($fn, $w=0, $h=0, $replace=0) {
  global $std;
  $w = intval($w);
  $h = intval($h);
  
  //source exist
  if (!file_exists($fn)) {
    $this->msgs[] = "{$fn} не найден";
    return false;    
  }
  
  // need resize
  if ($w<=0 && $h<=0) {
    return $fn;
  }
  
  //not exist ret or forced
  $res_fn = $this->resize_name($fn);
  if ($replace==0 && file_exists($res_fn)) {
    return $res_fn;
  }  
  
  //writeable
  $dir = dirname($fn);
  if (!is_writeable($dir)) {
    $this->msgs[] = "ошибка, \"{$dir}\" - только для чтения";
    return false;
  }
  
  
  
  // resizeing 
  		  
    $t = array();
    $not_supported = false;
    $ext = strtolower($std->file_extension($fn));
  
  
	  switch ($ext) 
		{
			case 'jpg': case 'jpeg':
        if (imagetypes() & IMG_JPG) {
       
          $im_src = ImageCreateFromJPEG($fn); 
        
       
        } else {  $not_supported = true; } 
        
        
        $t['format'] = "JPG";		
      break;
			case 'png': 
        if (imagetypes() & IMG_PNG) {$im_src = ImageCreateFromPNG($fn); } else {$not_supported = true;} 
        $t['format'] = "PNG";
      break;
			case 'gif': 
        if (imagetypes() & IMG_GIF) {$im_src = ImageCreateFromGIF($fn); } else {$not_supported = true;}
        $t['format'] = "GIF"; 
      break;
			default: $not_supported = true;
		}		
		if ($im_src===false) $not_supported=true;
    
    if ($not_supported) {
      $this->msgs[] = "$fn - resize filed not supported file type<br>";
      return false; 
    }

    $img["w"] = imagesx($im_src); 
    $img["h"] = imagesy($im_src); 
    
    $img["w_thumb"] = $w;
    $img["h_thumb"] = ($img["w_thumb"]/$img["w"])*$img["h"]; 
    
    /*  
    if ($img["w"] >= $this->img["h"])    { 
      $img["w_thumb"] = $w;
      $img["h_thumb"] = ($img["w_thumb"]/$img["w"])*$img["h"]; 
    } 
    else     { 
      $img["h_thumb"] = $h;
      $img["w_thumb"] = ($img["h_thumb"]/$img["h"])*$img["w"]; 
    }     
      */
      
	
    if( $this->gd >= 2 ) { 
            
      $im_out = imagecreatetruecolor($img["w_thumb"], $img["h_thumb"]); 
      
      imagecopyresampled (  $im_out, 
                            $im_src, 
                            0, 0, 0, 0, 
                            $img["w_thumb"],
                            $img["h_thumb"], 
                            $img["w"], 
                            $img["h"]); 
                          
    } 
    else {           
      $im_out = imagecreate($img["w_thumb"], $img["h_thumb"]); 
      
      imagecopyresized ( 
                          $im_out, 
                          $im_src, 
                          0, 0, 0, 0, 
                          $img["w_thumb"],    
                          $img["h_thumb"], 
                          $img["w"], 
                          $img["h"]); 
     } 

     if ($t["format"]=="JPG") { 
       imageJPEG($im_out, $res_fn, 100); 
     } 
     elseif ($t["format"]=="PNG") { 
       imagePNG($im_out, $res_fn); 
     } 
     elseif ($t["format"]=="GIF") { 
       imageGIF($im_out, $res_fn); 
     } 
    
  

  
  
          
  
  
  
  return $res_fn;
}

function show_download( $data, $name, $type="unknown/unknown", $compress=1 )
{
	if ( $compress and @function_exists('gzencode') )	{
		$name .= '.gz';
	}
	else	{
		$compress = 0;
	}
	
	header('Content-Type: '.$type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Disposition: attachment; filename="' . $name . '"');
	
	if ( ! $compress ) {
		@header('Content-Length: ' . strlen($data) );
	}
	
	@header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	@header('Pragma: public');
	
	if ( $compress )	{
		print gzencode($data);
	}
	else	{
		print $data;
	}
	
	exit();
}

function resize_name($fn) {
  
  $ret = dirname($fn)."/thumb_".basename($fn);
  return $ret;
}

function rand_name($ext="", $filename="", $num = 0) {
  global $std;
  
  $err = false;
  if ($filename!='') {
    $filename = trim($filename);
    $filename = preg_replace("#\.[\w]+$#msi", "", $filename);
    $filename = $std->translit($filename);
    $filename = str_replace(" ", "_", $filename);
    $filename = str_replace(".", "_", $filename);
    $filename = preg_replace("#[^\w\_\-]#msi", "", $filename);
    if ($filename=='') {
      $err = true;
    }
  }
  
  if ($num) {
    if (!isset($this->num)) {
      $this->num = 2;
    }
    else {
      $this->num++;
    }
    $filename.="_".$this->num;
  }
  
  if ($err) {
    $filename = uniqid(rand());  
    $filename = preg_replace("|[^A-Za-z0-9\.]|msi", "", $filename);  		
  }
	
  if ($ext!='') {
    $filename .=".".$ext;
  }
  
  return $filename;
}

/**
 * Определение расширения файла путем разбивки строки на массив
 * Если точка отсутствует возвращается очищенная строка полностью 
 * @param unknown_type $filename
 * @return unknown
 */
function extension($filename) {  
	$filename = basename($filename);
	$ar = explode(".",$filename);
	if (is_array($ar)) 	{
		$i = sizeof($ar)-1;
		$ext = $ar[$i];
		$ext = strtolower(preg_replace("#[^a-z0-9]#si", "", $ext));
	} 
	else {
	  $ext = "";
	}
  return $ext;	
}


function get_type($ext)
{
  
  $images = array('jpg', 'gif', 'png');
  
  if (in_array($ext, $images)) {
    $t = 1;
  }
  else {
    $t = 0;
  }
  
return $t;	
}


// filesystem
	
function read($name) {
  $fd = fopen($name, "r" );
  $file = @fread($fd,filesize($name));
  fclose($fd);
return $file;
}

function write($name, $data) {
  @chmod($name,0664);
  $fd = fopen( $name, "w" );
  $write=fwrite($fd,$data);
  fclose( $fd );
  return $write;
}

function append($fn, $str) {
  $fh = fopen($fn, "a");
  $write = fwrite($fh, $str);
  fclose($fh);   
}

function read_dir($dir_name) {
//echo "<b>read_dir [$dir_name]<br></b>";
  if (substr($dir_name,-1)=="/" || substr($dir_name,-1)=="\\"){
    $l = strlen($dir_name); 
    $dir_name =substr($dir_name,0,$l-1);
  }
  
  $dh = opendir($dir_name);
  while ($file_name=readdir($dh)) {  	
  	if (($file_name!=".") 
  	     && ($file_name!="..") 
  	     && ($file_name!="index.html") 
  	     && ($file_name!="Recycled")  
  	     && ($file_name!="System Volume Information")) {
  	       
  		if (is_dir($dir_name."/".$file_name)) {
  		  $this->read_dir($dir_name."/".$file_name);
  	  }
  		else	{
  			$this->files[]=strtolower($dir_name."/".$file_name);
  			if (strtolower($file_name)=="thumbs.db"){
  				//echo "deleting thimbs.db #{$dir_name}\\{$file_name}#<br>";
  				unlink($dir_name."/".$file_name);};
  		}
  	}
  }
  
  if (sizeof($this->files)>0) {
  	return $this->files;
  }
  else {
  	return "";
  }
  closedir($dh);
}

function raw_read_dir($dir_name, $recursive = 0) {

  if (substr($dir_name,-1)=="/" || substr($dir_name,-1)=="\\"){
    $l = strlen($dir_name); 
    $dir_name = substr($dir_name,0,$l-1);
  }
  
  $dh = opendir($dir_name);
  while ($file_name=readdir($dh)) {  	
  	if (($file_name!=".") && ($file_name!="..")) {
  	  $fn = $dir_name."/".$file_name;
  	 
  		if (is_dir($fn) && $recursive) {
  		  $this->raw_read_dir($fn, $recursive);
  	  }  		
  	  $this->files[] = $fn;   
  	}
  }
  closedir($dh);
  
  if (sizeof($this->files)>0) {
  	return $this->files;
  }
  else {
  	return array();
  }
}


function create_dirtree($dir) {
	$ar = explode("/", $dir);	
	$s = (preg_match("#^/#msi", $dir, $m)) ? "/" : "";
	
	$ret = array();
	foreach($ar as $d) {	  
	  $d = trim($d); if ($d=='') continue;	  
	  $t = $s.$d;
		$s .= $d."/";	
		
		if (!file_exists($t) || !is_dir($t)) {
			$r = mkdir($t, 0777);				
			if ($r) {
			  $ret[] = $d;
			}
			else {
			  t("Can't create {$t}");
			}
		}	  
	}
	
	return implode("/", $ret);
}

/**
 * Список файлов в указанной директории
 * ! не рекурсивная
 *
 * @param unknown_type $path
 * @param unknown_type $dirs
 * @param unknown_type $files
 * @return unknown
 */
function file_list($path, $dirs = 1, $files = 1) {
  
  $ar = array();
  
  // удаляем слеши в конце
  $path = preg_replace("#(/|\\\\)*$#si", "", $path);
  
  
  $dh = opendir($path);
  while ($fn = readdir($dh)) {  	
  	if (($fn!=".") && ($fn!="..")) {  	  
  	  $c_path = $path."/".$fn;     
  		if (is_dir($c_path) && $dirs) {
  		  $ar[] = $fn;
  	  }
  		elseif ($files)	{
  			$ar[] = $fn;
  		}
  	}
  }
  
  closedir($dh);  
   
  return $ar;
}

function php_chmod($path, $val, $recursive = 0) {
  
  $ar = $this->raw_read_dir($path, $recursive);
  foreach($ar as $p) {
    chmod($p, $val);
  }
 
  return $ar;
}

function remove_dir($dir="") {
  $dir = trim($dir);
  if ($dir=='') {
    die("Cant'remove dir, path not specified.");
  }
  
  if (!file_exists($dir) || !is_dir($dir)) {
    die("Can't remove dir, becouse path not exists or not dir.");
  }
  
  $ar = $this->raw_read_dir($dir, 1);
  $ar[] = $dir;
  
  foreach($ar as $fn) {
    if (is_dir($fn)) {
      $res = (rmdir($fn)) ? "ok" : "error";
    }
    else {
      $res = (unlink($fn)) ? "ok" : "error";
    }
    echo " = deleting <b>{$fn}</b> - {$res}<br>";
  }
}

// database
	
function get_list($page="", $object=0, $cat=0, $over=0)	 {
  global $db, $std, $sv;
  $object = intval($object);
  $page = $db->ad($page);
  
  $cat = intval($cat);
  $wh = ($cat>0) ? " cat='{$cat}' " : "1=1";
  $ov = ($over && $cat>0) ? "" : "page='{$page}' AND object='{$object}' AND ";
  $db->q("
  SELECT * fROM {$this->t} 
  WHERE {$ov} {$wh} ORDER BY filename ASC", __FILE__, __LINE__);
  
  $ar = array();
  while ($d = $db->f()) {
    $d = $this->parse($d);    
    
    $ar[$d['id']] = $d;
    
  }
  
  return $ar;
  
}
	
function parse($d)	 {
  global $sv, $std;
  
  $d['size_kb'] = $std->size_kb($d['size']);
  $d['size_mb'] = $std->size_mb($d['size']);
  
  $d['f_size'] = ($d['size_kb']>1000) ? $d['size_mb']." МБ" : $d['size_kb']." КБ";
  
  $d['f_filename'] = preg_replace("#[^\w\.]#msi", "", $d['filename']);
  
  
  $icon = (in_array($d['ext'], $sv->cfg['file_types']['icons'])) ? $d['ext'] : "file";
  
  $d['icon_path'] = $sv->cfg['paths']['file_icons'].$icon.".gif";
  $d['icon'] = "<img src='/{$d['icon_path']}' border=0 alt=\"[file={$d['id']}]\" title=\"[file={$d['id']}]\">";
  
  $d['sub'] = $sub = $this->get_sub($d['page'], $d['object']);
  
  

  //image
  if ($d['type']==1) {
    $d['resize_name'] = $this->resize_name($this->gdir.$sub.$d['savename']);
    $d['resize_exists'] = (file_exists($d['resize_name'])) ? 1 : 0;
    
    if ($d['resize_exists']) {
       $x = getimagesize($d['resize_name']);
       $d['resize_w'] = $x[0];
       $d['resize_h'] = $x[1]; 
    }
      
     
  }
  else {
    $icon = (file_exists("sources/img/icons/{$d['ext']}.gif")) ? $d['ext'] : "attache";
    
   
  }
  

  $d['open_url'] = $this->gurl.$sub.$d['savename'];
  $d['open_resize_url'] =  $this->gurl.$sub."thumb_".$d['savename'];  
  $d['open_resize_url'] = ($d['w']<150) ? $d['open_url'] : $d['open_resize_url'];
  
  $d['url'] = $this->gurl.$sub.$d['savename'];
  $d['url_resize'] =  $this->gurl.$sub."thumb_".$d['savename'];

  $this->check_pos($d);

  return $d;
}

function catch_submit($page='', $object=0, $type='default', $fid = 0) {
global $sv, $db, $std;	   
    $err = false;
    $errm = array();
    
    $spage = $sv->_post['subpage'];
    
    $fid = intval($fid);
    $object = intval($object);
    
	  if (isset($sv->_get['subaction']) && $sv->_get['subaction']=='delete_file') {
      $file_id = intval($sv->_get['file_id']);			
      $this->delete_item($file_id);
    }
    
	  $ret = array(); $ar = array();
    for ($i=1; $i <= $this->max_threads; $i++) {
        $ar[] = "file_{$fid}_{$i}";
    }
    
   
    $submited = 0;
		foreach ($ar as $k) {
		 
		  
		  if (!isset($this->files[$k])) { 
		    continue;
		  } 
		  else { 
		    $submited++; 
		  }
	
			$up = $this->upload($k, $type);	
			
  
			if ($up['uploaded']) {
        $type = $db->ad($type);
        foreach($up as $k=>$v) {
          $up[$k] = $db->ad($v);
        }
        $uid = intval($sv->user['session']['account_id']);
        $sql = "
				  INSERT INTO {$this->t} SET
				   
            object='{$object}',
            page='{$page}', 
            filename='{$up['filename']}', 
            savename='{$up['savename']}',             
            size='{$up['filesize']}', 
            time='{$sv->post_time}',
            mime='{$up['mime']}',
            ext='{$up['ext']}',
            type='{$up['type']}',
            w='{$up['w']}',
            h='{$up['h']}',
            user='{$uid}'
            ";
       
				$db->q($sql, __FILE__, __LINE__);     

				$this->msgs[] = ucfirst($up['filename'])." (".$std->size_kb($up['filesize'])." КБ) успешно загружен";
			}
			else if ($up===false) {
			  $err = true;
			}
		}
   if (count($errm)==0 && $submited==0) return false;
   $ret['errm'] = $this->msgs;
   $ret['err'] = $err;
   
 
   return $ret;
}

function delete_item($id = 0, $dir='') {
  global $sv, $db, $std;	
  

  $dir = ($dir=='') ? $this->dir : $dir;
	$ret = array(); 
	$id = intval($id);
	$db->q("SELECT * FROM {$this->t} WHERE id='{$id}'");
	while ($d = $db->f())	{   
      
	  $d = $this->parse($d);
   // $d['resize_name'] = $this->resize_name_light( $dir.$d['filename'], $d['size'] );    
  
   
   $dir = $this->const_dir.$d['sub'];
   
    
		if (file_exists($dir.$d['savename'])) {
      unlink($dir.$d['savename']);
    }
    if (file_exists($dir."thumb_".$d['savename'])) {
      unlink($dir."thumb_".$d['savename']);
    }
    
   
  }
 
  $db->q("DELETE FROM {$this->t} WHERE id='{$id}'");
  if ($db->af()) {
    $ret = true;
  }
  else {
    $ret = false;
  }
  return $ret;
  
}
													

// templates

function upload_form($action, $type='default', $size=5, $fid = 0) {
  global $sv, $db, $std;
  
    $fid = intval($fid);
    
    $ext = (isset($this->types[$type]) && is_array($this->types[$type])) ? $this->types[$type] : array();
    $e = (count($ext)>0) ? implode(", ", $ext) : "<span style='color:red;'>типы файлов не определены</span>";
    $in="";
    for ($i=1; $i<=$size; $i++) {
      $in .= "<input name='file_{$fid}_{$i}' type=file size=20 style='font-size:10px;'><br><br>";
    }
  
    $ret = "
          <table  bgcolor=#cccccc cellpadding=5 cellspacing=1>
          <tr bgcolor=#efefef><td width=150><b>Прикрепить файлы</b><br><small>[ {$e} ]</td></tr>	
					<form ENCTYPE='multipart/form-data' action='{$action}' method=post>				
					<tr bgcolor=white>         
          <td align=center>
        	  <input type=hidden name='subaction' value='upload_file'>
            <input type=hidden name='subpage' value='{$type}'>
						<input type=hidden name='MAX_FILE_SIZE' value='999999000'>
					{$in}
						<input type=submit value='Прикрепить' style='font-size:10px;'></center>
					</td>
          </tr>	
					</form>								
					</table>         
    ";

    return $ret;

}				

function templates($d, $code='file', $static=0) {
  global $std;
  
  

  if ($static) {

      $alt = addslashes("{$d['title']}");
      $res = basename($d['resize_name']);
      switch ($code) {
        case 'image':
          $ret = "        
            <img src='{$this->url}{$d['savename']}' border=0 alt='{$alt}' title='{$alt}'>
          
          ";
        break;
        case 'preview':
          $ret = "<a href='{$this->url}{$d['savename']}'><img src='{$this->url}{$res}' border=0 alt='{$alt}' title='{$alt}'></a>";
        break;
        default: 
          $ret = "
          <table>  
          <tr><td>{$d['icon']}</td>
              <td><a href='".u('f', '', $d['id'])."'>{$d['filename']}</a> ({$d['f_size']})<br>
              <small style='color:#999999;'>{$tt}: {$d['dl']}{$last}</small></td>
          </tr>
          </table>
            
            ";
         
      }
          
    
    
  }
  else {
      $tt  = ($d['type']==1) ? "Просмотров" : "Скачиваний";
      $last = ($d['last_dl']>0) ? " / Last: ".$std->time->format($d['last_dl'], 0.5) : "";
      $alt = addslashes("{$tt}: {$d['dl']}{$last} / Файл: {$d['filename']}");

    
      switch ($code) {
        case 'image':
          $ret = "        
            <img src='".u('f', '', $d['id'])."' border=0 alt='{$alt}' title='{$alt}'>
          
          ";
        break;
        case 'preview':
          $ret = "<a href='".u('f', '', $d['id'])."'><img src='{$d['resize_name']}' border=0 alt='{$alt}' title='{$alt}'></a>";
        break;
        default: 
          $ret = "
          <table>  
          <tr><td>{$d['icon']}</td>
              <td><a href='".u('f', '', $d['id'])."'>{$d['filename']}</a> ({$d['f_size']})</td>
          </tr>
          </table>
            
            ";
         
      }
  }
  return $ret;
}

// render
function replace($t, $static=0) {
  global $sv, $db, $std;
  $ar = array();
  if (preg_match_all("#\[file=([0-9]+)\]#si", $t, $m)) {
    foreach($m[1] as $k)  {
      $k = intval($k);  $ar[$k]=1;
    }
  }
  if (preg_match_all("#\[image=([0-9]+)\]#si", $t, $m)) {
    foreach($m[1] as $k)  {
      $k = intval($k);  $ar[$k]=1;
    }
  }
  if (preg_match_all("#\[preview=([0-9]+)\]#si", $t, $m)) {
    foreach($m[1] as $k)  {
      $k = intval($k);  $ar[$k]=1;
    }
  }
  
  if (count($ar)==0) return $t;
  
  $keys = array_keys($ar);
  $in = implode(", ", $keys);
  $ar = array();
  $db->q("SELECT * FROM {$this->t} WHERE id IN ({$in})", __FILE__, __LINE__);
  while ($d = $db->f()) {
    $d = $this->parse($d);
    $ar[] = $d;
  }
    
  if (count($ar)==0) return false;
  
  $f = $r = array();
  foreach ($ar as $d) {
    $f[] = "#\[file={$d['id']}\]#si";
    $f[] = "#\[image={$d['id']}\]#si";
    $f[] = "#\[preview={$d['id']}\]#si";
    
    $r[] = $this->templates($d, 'file', $static);
    $r[] = $this->templates($d, 'image', $static);
    $r[] = $this->templates($d, 'preview', $static);
    
    
  }
  $t = preg_replace($f, $r, $t);
  

  
  return $t;
}


function get_sub($page, $object) {  
  
  $num = floor($object/100);  

  if ($num>100) {
    $num2 = floor($num/100);
    $num = "{$num2}/{$num}";
  }
    
  
  $dir = "{$page}/{$num}/{$object}/";   
  
  return $dir;
}


function get_dir($page, $object)   {
  global $std;
    
  $num = floor($object/100);
  
  if ($num>100) {
    $num2 = floor($num/100);
    $num = "{$num2}/{$num}";
  }
  
  $dir = $this->dir."{$page}/{$num}/{$object}/"; 
  
  if (!file_exists($dir) || !is_dir($dir)) {
    $std->file->create_dirtree($dir);
  }
  return $dir;
}
  
function set_current_dir($page, $object) {
  $this->t_d = $this->dir;
  $this->dir = $this->get_dir($page, $object);  
}
function restore_current_dir() {
  $this->dir = $this->t_d;  
}

function text_show_replace($t, $type='admin') {
  
  switch($type) {
    case 'user':
      //$t = preg_replace("#http\://img\.norcom\.ru#msi", "/img_norcom_ru", $t);
    break;
    default:
      $t = preg_replace("#http\://img\.norcom\.ru#msi", "img_norcom_ru", $t);
      
  }
  
  
  
  return $t;
}

function text_save_replace($t) {

  $t = preg_replace("#https\://redaktor\.wlan(\:447|\:444)#msi", "", $t);
  $t = preg_replace("#redaktor\.wlan(\:447|\:444)#msi", "", $t);
  $t = preg_replace("#=(\"|'|)[^=]*img_norcom_ru#msi", "=\\1http://img.norcom.ru", $t);
  $t = preg_replace("#/?img_norcom_ru#msi", "http://img.norcom.ru", $t);
 
  return $t;
}

function check_pos($d) {
  global $std, $sv;
  
  $d['path'] = $this->path($d, $d['savename']);
    
  
 // $d['old_path'] = "/usr/www/norcom_public/public_html/files/file/{$d['savename']}";


 // $d['r_old_path'] = $this->resize_name($d['old_path']);
 // $d['r_path'] =  $this->path($d, basename($d['r_old_path']));
 
  //echo "<pre>";print_r($d);
  /*
  if (!file_exists($d['path']) || filesize($d['path'])==0) {
    copy($d['old_path'], $d['path']);
    if (!file_exists($d['path']) || filesize($d['path'])==0) {
      echo "can't copy to: {$d['path']}<br>";      
    }    
  }
  */
  /*
  
  if (file_exists($d['r_old_path']) 
      && (!file_exists($d['r_path']) || filesize($d['r_path'])==0)) {
    copy($d['r_old_path'], $d['r_path']);
    if (!file_exists($d['r_path']) || filesize($d['r_path'])==0) {
      echo "can't copy resize to: {$d['r_path']}<br>";      
    }    
  }
*/
  
  return $d;
}

/**
 * ?
 *
 * @param unknown_type $d
 * @param unknown_type $filename
 * @return unknown
 */
function path($d, $filename)   {
  global $std;
  
  if (!in_array($d['page'], $this->page_types)) {
    die("creating dirtree (file::path) - page is not recognized");
  }
  
  $num = floor($d['object']/100);
  
  $dir = $this->gdir."{$d['page']}/{$num}/{$d['object']}/"; 
  
  if (!file_exists($dir) || !is_dir($dir)) {
    $this->create_dirtree($dir);
  }
  $path = $dir.$filename;
  
  return $path;
}
  

/**
 * Check all depend for uploading
 * !important: returns false if not_required upload && not filled file field
 * @param unknown_type $name
 * @param unknown_type $ext_ar
 * @param unknown_type $savedir
 * @param unknown_type $upload_reqired
 * @return unknown
 */
function check_upload($name, $ext_ar, $savedir, $upload_reqired=1) {
  global $std, $sv;
    $err = false;
    $errm = array();
    $ret = array( 
      'savedir' => $savedir,
      'tmp_name' => null,
      'savename' => null,
      'savepath' => null,
      'ext' => null,
      'mime' => '', 
      'name' => ''
    );
    
    if (!isset($sv->_files[$name]) || !is_array($sv->_files[$name])) {
      $err = true;
      $errm[] = "Файл не получен.";     
    }    
    else {
      $up = $sv->_files[$name];
      foreach($up as $k=>$v) {
        $ret[$k] = $v;
      }
      
      $ferr = intval($up['error']);
    }
  
    
    if (!$err && $ferr!=0) {
      
      if (!$upload_reqired && $ferr==4) {
        return false;
      }
      else {
        $err = true;
        $errm[] = $this->error_codes[$ferr];     
      }
    }
 
    //EXT check
    if (!$err) {
        $filename = $up['name'];
        
        if (preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $filename, $m)) {
          $raw_name = $m[1];
          $ext = strtolower($m[2]);
        }
        else {
          $raw_name = $filename;
          $ext = "";
        }  
        $ret['ext'] = $ext;
        $good = false;     
        foreach($ext_ar as $e)  {
          if ($e===$ext) {
            $good = true;
            break;
          }
        }    
        if (!$good) {
          $err = true;
          $errm[] = "Неразрешенный для загрузки тип файла, 
          расширения \"<b>{$ext}</b>\" нет в списке: ".implode(", ", $ext_ar);
          
        }
        if (!$err) {
          if ($ext=='php' || $ext=='phtml') {
            $err = true;
            $errm[] = "Загрузка php скриптов запрещена из соображений безопасности.";        
          }
        }
    }
    
    if (!$err) {      
      $name = $std->translit($raw_name);
      $name = str_replace(" ", "_", $name);
      $name = preg_replace("#[^a-z0-9\_\-]#msi", "", $name);
      $name = strtolower($name);
      $name = ($name=='') ? "undefined" : $name;    
      $savename = $name.".".$ext;
      $savepath = $savedir.$savename;    
      $i = 0;
      while (file_exists($savepath)) { $i++; if ($i>200) die("Бесконечный цикл в генерации имени файла: ".__FUNCTION__.__FILE__.__LINE__);
        $savename = $name."_{$i}".".".$ext;
        $savepath = $savedir.$savename;
      }  
      $ret['savename'] = $savename;
      $ret['savepath'] = $savepath;
    }      
   
    $ret['mime'] = (isset($ret['type'])) ? $ret['type'] : '';
    $ret['filename'] = $ret['name'];
      
    $ret['err'] = $err;
    $ret['errm'] = $errm;
    
   return $ret;  
}

/**
 * Генерация уникального имени файла с расширением взятого у заданного имени
 * в случае ошибки возвращается пустая строка
 * @param unknown_type $savedir
 * @param unknown_type $filename
 * @return unknown
 */
function unique_filename($savedir, $filename) {

  $ext = $this->extension($filename);
  
  // удаляем слеши в конце
  $savedir = preg_replace("#[\/|\\\]+$#si", "", $savedir);
  
  if (!file_exists($savedir) || !is_dir($savedir)) {
    echo "Dir <b>{$savedir}</b> for unique_filename not exists: ".__FUNCTION__.__FILE__.__LINE__;    
  }
  $savepath = $savedir."/".uniqid().".".$ext;    
  $i = 0;
  while (file_exists($savepath)) { $i++; 
    if ($i>200) { 
      echo "Limit for gen uniq filename is reached <b>{$savepath}</b>: ".__FUNCTION__.__FILE__.__LINE__;
      return '';
    }
    $savepath = $savedir."/".uniqid().".".$ext;    
  }  
  return $savepath;
}

function get_img_icon($filename) {
  global $sv;

  $ext = $this->extension($filename);
  
  $path = $this->icons_dir.$ext.".gif";
  $url = $this->icons_url.$ext.".gif";
  $d_url = $this->icons_url."attach.gif";
  $icon = (file_exists($path)) ? $url : $d_url;
  $ret = "<img src='{$icon}' width=16 height=16 border=0>";
  
  return $ret;
}
// endof class  
}





?>