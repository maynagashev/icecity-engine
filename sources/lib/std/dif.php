<?php

class std_resize {
  
var $wh = 150;  
var $err = false;
var $errm = array();
var $c_format;
var $src;
var $target;

var $verbose = 1;  
var $log_all = array();
var $last_session = array();


// AUTO ===================
function auto_fixed($src, $target, $w, $h) {
  
  $this->log("resize->auto_fixed: from {$src} to {$target} @ {$w}x{$h}", 1);
  if (!file_exists($src)) {
    $this->log("src {$src} not exists");
    return false;
  }
  
  $im_src = $this->get_im_src($src);
  if (!$im_src) {
    $this->log("can't get im_src");
    return false;
  }
  
  $im_out = $this->resize_with_proportion($im_src, $w, $h);
  if (!$im_out) {
    $this->log("can't get im_out");
    return false;
  }
  
  $res = $this->save_results($im_out, $target);
  if (!file_exists($target) || filesize($target)<=0)  {
    $this->log("target {$target} not exists or null");
    return false;
  }
  
  return true;
}

function auto_fixed_nocrop($src, $target, $w, $h) {
  $this->log("resize->auto_fixed_nocrop: from {$src} to {$target} @ {$w}x{$h}", 1);
  if (!file_exists($src)) {
    $this->log("src {$src} not exists");
    return false;
  }  
  
  $im_src = $this->get_im_src($src);
  if (!$im_src) {
    $this->log("can't get im_src");
    return false;
  }
  
  $im_out = $this->resize_fixed_nocrop($im_src, $w, $h);
  if (!$im_out) {
    $this->log("can't get im_out");
    return false;
  }
  
  $res = $this->save_results($im_out, $target);
  if (!file_exists($target) || filesize($target)<=0)  {
    $this->log("target {$target} not exists or null");
    return false;
  }
  
  return true;
}

function auto_by_width($src, $target, $w) {
  
  $this->log("resize->auto_by_width: from {$src} to {$target} @ {$w}px", 1);
  if (!file_exists($src)) {
    $this->log("src {$src} not exists");
    return false;
  }
  
  $im_src = $this->get_im_src($src);
  if (!$im_src) {
    $this->log("can't get im_src");
    return false;
  }
  
  $im_out = $this->resize_by_width($im_src, $w);
  if (!$im_out) {
    $this->log("can't get im_out");
    return false;
  }
  
  $res = $this->save_results($im_out, $target);
  if (!file_exists($target) || filesize($target)<=0)  {
    $this->log("target {$target} not exists or null");
    return false;
  }
  
  return true;
}

/**
 * Ресайз по бОльшей стороне до определенного размера
 *
 * @param unknown_type $src
 * @param unknown_type $target
 * @param unknown_type $w
 * @return unknown
 */
function auto_by_major($src, $target, $w) {
  
  $this->log("resize->auto_by_major: from {$src} to {$target} @ {$w}px", 1);
  if (!file_exists($src)) {
    $this->log("src {$src} not exists");
    return false;
  }
  
  $im_src = $this->get_im_src($src);
  if (!$im_src) {   
    return false;
  }
  
  $im_out = $this->resize_by_major($im_src, $w);
  if (!$im_out) {    
    return false;
  }
  
  $res = $this->save_results($im_out, $target);
  if (!file_exists($target) || filesize($target)<=0)  {
    $this->log("target {$target} not exists or null");
    return false;
  }
  
  return true;
}

// sub actions, algoritms
 
function resize_no_proportion($im_src, $tw=50, $th=50) {
  global $sv, $std, $db;
  
  $w = imagesx($im_src); 
  $h = imagesy($im_src); 
  $im_out = imagecreatetruecolor($tw, $th);  
  imagecopyresampled (  $im_out, 
                        $im_src,
                        0, //dst_x
                        0, //dst_y
                        0, //src_x
                        0, //src_y
                        $tw,
                        $th, 
                        $w, 
                        $h); 
                            
  return $im_out;
}

/**
 * в данном случае картинка подгоняется под фиксированные рамки поэтому стороны 
 * могут быть меньше или равны заданным, пропорции сохраняются
 *
 * @param unknown_type $im_src
 * @param unknown_type $tw
 * @param unknown_type $th
 * @return unknown
 */
function resize_fixed_nocrop($im_src, $tw=50, $th=50) {
  global $sv, $std, $db;
  
  $img = array(
    'tw' => $tw,
    'th' => $th
  );
  
  $img['w_src'] = $w_src = imagesx($im_src); 
  $img['h_src'] = $h_src = imagesy($im_src); 
  
  
  // во сколько раз исходное больше нужного размера
  $img['prop_w'] = $prop_w = $w_src/$tw;
  $img['prop_h'] = $prop_h = $h_src/$th;
  
  t($img); 
  // определяем во сколько раз уменьшать картинку
  $prop = 1;  
  // если по ширине и высоте не больше (исходная картинка меньше ресайза), то и делать ничего не надо
  if ($prop_w<=1 && $prop_h<=1) {
    // пусто
  }
  // если по ширине или вцысоте больше, то уменьшаем по той стороне у которой множитель больше
  elseif ($prop_w>1 || $prop_h>1) {    
    // если по ширине больше уменьшаем, или множители одинаковы то берем за основу множитель ширины
    if ($prop_w>=$prop_h) {
      $prop = $prop_w;    
    }
    else {
      $prop = $prop_h;
    }    
  }
 
  $img['prop'] = $prop;
  
  // ширина и высота результата
  $img['w_r'] = $w_t = $w_src/$prop;
  $img['h_r'] = $h_t = $h_src/$prop;
    

  $im_out = imagecreatetruecolor($w_t, $h_t);     
  imagecopyresampled (  $im_out, 
                        $im_src,
                        0, 0, 0, 0, 
                        $w_t,
                        $h_t, 
                        $w_src, 
                        $h_src); 
//t($img);                              
  return $im_out;
}
function resize_with_proportion($im_src, $tw=50, $th=50) {
  global $sv, $std, $db;
  
  $w_src = imagesx($im_src); 
  $h_src = imagesy($im_src); 

  $img['w_r'] = $p_w = $tw;
  $img['h_r'] = $p_h = $th;
    
    //пропорции оригинала и результата ширина/высота
    $img['wp'] = $wp = $w_src/$h_src;
    $img['wp_r'] = $wp_r = $tw/$th;
    
    // иходник уже результата
    if ($wp < $wp_r) {
      $dst_x = 0;
      
      //shift Y
      $p = $tw / $w_src;
      $img['p_h'] = $p_h = $p * $h_src;
      
      if ($p_h > $img['h_r']) {
        // отступ по оси Y
        $dst_y = - round(($p_h - $th)/2);
      }
      
    }
    // исходник шире результата
    elseif ($wp > $wp_r) {
      $dst_y = 0;
      
      //shift X
      $p = $img['h_r'] / $h_src;
      $img['p_w'] = $p_w = $p * $w_src;
      
      if ($p_w > $img['w_r']) {
        // отступ по оси Х
        $dst_x = - round(($p_w - $tw)/2);
      }
    }
    else {
      $dst_x = 0;
      $dst_y = 0;
    }
            
    $img['dst_x'] = $dst_x; $img['dst_y'] = $dst_y; 
    
    
    $im_out = imagecreatetruecolor($img["w_r"], $img['h_r']);  
    imagecopyresampled (  $im_out, 
                          $im_src,
                          $dst_x, //dst_x
                          $dst_y, //dst_y
                          0, //src_x
                          0, //src_y
                          $p_w,
                          $p_h, 
                          $w_src, 
                          $h_src); 
                          
                          
    return $im_out;
}

function resize_by_width($im_src, $width) {
   
  $w_src = imagesx($im_src); 
  $h_src = imagesy($im_src); 

  $w_tn = $width;
  $h_tn = ($w_tn/$w_src)*$h_src;        
  
  // если ширина меньше то берем ширину оригинала.
  if ($w_src<$w_tn) {
    $w_tn = $w_src;
    $h_tn = ($w_tn/$w_src)*$h_src;
  }
    
  $im_out = imagecreatetruecolor($w_tn, $h_tn);     
  imagecopyresampled (  $im_out, 
                        $im_src,
                        0, 0, 0, 0, 
                        $w_tn,
                        $h_tn, 
                        $w_src, 
                        $h_src); 
                              
  return $im_out;
}

function resize_by_major($im_src, $size)  {

  $w_src = imagesx($im_src); 
  $h_src = imagesy($im_src); 

  
  if ($w_src > $h_src) {
    $w_tn = $size;
    $h_tn = ($w_tn/$w_src)*$h_src;     
  }
  else {
    $h_tn = $size;
    $w_tn = ($h_tn/$h_src)*$w_src;     
  } 
  
  $im_out = imagecreatetruecolor($w_tn, $h_tn);     
  imagecopyresampled (  $im_out, 
                        $im_src,
                        0, 0, 0, 0, 
                        $w_tn,
                        $h_tn, 
                        $w_src, 
                        $h_src); 
                              
  return $im_out;
      
}

// STUFF ==================


function get_im_src($path, $update_c_format = 1) {  
  
  $ext =(preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $path, $m)) ?  strtolower($m[2]) :  "";
  $ret = false;
  
