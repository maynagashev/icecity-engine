<?php
/**
 * Класс для быстрого подключения markitup редактора к формам
 * Используется редактор: http://markitup.jaysalvat.com
 *
 */
class std_markitup {
  
  var $markitup_url = "/sources/markitup/";
  var $emoticons_url = "/sources/markitup/emoticons/";
  var $use_emoticons = 1;
  var $use_light = 1; // only bbcode

  var $width = '500px'; // px 
  var $edit_width = '442px'; 
  var $text;

  var $use_tinymce = 0;
  
  var $js = "";
  
  var $tinymce_button = "";
  var $tinymce_mode = 0;
  var $content_css = "/css/style.css";  
  
function compile($selector = 'textarea', $type = 'html')   {
  global $sv;
  
  
  // если разрешено использование тмц
  if ($this->use_tinymce) {
    $this->init_tinymce();
  }
  
  
  if ($this->tinymce_mode) {
    $ret = $this->tinymce_code();
  }
  else {
    $ret = $this->markitup_code($selector, $type);
  }
  
  $this->js = $ret;
  
  return $ret;
}
/**
 * Получение js кода markitup
 *
 * @param unknown_type $object_selector
 * @param unknown_type $type
 * @return unknown
 */
function markitup_code($object_selector='textarea', $type = 'bbcode') {
  
    
  // перезаписываем ширину 
  $style = "
        <style>
        .markItUp  {
        	width:{$this->width} !important;
        }
        .markItUpEditor {
        	width:{$this->edit_width} !important;
        }
        </style>
  
  ";
  switch($type) {
    case 'html':
          
      $ret = "
        {$style}
        <link rel='stylesheet' type='text/css' href='{$this->markitup_url}skins/markitup/style.css' />
        <link rel='stylesheet' type='text/css' href='{$this->markitup_url}sets/html/style.css' />
        <script type='text/javascript' src='{$this->markitup_url}jquery.markitup.js'></script>
        <script type='text/javascript' src='{$this->markitup_url}sets/html/set.js'></script>
        
        <script language='JavaScript'>
          $(document).ready(function()	{
            $('{$object_selector}').markItUp(myHtmlSettings);
          }); 
        </script>      
      ";      
      
    break;
    
    case 'bbcode': default:
      $var = ($this->use_light) ? "myBbcodeSettingsLight" : "myBbcodeSettings";
      $ret = "
        {$style}
        <link rel='stylesheet' type='text/css' href='{$this->markitup_url}skins/markitup/style.css' />
        <link rel='stylesheet' type='text/css' href='{$this->markitup_url}sets/bbcode/style.css' />
        <script type='text/javascript' src='{$this->markitup_url}jquery.markitup.js'></script>
        <script type='text/javascript' src='{$this->markitup_url}sets/bbcode/set.js'></script>
        
        <script language='JavaScript'>
          $(document).ready(function()	{
            $('{$object_selector}').markItUp({$var});
          }); 
        </script>      
      ";
        
    break;
  }
  
  
  if ($this->use_emoticons) {
    
    $ret .= "
        <script language='JavaScript'>
        $(document).ready(function()	{
          $('#emoticons a').click(function() {
              emoticon = $(this).attr('title');
              $.markItUp( { replaceWith:emoticon } );
              return false;
          });
        });
        </script>  
        <div id='emoticons'>
          <a href='#' title=':D'><img src='{$this->emoticons_url}emoticon-happy.png' border='0'/></a>
        	<a href='#' title=':('><img src='{$this->emoticons_url}emoticon-unhappy.png' border='0'/></a>
        	<a href='#' title=':o'><img src='{$this->emoticons_url}emoticon-surprised.png' border='0'/></a>
        	<a href='#' title=':p'><img src='{$this->emoticons_url}emoticon-tongue.png' border='0'/></a>
        	<a href='#' title=';)'><img src='{$this->emoticons_url}emoticon-wink.png' border='0'/></a>
        	<a href='#' title=':)'><img src='{$this->emoticons_url}emoticon-smile.png' border='0'/></a>
        </div>
    ";

  }
  
  
  return $ret;
}


function tinymce_code() {
  global $sv, $std, $db;
  
$ret = <<<EOD

<script type="text/javascript" src="/sources/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "{$this->content_css}", 

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>

EOD;
  
  
  return $ret;
}


function bbcode2html($text) {
  
  $this->text = &$text;
	$text = trim($text);
	
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', array($this, "escape"), $text);
	
	// BBCode to find...
	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',	
					 '/\[i\](.*?)\[\/i\]/ms',
					 '/\[u\](.*?)\[\/u\]/ms',
					 '/\[img\](.*?)\[\/img\]/ms',
					 '/\[email\](.*?)\[\/email\]/ms',
					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
					 '/\[quote](.*?)\[\/quote\]/ms',
					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
					 '/\[list\](.*?)\[\/list\]/ms',
					 '/\[\*\]\s?(.*?)\n/ms'
	);
	// And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<img src="\1" alt="\1" />',
					 '<a href="mailto:\1">\1</a>',
					 '<a href="\1">\2</a>',
					 '<span style="font-size:\1%">\2</span>',
					 '<span style="color:\1">\2</span>',
					 '<blockquote>\1</blockquote>',
					 '<ol start="\1">\2</ol>',
					 '<ul>\1</ul>',
					 '<li>\1</li>'
	);
	$text = preg_replace($in, $out, $text);
	
	if ($this->use_emoticons) {
	  
  	// Smileys to find...
  	$in = array( 	 ':D', 	
  					 ':)',
  					 ':o',
  					 ':p',
  					 ':(',
  					 ';)'
  	);
  	// And replace them by...
  	$out = array(	 '<img alt=":D" src="'.$this->emoticons_url.'emoticon-happy.png" />',
  					 '<img alt=":)" src="'.$this->emoticons_url.'emoticon-smile.png" />',
  					 '<img alt=":o" src="'.$this->emoticons_url.'emoticon-surprised.png" />',
  					 '<img alt=":p" src="'.$this->emoticons_url.'emoticon-tongue.png" />',
  					 '<img alt=":(" src="'.$this->emoticons_url.'emoticon-unhappy.png" />',
  					 '<img alt=";)" src="'.$this->emoticons_url.'emoticon-wink.png" />'
  	);
    $text = str_replace($in, $out, $text);
	}

	
	// paragraphs
	$text = str_replace("\r", "", $text);
	$text = "<p>".ereg_replace("(\n){2,}", "</p><p>", $text)."</p>";
	$text = nl2br($text);
	

	$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', array($this, "removeBr"), $text);
	$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);
	
	$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', array($this, "removeBr"), $text);
	$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);
	
	return $text;
}

