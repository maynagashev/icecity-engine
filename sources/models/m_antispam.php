<?php

/*

основные функции:
  init_captcha($url = "")  
  show_captcha($key1 = '')

  Условия показа капчи со шрифтами:
  if ( $allow_fonts AND function_exists('imagettftext') AND is_array( $fonts ) AND count( $fonts ) ) 		{
    if ( function_exists('imageantialias') ) 		{
    	imageantialias( $im, TRUE );
    }
			
  $key1 = md5(uniqid(""));
  $key2 = rand(10000, 99999);  
  В форме нужно передать оба значения, при том что одно из них покзаывается только на картинке:
  /tools/captcha/?id=$key1
  
  
*/

class m_antispam extends class_model {

var $tables = array(
  'antispam' => "
    `id` bigint(20) NOT NULL auto_increment,
    `key1` varchar(255) NOT NULL default '',
    `key2` varchar(255) NOT NULL default '',
    `time` int(11) NOT NULL default '0',
    `ip` varchar(255) NOT NULL default '',
    
    `created_at` datetime default NULL,
    `created_by` int(11) NOT NULL default '0',
    `updated_at` datetime default NULL,
    `updated_by` int(11) NOT NULL default '0',
    `expires_at` datetime default NULL,
      
    PRIMARY KEY  (`id`),
    KEY (`key1`)  
  "
);
var $spam_code = ""; //current code
var $gd_version = 2;
var $path_background = 'i/style_captcha/captcha_backgrounds';	
var $path_fonts      = 'i/style_captcha/captcha_fonts';
var $init = 0;
	
var $key1 = "";
var $key2 = "";
var $d_url = "/tools/captcha/?id=";
var $c_url = "";

// paint_email
var $public_tables = array('book');

/**
 * Название поля email для показа в виде картинки
 *
 * @var unknown_type
 */
var $email_field = "email";
/**
 * Цвета для бэка в pic2email
 *
 * @var unknown_type
 */

// параметры функции paint_text
var $bg_red   = 240;
var $bg_green = 240;
var $bg_blue  = 240;
var $pic_w = 200;
var $pic_h = 20;
var $text_align = "center"; // left | center | right


function __construct() {
  global $sv, $db;  
  
  $this->t = $sv->t['antispam'];
 
  $this->init_field(array(
  'name' => 'time',
  'title' => 'Time',
  'type' => 'int',   
  'input' => 'time',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit')
  ));    
  
  $this->init_field(array(
  'name' => 'key1',
  'title' => 'KEY1',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')  
  ));  
  
  $this->init_field(array(
  'name' => 'key2',
  'title' => 'KEY2',
  'type' => 'varchar',
  'len' => '50',
  'show_in' => array('default', 'remove'),
  'write_in' => array('edit', 'create')  
  ));    
  

  $this->init_field(array(
  'name' => 'ip',
  'title' => 'IP',
  'type' => 'varchar',  
  'show_in' => array('remove', 'default'),
  'write_in' => array('edit')
  ));  
    
}

function last_v($p) {
  global $sv;
  
  if ($sv->code=='create' || $sv->code=='add') {
    $p['time'] = $sv->post_time;
    $p['ip'] = $sv->ip;
    
  }
  
  return $p;
}

/**
 * Инициализация капчи
 *
 * @return готовые части html для формы
 */
function init_captcha($url = '') {
  global $sv, $std, $db;
  
  $this->remove_expired();  
  $this->generate_keys();
  
  
  $this->c_url = ($url!='') ? $url.$this->key1 : $this->d_url.$this->key1;
  
  $ret['img'] = "
  <img src='{$this->c_url}' width='150' height='45'
  alt='код подтверждения, включите картинки' 
  title='код подтверждения, включите картинки' style='border: 1px solid #999999;'>";
  
  $ret['input1'] = "<input type='hidden' size=20 name='new[key1]' value='{$this->key1}'>";
  $ret['input2'] = "<input type='text' size=10 name='new[key2]' value='' maxlength=7 style='font-size:100%;text-align:center;'>";
 
  $ret['html'] = "

    <table cellpadding=0 cellspacing=0 style='margin:0px;' border=0>      
      <tr>
        <td align=center rowspan=2 style='padding:5px 5px 0 0;'>{$ret['img']}</td>
        <td valign=bottom nowrap style='padding:0px;'><small>&#8592;&nbsp;введите код изображенный на картинке</td>
      </tr>
      <tr>
        <td align=center valign=top style='padding:0px;'>{$ret['input1']}{$ret['input2']}</td>
      </tr>
    </table>

      
  ";
  return $ret;
}

/**
 * Показ капчи по первому ключу
 *
 * @param unknown_type $key1 - если не задан берется из $sv->_get['id']
 */
function paint_captcha($key1 = '') {
  global $sv, $std, $db;
  
  if ($key1=='') {
    $key1 = (isset($sv->_get['id'])) ? $std->text->cut($sv->_get['id'], 'cut', 'mstrip') : $key1;
  }
  
  $key2 = $this->get_key2($key1);
  $key2 = ($key2!==false) ? $key2 : 'expired';
  
  $this->show_gd_img($key2);
  
  exit();
}

/**
 * Проеврка введенног кода
 *
 * @param unknown_type $key1 ключ
 * @param unknown_type $key2 введенный код
 * @return unknown
 */
function v_code($key1 = '', $key2 = '') {
  global $sv, $std;
  
  $err = true;
  
  $key1 = ($key1=='' && isset($sv->_post['new']['key1'])) ? $std->textcut($sv->_post['new']['key1'], 'allow', 'madd') : $key1;
  $key2 = ($key2=='' && isset($sv->_post['new']['key2'])) ? $std->textcut($sv->_post['new']['key2'], 'allow', 'madd') : $key2;
  $check = $this->get_key2($key1, true);

  if (!($check!==false && $check === $key2)) {
    $err = false;        
  }

  return $err;
}
/**
 * Показ email в виде картинки, если параметры не заданы то:
 * 1) проверяется sv->_get[]
 * 2) берутся значения по умолчанию book = id
 *
 * @param unknown_type $table
 * @param unknown_type $field 
 */
function paint_email($table = '', $field = '', $id = '') {
  global $sv, $std, $db;
  
  $err = 0;
  $text = "";
  
  $table = ($table=='' && isset($sv->_get['table'])) ? $std->text->cut($sv->_get['table'], 'allow', 'mstrip') : $table;
  $field = ($field=='' && isset($sv->_get['field'])) ? $std->text->cut($sv->_get['field'], 'allow', 'mstrip') : $field;
  $id = ($id=='' && isset($sv->_get['id'])) ? $std->text->cut($sv->_get['id'], 'allow', 'mstrip') : $id;
  $return = $this->email_field;
  
  $table = preg_replace("#[^a-z0-9\_\-]#si", "", $table);
  $field = preg_replace("#[^a-z0-9\_\-]#si", "", $field);
  $id = preg_replace("#[^a-z0-9\_\-]#si", "", $id);
  $return = preg_replace("#[^a-z0-9\_\-]#si", "", $return);
  
  $table = ($table=='' || !in_array($table, $this->public_tables)) ? "book" : $table;
  $field = ($field=='') ? "id" : $field;
  $return = ($return=='') ? "email" : $return;
  
  if (!isset($sv->t[$table])) {
    $err = 1;    
  }
  
  if (!$err) {
    $q = "SELECT `".$db->esc($return)."` FROM {$sv->t[$table]} WHERE `".$db->esc($field)."`='".$db->esc($id)."'";
    $db->q($q, __FILE__, __LINE__);
    if ($db->nr()>0) {
      $d = $db->f();
      $text = $d[$return];
    }
    else{
      $err = 1;
    }
  }
  
  if ($err) {
    $text = "--error--";
  }
  
  // show
  $this->paint_text($text);
  
}

// STUFF 

/**
 * Удаление устаревших записей
 *
 * @return unknown
 */
function remove_expired() {
  global $sv, $db;
  
  if ($this->init) return false;
  
  $exp = $sv->post_time - (60*30); //30 min
  $db->q("DELETE FROM {$this->t} WHERE `time`<'{$exp}'");
  $this->init = 1;
  
  return true;
}

/**
 * Генерация пары ключей и запись в базу
 *
 * @return array(key1 => '',  key2=> '')
 */
function generate_keys() {
	GLOBAL $sv,$db,$std;

  srand((double)microtime()*1000000);
  $key1 = md5(uniqid(""));
  $key2 = rand(10000, 99999);
  
  $db->q("INSERT INTO {$this->t} (key1, key2, time, ip) VALUES ('{$key1}', '{$key2}', '{$sv->post_time}', '{$sv->ip}')", __FILE__, __LINE__);
  
  $this->key1 = $key1;
  $this->key2 = $key2;
  
  return array('key1'=>$key1, 'key2'=>$key2);
}

/**
 * Получение второго ключа по первому
 *
 * @param unknown_type $key1
 * @param unknown_type $del - после проверки удалить запись
 * @return возвращает значение второго ключа либо false
 */
function get_key2($key1, $del = false) {
  GLOBAL $sv, $db;

  $key1 = $db->esc($key1);
  
  $res = $db->q("SELECT key2 FROM {$this->t} WHERE ip='{$sv->ip}' AND key1='{$key1}'", __FILE__, __LINE__);
  if ($db->nr()>0) {
    $d = $db->f();    
    if ($del) {
      $db->q("DELETE FROM {$this->t} WHERE ip='{$sv->ip}' AND key1='{$key1}'", __FILE__, __LINE__);    
    }    
    return $d['key2'];
  }
  else {
    return false;
  }
}

/**
 * Вывести картинку с заданным текстом
 *
 * @param unknown_type $text
 */
function paint_text($text)  {
  GLOBAL $sv,$db,$std;

  //$text=iconv("WINDOWS-1251", "UTF-8", $text);
  //$font="{$sv->sitedir}{$sv->fonts_dir}curlz.ttf";
  //imageTTFText($im,$size,0,$x,25,$black,$font,$text);   
  /*
  srand((double)microtime()*1000000);
  $x = rand(10, 25);
  $size = rand(20, 25);
  */
  
  $w = $this->pic_w;
  $h = $this->pic_h;
  
  $im = ImageCreate($w, $h);
  $backgroundcolor = imagecolorallocate($im, $this->bg_red, $this->bg_green, $this->bg_blue);
  $black  = imagecolorallocate($im, 14, 14, 4);  
  

  
  $pxlen = strlen($text)*8;
  $ost = $w - $pxlen;
  switch($this->text_align) {
    case 'left':
      $x = 0;
    break;
    case 'right':      
      $x = $ost;    
    break;
    case 'center': default:
      $x = round($ost/2);
    break;
  }
  
  imagestring($im, 4, $x, 3, $text, $black);
  Header("Content-type: image/jpeg");
    
  imagejpeg($im, '' ,100);    
  imagedestroy( $im );
	
	exit();
}

// IPB функции
function show_gd_img( $content="" ) {
	//-----------------------------------------
	// INIT
	//-----------------------------------------
	
	$content       = '  '. preg_replace( "/(\w)/", "\\1 ", $content ) .' ';
	$allow_fonts   = 1; 
	//isset( $this->ipsclass->vars['captcha_allow_fonts'] ) ? $this->ipsclass->vars['captcha_allow_fonts'] : 1;
	$use_fonts     = 1;
	$tmp_x         = 135;
	$tmp_y         = 20;
	$image_x       = 200;
	$image_y       = 60;
	$circles       = 3;
	$continue_loop = TRUE;
	$_started      = FALSE;

	//-----------------------------------------
	// Get backgrounds and fonts...
	//-----------------------------------------
	
	$backgrounds = $this->show_gd_img_get_backgrounds();

	$fonts       = $this->show_gd_img_get_fonts();

	//-----------------------------------------
	// Seed rand functions for PHP versions that don't
	//-----------------------------------------
	
	mt_srand( (double) microtime() * 1000000 );
	
	//-----------------------------------------
	// Got a background?
	//-----------------------------------------
	
	if ( $this->gd_version> 1 )
	{
		while ( $continue_loop )
		{
			if ( is_array( $backgrounds ) AND count( $backgrounds ) )
			{
				$i = mt_rand(0, count( $backgrounds ) - 1 );
			
				$background      = $backgrounds[ $i ];
				$_file_extension = preg_replace( "#^.*\.(\w{2,4})$#is", "\\1", strtolower( $background ) );
			
				switch( $_file_extension )
				{
					case 'jpg':
					case 'jpe':
					case 'jpeg':
						if ( ! function_exists('imagecreatefromjpeg') OR ! $im = @imagecreatefromjpeg($background) )
						{
							unset( $backgrounds[ $i ] );
						}
						else
						{
							$continue_loop = FALSE;
							$_started      = TRUE;
						}
						break;
					case 'gif':
						if ( ! function_exists('imagecreatefromgif') OR ! $im = @imagecreatefromgif($background) )
						{
							unset( $backgrounds[ $i ] );
						}
						else
						{
							$continue_loop = FALSE;
							$_started      = TRUE;
						}
						break;
					case 'png':
						if ( ! function_exists('imagecreatefrompng') OR ! $im = @imagecreatefrompng($background) )
						{
							unset( $backgrounds[ $i ] );
						}
						else
						{
							$continue_loop = FALSE;
							$_started      = TRUE;
						}
						break;
				}
			}
			else
			{
				$continue_loop = FALSE;
			}
		}
	}
	
	//-----------------------------------------
	// Still not got one? DO OLD FASHIONED
	//-----------------------------------------
	
	if ( $_started !== TRUE )	{
		if ( $this->gd_version == 1 ) {
			$im   = imagecreate($image_x, $image_y);
			$tmp  = imagecreate($tmp_x, $tmp_y);
		}
		else 		{
			$im  = imagecreatetruecolor($image_x, $image_y);
			$tmp = imagecreatetruecolor($tmp_x, $tmp_y);
		}
		
		$white  = ImageColorAllocate($tmp, 255, 255, 255);
		$black  = ImageColorAllocate($tmp, 0, 0, 0);
		$grey   = ImageColorAllocate($tmp, 200, 200, 200 );

		imagefill($tmp, 0, 0, $white);

		for ( $i = 1; $i <= $circles; $i++ )		{
			$values = array(
							0  => rand(0, $tmp_x - 10),
							1  => rand(0, $tmp_y - 3),
							2  => rand(0, $tmp_x - 10),
							3  => rand(0, $tmp_y - 3),
							4  => rand(0, $tmp_x - 10),
							5  => rand(0, $tmp_y - 3),
							6  => rand(0, $tmp_x - 10),
							7  => rand(0, $tmp_y - 3),
							8  => rand(0, $tmp_x - 10),
							9  => rand(0, $tmp_y - 3),
							10 => rand(0, $tmp_x - 10),
							11 => rand(0, $tmp_y - 3),
					     );

			$randomcolor = imagecolorallocate( $tmp, rand(100,255), rand(100,255),rand(100,255) );
			imagefilledpolygon($tmp, $values, 6, $randomcolor );
		}

		$num     = strlen($content);
		$x_param = 0;
		$y_param = 0;

		for( $i = 0; $i < $num; $i++ ) 		{
			$x_param += rand(6,12);
			$y_param = rand(-3,8);

			$randomcolor = imagecolorallocate( $tmp, rand(0,150), rand(0,150),rand(0,150) );

			imagestring($tmp, 5, $x_param+1, $y_param+1, $content{$i}, $grey);
			imagestring($tmp, 5, $x_param, $y_param, $content{$i}, $randomcolor);
		}

		//-----------------------------------------
		// Distort by resizing
		//-----------------------------------------

		imagecopyresized($im, $tmp, 0, 0, 0, 0, $image_x, $image_y, $tmp_x, $tmp_y);

		imagedestroy($tmp);
		
		//-----------------------------------------
		// Background dots and lines
		//-----------------------------------------

		$random_pixels = $image_x * $image_y / 10;

		for ($i = 0; $i < $random_pixels; $i++) {
			$randomcolor = imagecolorallocate( $im, rand(0,150), rand(0,150),rand(0,150) );
			ImageSetPixel($im, rand(0, $image_x), rand(0, $image_y), $randomcolor);
		}

		$no_x_lines = ($image_x - 1) / 5;

		for ( $i = 0; $i <= $no_x_lines; $i++ ) {
			ImageLine( $im, $i * $no_x_lines, 0, $i * $no_x_lines, $image_y, $grey );
			ImageLine( $im, $i * $no_x_lines, 0, ($i * $no_x_lines)+$no_x_lines, $image_y, $grey );
		}

		$no_y_lines = ($image_y - 1) / 5;

		for ( $i = 0; $i <= $no_y_lines; $i++ ) {
			ImageLine( $im, 0, $i * $no_y_lines, $image_x, $i * $no_y_lines, $grey );
		}
	}
	else 	{
		//-----------------------------------------
		// Can we use fonts?
		//-----------------------------------------
		
		if ( $allow_fonts AND function_exists('imagettftext') AND is_array( $fonts ) AND count( $fonts ) ) 		{
			if ( function_exists('imageantialias') ) 		{
				imageantialias( $im, TRUE );
			}
			
			$num       = strlen($content);
			$x_param   = -18;
			$y_param   = 0;
			$_font     = $fonts[ mt_rand( 0, count( $fonts ) - 1 ) ];
			
			for( $i = 0; $i < $num; $i++ )
			{
				$y_param     = rand( 35, 48 );
				
				# Main color
				$col_r       = rand(50,200);
				$col_g       = rand(0,150);
				$col_b       = rand(50,200);
				# High light
				$col_r_l     = ( $col_r + 50 > 255 ) ? 255 : $col_r + 50;
				$col_g_l     = ( $col_g + 50 > 255 ) ? 255 : $col_g + 50;
				$col_b_l     = ( $col_b + 50 > 255 ) ? 255 : $col_b + 50;
				# Low light
				$col_r_d     = ( $col_r - 50 < 0 ) ? 0 : $col_r - 50;
				$col_g_d     = ( $col_g - 50 < 0 ) ? 0 : $col_g - 50;
				$col_b_d     = ( $col_b - 50 < 0 ) ? 0 : $col_b - 50;
				
				$color_main  = imagecolorallocate( $im, $col_r, $col_g, $col_b );
				$color_light = imagecolorallocate( $im, $col_r_l, $col_g_l, $col_b_l );
				$color_dark  = imagecolorallocate( $im, $col_r_d, $col_g_d, $col_b_d );
				$_slant      = mt_rand( -20, 40 );
				
				if ( $i == 1 OR $i == 3 OR $i == 5 )
				{
					for( $ii = 0 ; $ii < 2 ; $ii++ )
					{
						$a   = $x_param + 50;
						$b   = mt_rand(0,100);
						$c   = $a + 20;
						$d   = $b + 20;
						$e   = ( $i == 3 ) ? mt_rand( 280, 320 ) : mt_rand( -280, -320 );
						
						imagearc( $im, $a  , $b  , $c, $d, 0, $e, $color_light );
						imagearc( $im, $a+1, $b+1, $c, $d, 0, $e, $color_main );
					}
				}
				
				if ( ! $_result = @imagettftext( $im, 24, $_slant, $x_param - 1, $y_param - 1, $color_light, $_font, $content{$i} ) )
				{
					$use_fonts = FALSE;
					break;
				}
				else
				{
					@imagettftext( $im, 24, $_slant, $x_param + 1, $y_param + 1, $color_dark, $_font, $content{$i} );
					@imagettftext( $im, 24, $_slant, $x_param, $y_param, $color_main, $_font, $content{$i} );
				}
				
				$x_param += rand( 15, 18 );
			}
			
			$use_fonts = TRUE;
		}
		
		if ( ! $use_fonts )
		{
			//-----------------------------------------
			// Continue with nice background image
			//-----------------------------------------
		
			$tmp         = imagecreatetruecolor($tmp_x  , $tmp_y  );
			$tmp2        = imagecreatetruecolor($image_x, $image_y);
	
			$white       = imagecolorallocate( $tmp, 255, 255, 255 );
			$black       = imagecolorallocate( $tmp, 0, 0, 0 );
			$grey        = imagecolorallocate( $tmp, 100, 100, 100 );
			$transparent = imagecolorallocate( $tmp2, 255, 255, 255 );
			$_white      = imagecolorallocate( $tmp2, 255, 255, 255 );
		
			imagefill($tmp , 0, 0, $white );
			imagefill($tmp2, 0, 0, $_white);
		
			$num         = strlen($content);
			$x_param     = 0;
			$y_param     = 0;

			for( $i = 0; $i < $num; $i++ )
			{
				if ( $i > 0 )
				{
					$x_param += rand( 6, 12 );
				}
			
				$y_param  = rand( 0, 5 );
			
				$randomcolor = imagecolorallocate( $tmp, rand(50,200), rand(50,200),rand(50,200) );

				imagestring( $tmp, 5, $x_param + 1, $y_param + 1, $content{$i}, $grey );
				imagestring( $tmp, 5, $x_param    , $y_param    , $content{$i}, $randomcolor );
			}
		
			imagecopyresized($tmp2, $tmp, 0, 0, 0, 0, $image_x, $image_y, $tmp_x, $tmp_y );
		
			$tmp2 = $this->show_gd_img_wave( $tmp2, 8, true );
		
			imagecolortransparent( $tmp2, $transparent );
			imagecopymerge( $im, $tmp2, 0, 0, 0, 0, $image_x, $image_y, 100 );
	
			imagedestroy($tmp);
			imagedestroy($tmp2);
		}
	}
	
	//-----------------------------------------
	// Blur?
	//-----------------------------------------
	
	if ( function_exists( 'imagefilter' ) )
	{
		@imagefilter( $im, IMG_FILTER_GAUSSIAN_BLUR );
	}
	
	//-----------------------------------------
	// Render a border
	//-----------------------------------------
	
	$black = imagecolorallocate( $im, 0, 0, 0 );
	
	imageline( $im, 0, 0, $image_x, 0, $black );
	imageline( $im, 0, 0, 0, $image_y, $black );
	imageline( $im, $image_x - 1, 0, $image_x - 1, $image_y, $black );
	imageline( $im, 0, $image_y - 1, $image_x, $image_y - 1, $black );
	
	//-----------------------------------------
	// Show it!
	//-----------------------------------------
	
	@header( "Content-Type: image/jpeg" );
	
	imagejpeg( $im );
	imagedestroy( $im );
	
	exit();
}

function show_gd_img_get_backgrounds() {
	//-----------------------------------------
	// INIT
	//-----------------------------------------
	
	$images = array();
	$_path  = $this->path_background;
	
	if ( $_dir = @opendir( $_path ) )
	{
		while( false !== ( $_file = @readdir( $_dir ) ) )
		{
			if ( preg_match( "#\.(gif|jpeg|jpg|png)$#i", $_file ) )
			{
				$images[] = $_path . '/' . $_file;
			}
		}
	}
	
	return $images;
}

function show_gd_img_get_fonts()
{
	//-----------------------------------------
	// INIT
	//-----------------------------------------
	
	$fonts  = array();
	$_path  = $this->path_fonts;
	
	if ( $_dir = @opendir( $_path ) )
	{
		while( false !== ( $_file = @readdir( $_dir ) ) )
		{
			if ( preg_match( "#\.(ttf)$#i", $_file ) )
			{
			  if (preg_match( "#symbol#msi", $_file )) continue;
				$fonts[] = $_path . '/' . $_file;
			}
		}
	}
	
	return $fonts;
}

function show_gd_img_wave( $im, $wave=10 )
{
	$_width  = imagesx( $im );
	$_height = imagesy( $im );

	$tmp = imagecreatetruecolor( $_width, $_height );

	$_direction = ( time() % 0 ) ? TRUE : FALSE;

	for ( $x = 0; $x < $_width; $x++ )
	{
		for ( $y = 0 ; $y < $_height ; $y++ )
		{
			$xo = $wave * sin( 2 * 3.1415 * $y / 128 );
			$yo = $wave * cos( 2 * 3.1415 * $x / 128 );

			$_x = $x - $xo;
			$_y = $y - $yo;
			
			if ( ($_x > 0 AND $_x < $_width) AND ($_y > 0 AND $_y < $_height) )
			{
				$index  = imagecolorat($im, $_x, $_y);
             		$colors = imagecolorsforindex($im, $index);
             		$color  = imagecolorresolve( $tmp, $colors['red'], $colors['green'], $colors['blue'] );
			}
			else
			{
				$color = imagecolorresolve( $tmp, 255, 255, 255 );
			}

			imagesetpixel( $tmp, $x, $y, $color );
		}
	}

	return $tmp;
}
	
	
//eoc
}
  

?>