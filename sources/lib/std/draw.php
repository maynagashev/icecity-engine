<?php

class std_draw {
  var $gd_version = 2;
  
  
  function draw_text($text, $format = 'jpg') {
    
    $im = ImageCreate(180, 20);
    $backgroundcolor=imagecolorallocate($im,240,240,240);
    $black=imagecolorallocate($im,14,14,4);  
    $text = mb_convert_encoding($text, 'utf8', 'cp1251');
    imagestring($im, 4, 5, 0, $text, $black);
    
    if ($format == 'png') {
      Header("Content-type: image/png");
      imagepng($im);
    }
    else {
      Header("Content-type: image/jpeg");
      imagejpeg($im, null, 100);
    }
    exit();
    
  }
  
  
  function draw_font($text) {
    $text = "".$text;
    // 5px-sq-classic.ttf
    $font = "sources/fonts/visitor_rus.ttf";    
    $text = iconv("windows-1251", "UTF-8", $text);
    

    $size = 20;
    $ar = imagettfbbox($size, 0, $font, $text);
   // print_r($ar);
    $w = $ar[2]-$ar[0]+10;
    $h = abs($ar[7]-$ar[1])+8;
    
    
    $im   = imagecreate($w, $h);	
   
    
    $backgroundcolor = imagecolorallocate($im, 255,255,255);     
    $c  = ImageColorAllocate($im, 200, 200, 200);
    $b  = ImageColorAllocate($im, 0, 0, 0);
    
    
    $x = 0; $y=$h-5; 
    imagettftext( $im, $size, 0, $x, $y, $b, $font, $text) ;
	  //imagettftext( $im, $size, 0, $x+2, $y+1, $c, $font, $text) ;
	
  	header( "Content-Type: image/jpeg" );
  	
  	imagejpeg( $im );
  	imagedestroy( $im );  

	  exit();
    
  }
  
  
  

function draw_resize($fn, $ext,  $w=150) {
  global $std;
  
  $rf = "resize/{$w}".basename($fn);
  if (file_exists($rf)) {
    readfile($rf);
    exit();
  }
  //source exist
  if (!file_exists($fn)) {
    $this->draw_font("исходный  файл не найден");
    return false;    
  }
  
  // need resize
  if ($w<=0) {
    $this->draw_font("w<0");
    return $fn;
  }
  
  
  
  // resizeing 
  
    $t = array();
    $not_supported = false;
   
	  switch ($ext)
		{
			case 'jpg': case 'jpeg':
        if (imagetypes() & IMG_JPG) {$im_src = ImageCreateFromJPEG($fn); } else {  $not_supported = true; } 
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
		if ($t['im_src']===false) $not_supported=true;
    
    if ($not_supported) {
     $this->draw_font("resize filed not supported file type");
      return false; 
    }
    
    $img["w"] = imagesx($im_src); 
    $img["h"] = imagesy($im_src); 
    
    $img["w_thumb"] = $w;
    $img["h_thumb"] = ($img["w_thumb"]/$img["w"])*$img["h"]; 
    
    
   
  
	
    if( $this->gd_version >= 2 ) { 
            
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
       imageJPEG($im_out, $rf); 
     } 
     elseif ($t["format"]=="PNG") { 
       imagePNG($im_out, $rf); 
     } 
     elseif ($t["format"]=="GIF") { 
       imageGIF($im_out, $rf); 
     } 
     
  readfile($rf);
  exit();
  
  
  return $res_fn;
}

function table($d) {
  global $sv, $std;
  
  
  $ext = $std->file_extension($d['filename']);
  $big = "http://{$sv->host}/img_{$d['key1']}.{$ext}";
  $small = "http://{$sv->host}/img_s_{$d['key1']}.{$ext}";
  $m = "http://{$sv->host}/img_m_{$d['key1']}.{$ext}";
  
  $s_filename = $d['s_filename'];      
  $file = UPLOAD_DIR.$s_filename;
  
  $ar = getimagesize($file);
  $w = $ar[0];
  $h = $ar[1];
        
  
  
  $s1 = ($w>150) ? "
    <tr><td><small>С предпросмотром (ширина <b>150px</b>)<br>
      <textarea cols=65 rows=3>[url={$big}][img]{$small}[/img][/url]</textarea></td>
  </tr>  
  
  " : "";
  
  $s2 = ($w>400) ? "  <tr><td><small>Предпросмотр (ширина <b>400px</b>)<br>
      <textarea cols=65 rows=3>[url={$big}][img]{$m}[/img][/url]</textarea></td>
  </tr>  ":"";
$ret = <<<EOD

<Table align=left style='border-top: 1px solid dashed;'>
<tr>
  <tD><small>Адрес изображения:</td>
  <td><input type='text' size=50 value="{$big}" 
  style='border: 1px solid  #efefef;background-color:#73CA6C;padding-left:3px;color:white;'></td>
</tr>
<tr>
  <tD><small>Размер:</td>
  <td><input type='text' size=15 value="{$w}x{$h}"  
  style='text-align:center;border: 1px solid #efefef;background-color:#73CA6C;padding-left:3px;color:white;'></td>
</tr>
<tr><td colspan=2>
<div align=right><small>
  <a href='#' onClick="switch_box('forum_codes')">Коды для форумов</a>
</div>
</td></tr>
<tr id='forum_codes' style='display:none;'><tD colspan=2>
  <table width=100%>
  <tr><td><small>Просто картинка<br>
      <textarea cols=65 rows=2>[img]{$big}[/img]</textarea></td>
  </tr>
  {$s1}
  {$s2}
  
  </table>
</td></tr>

</table>
  
  
  
  
EOD;

  return $ret;
 
}

function ext($filename)
  {

  	$filename=basename($filename);
  	
  	if (preg_match("#\.([\w]+)$#msi", $filename, $m)) {
  	  $ext = strtolower($m[1]);
  	}
  	else {
  	  $ext = "";
  	}
 
  return $ext;	
  }  
  

  
function draw_userbar($text, $ct) {

  $font = "./sources/fonts/visitor_rus.ttf";    
  
  //$text = iconv("windows-1251", "UTF-8", $text);
  
  $text = mb_convert_encoding($text, "UTF-8", "windows-1251");
  $size = 8;
  $size2 = 7; 
  

  
  
  $ar = imagettfbbox($size, 0, $font, $text);
  // print_r($ar);
  $w = $ar[2]-$ar[0]+10;
  $h = abs($ar[7]-$ar[1])+8;
  
  
  $im   = imagecreatefromgif("./sources/img/userbar3.gif");	
  
  
  $backgroundcolor = imagecolorallocate($im, 255,255,255);     
  $w  = ImageColorAllocate($im, 255, 255, 255);
  $c  = ImageColorAllocate($im, 200, 200, 200); 
  $b  = ImageColorAllocate($im, 64, 93, 63);
  
  
  $x = 25; $y=$h-1; 
  
  imagettftext( $im, $size, 0, $x-1, $y, $b, $font, $text) ;
  imagettftext( $im, $size, 0, $x+1, $y, $b, $font, $text) ;
  imagettftext( $im, $size, 0, $x, $y+1, $b, $font, $text) ;
  imagettftext( $im, $size, 0, $x, $y-1, $b, $font, $text) ;
 
  imagettftext( $im, $size, 0, $x, $y, $w, $font, $text) ;
  
  
  //counts
  $y = $h+1;
  
  $px = ($ct[0]<10) ? 0 : (($ct[0]<100) ? -3 : (($ct[0]<1000) ? -6 : -9)); 
  imagettftext( $im, $size2, 0, 238+$px, $y, $w, $font, $ct[0]) ;
  
  $px = ($ct[1]<10) ? 0 : (($ct[1]<100) ? -3 : (($ct[1]<1000) ? -6 : -9));
  imagettftext( $im, $size2, 0, 270+$px, $y, $w, $font, $ct[1]) ;
  
  $px = ($ct[2]<10) ? 0 : (($ct[2]<100) ? -3 : (($ct[2]<1000) ? -6 : -9));
  imagettftext( $im, $size2, 0, 300+$px, $y, $w, $font, $ct[2]) ;
  
  $px = ($ct[3]<10) ? 0 : (($ct[3]<100) ? -3  : (($ct[3]<1000) ? -6 : -9));
  imagettftext( $im, $size2, 0, 332+$px, $y, $w, $font, $ct[3]) ; 
  
   
  header( "Content-Type: image/gif" );
  
  imagegif( $im );
  imagedestroy( $im );  
  
exit();

}

/**
 * 125x125 png
 *
 * @param unknown_type $url
 * @param unknown_type $rating
 */
function informer_rating($url, $rating) {
  global $sv;
  
  $image_w = 125;
  $image_h = 125;
    
  $font = PUBLIC_DIR."sources/fonts/visitor_rus.ttf";    
  $informer_path = PUBLIC_DIR."i/rating.png";
  $url = mb_convert_encoding($url, "UTF-8", "windows-1251");
  $size = 12;
  $size2 = 45;    

  $im   = imagecreatefrompng($informer_path);	
  
  
  $backgroundcolor = imagecolorallocate($im, 255,255,255);     
  $w  = ImageColorAllocate($im, 255, 255, 255);
  $c  = ImageColorAllocate($im, 200, 200, 200); 
  $b  = ImageColorAllocate($im, 64, 93, 63);
    
  // расчитываем положение урла по X
  $len = strlen($url);
  
  if ($len<10) {
    $size = 14;
  }
  elseif ($len<14) {
    $size = 12;
  }
  elseif($len<16) {
    $size = 10;
  }
  elseif ($len<20) {
    $size = 9;
  }
  elseif ($len<25) {
    $size = 8;
  }
  else {
    $size = 7;
  }
  $s = imagettfbbox ( $size , 0 , $font , $url);  $w = $s[2]-$s[0];     
  $pad_x = round(($image_w-$w)/2);
  imagettftext( $im, $size, 0, $pad_x, 25, $b, $font, $url) ;
  
  
  
  // значеие ретйинга
  $s = imagettfbbox ( $size2 , 0 , $font , $rating);  $w = $s[2]-$s[0];     
  $pad_x = round(($image_w-$w)/2);
  imagettftext( $im, $size2, 0, $pad_x, 79, $b, $font, $rating) ;  
  

  header( "Content-Type: image/png" );
  
  imagepng( $im );
  imagedestroy( $im );  
    
  exit();
}

/**
 * 88x31 png
 *
 * @param unknown_type $url
 * @param unknown_type $rating
 */
function button_rating($url, $rating) {
  global $sv;
  
  $image_w = 88;
  $image_h = 31;
    
  $font = PUBLIC_DIR."sources/fonts/visitor_rus.ttf";    
  $informer_path = PUBLIC_DIR."i/button.png";
  $url = mb_convert_encoding($url, "UTF-8", "windows-1251");
  $size = 15;

  $im   = imagecreatefrompng($informer_path);	
  
  $backgroundcolor = imagecolorallocate($im, 255,255,255);     
  $w  = ImageColorAllocate($im, 255, 255, 255);
  $c  = ImageColorAllocate($im, 200, 200, 200); 
  $b  = ImageColorAllocate($im, 64, 93, 63);
      
  // значеие ретйинга
  $s = imagettfbbox ( $size , 0 , $font , $rating);  $w = $s[2]-$s[0];     
  $pad_x = round($image_w-$w-8); 
  imagettftext( $im, $size, 0, $pad_x, 27, $b, $font, $rating) ;  
  

  header( "Content-Type: image/png" );
  
  imagepng( $im );
  imagedestroy( $im );  
    
  exit();
}


  
}
?>