function escape($s) {	  
	$this->text = strip_tags($this->text);
	return "<div class='code'><div style='width:100%;overflow: auto;'><pre><code>".htmlspecialchars($s[1], ENT_QUOTES, 'cp1251')."</code></pre></div></div>";
}	

function removeBr($s) {
	return str_replace("<br />", "", $s[0]);
}
		
function init_tinymce() {
  global $sv, $std, $db;
  
  // проверяем переключение режима
  if (isset($sv->_get['tinymce_mode'])) {
    $_SESSION['tinymce_mode'] = ($sv->_get['tinymce_mode']) ? 1 : 0;
  }
  
  // текущий режим
  $this->tinymce_mode = (isset($_SESSION["tinymce_mode"]) && $_SESSION["tinymce_mode"]==1) ? 1 : 0;
    
  if ($this->tinymce_mode) {
    $c_mode = "расширенный";
    $s_mode = "обычный";
    $s = 0;
  }
  else {
    $c_mode = "обычный"; 
    $s_mode = "расширенный";
    $s = 1;
  }
  
  $url = u($sv->act, $sv->code, $sv->id)."&tinymce_mode={$s}";
  
  
  $this->tinymce_button = "  
  Текущий режим: <b>{$c_mode}</b> &nbsp;&nbsp;&nbsp;
  <input type='button' value='Переключить на {$s_mode}' onclick=\"if(confirm('Вы уверены что хотите переключить режим? Все несохраненные данные будут утеряны.')) {window.location.href='{$url}';} else {return false;};\">
  
  ";
  
}


function js($object_selector='textarea', $type = 'bbcode') {
  return $this->markitup_code($object_selector, $type);
}

}

?>