  // not supported
  $ns = false;

  switch ($ext)
	{
		case 'jpg': case 'jpeg':
      if (imagetypes() & IMG_JPG) {$im_src = ImageCreateFromJPEG($path); } else {  $ns = true; } 
      if ($update_c_format) $this->c_format = "JPG";		
    break;
		case 'png': 
      if (imagetypes() & IMG_PNG) {$im_src = ImageCreateFromPNG($path); } else {$ns = true;} 
      if ($update_c_format) $this->c_format = "PNG";
    break;
		case 'gif': 
      if (imagetypes() & IMG_GIF) {$im_src = ImageCreateFromGIF($path); } else {$ns = true;}
      if ($update_c_format) $this->c_format = "GIF"; 
    break;
		default: 
		  $ns = true;
	}	

	if ($ns) {
	  $this->err = 1;	  
		$this->log("Не поддерживаемый тип файла <b>{$ext}</b> в {$path}");
		$ret = false;
	}
	elseif (!$im_src) {
	  $this->err = 1;
	  $this->log("Не удалось прочитать файл c изображением.");
	  $ret = false;
	}
	else {
	 $ret = $im_src;
	}
  return $ret;
}

function save_results($im_out, $target) {  

    if ($this->c_format == "JPG") { 
      imageJPEG($im_out, $target); 
    } 
    elseif ($this->c_format=="PNG") { 
      imagePNG($im_out, $target); 
    } 
    elseif ($this->c_format=="GIF") { 
      imageGIF($im_out, $target); 
    } 
    else {
      die("bad c_format = {$this->c_format} in ".__FILE__.__LINE__);
    }
    return true;
}

function log($str, $new_sess = 0) {
  
  if ($this->verbose>0) {
    $eol = ($this->verbose==1) ? "\n" : "<br>";
    echo $str.$eol;
   //ec($str);
  }
  
  if ($new_sess) {
    $this->last_session = array();
  }
  
  $this->last_session[] = $str;
  $this->log_all[] = $str;
  
  return true;
}
    

// OLD =======================
 
function by_width($path, $target, $width) {

  $this->src = $path;
  $this->target = $target;
  
  if (!$target) {
    die("target for resize by width {$width} not specified: {$path}");
  }
  
  if (!file_exists($path)) {
    $this->errm = __FUNCTION__." - source file not exists: {$path}";
    $this->err = 1;
  }

  if (!$this->err) {
    $im_src = $this->get_im_src($path);  
  }
 
	if (!$this->err) {	  	  
    $im_out = $this->resize_by_width($im_src, $width); 
    $this->save_results($im_out, $target);
	}
     
	$res = ($this->err) ? false : true;
	
	return $res;
}

function wh_resize($path, $target="") { 
  
  $wh = $this->wh;
  
  $err = false;
  $errm = array();
  $target = ($target=='') ? $path : $target;
  
  
  if (!file_exists($path)) {
    $this->log( __FUNCTION__." - source file not exists: {$path}", 1);
    $err = 1;
  }
  
  
  
  $ext =(preg_match("#^(.*)\.([a-z0-9]{2,10})$#msi", $path, $m)) ?  strtolower($m[2]) :  "";
 
  
  // resizeing   
  $ns = false;
  if (!$err) {
    switch ($ext)
  	{
  		case 'jpg': case 'jpeg':
        if (imagetypes() & IMG_JPG) {$im_src = ImageCreateFromJPEG($path); } else {  $ns = true; } 
        $t['format'] = "JPG";		
      break;
  		case 'png': 
        if (imagetypes() & IMG_PNG) {$im_src = ImageCreateFromPNG($path); } else {$ns = true;} 
        $t['format'] = "PNG";
      break;
  		case 'gif': 
        if (imagetypes() & IMG_GIF) {$im_src = ImageCreateFromGIF($path); } else {$ns = true;}
        $t['format'] = "GIF"; 
      break;
  		default: 
  		  $err = 1;
  		  $this->log("Не поддерживаемый тип файла <b>{$ext}</b> в {$path}");
  	}		
  	
  	if ($im_src==false) {
  	  $err = 1;
  	  $this->log("Не удалось прочитать файл.");	  
  	}
  }
    
	if (!$err) {	  
	  //$im_out = $this->resize_no_proportion($im_src, $wh, $wh);
    $im_out = $this->resize_with_proportion($im_src, $wh, $wh);                          
     
    
    if ($t["format"]=="JPG") { 
      imageJPEG($im_out, $target); 
    } 
    elseif ($t["format"]=="PNG") { 
      imagePNG($im_out, $target); 
    } 
    elseif ($t["format"]=="GIF") { 
      imageGIF($im_out, $target); 
    } 
	}
 
	
  $t['err'] = $err;
  $t['errm'] = $errm;
                 
  return $t;
}


function watermark($src_path, $wm_path, $margin = 10) {
  
  $err = 0;
  $im = $this->get_im_src($src_path);
  if (!$im) {
    $this->log("Не удалось прочитать файл с изображением.", 1);
    $err = 1;
  }
  
  $im_wm = $this->get_im_src($wm_path, 0);
  if (!$im_wm) {
    $this->log("Не удалось прочитать водяной знак.", 0);
    $err = 1;
  }
  
  if (!$err) {
    //$im = imagecreatetruecolor(100, 200);
    $h = imagesy($im);
    $w = imagesx($im);
    $wm_w = imagesx($im_wm);
    $wm_h = imagesy($im_wm);
    
    $x = $w - $wm_w - $margin;
    $y = $h - $wm_h - $margin;
    
    imagealphablending($im, true); 
    imagecopy($im, $im_wm, $x, $y, 0, 0, $wm_w, $wm_h);
    $ret = $this->save_results($im, $src_path); 
  }
  else {
    $ret = false;
  }
  return $ret;
}
  
//eoc
}


?>