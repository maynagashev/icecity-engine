<?php

class m_tag extends class_model {  
  
  var $tables = array(
    'tags' => "
      `id` bigint(11) NOT NULL auto_increment,
      `page` varchar(255) default NULL,
      `object` int(11) default NULL,
      `tag` varchar(255) default NULL,
      `hidden` tinyint(3) NOT NULL default '0',
      `created_at` datetime default NULL,
      `created_by` int(11) NOT NULL default '0',
      `updated_at` datetime default NULL,
      `updated_by` int(11) NOT NULL default '0',
      `expires_at` datetime default NULL,
      PRIMARY KEY  (`id`),
      KEY `page` (`page`),
      KEY `object` (`object`)    
    "
  );
    
function __construct()   {
  global $sv;
  
  $this->t = $sv->t['tags'];
  
}
  
/**
 * Парсинг и проверка строки, возвращает проверенную строку
 *
 * @param unknown_type $str
 * @param unknown_type $raw
 * @return unknown
 */
function parse_str($str, $raw=0) {
  
  $ar = explode(",", $str);
  
  $p = array();
  foreach($ar as $w) {
    $w = $this->wcut($w);
    if ($this->wcheck($w)) {
      $p[] = $w;
    }
  }
 
  $p = array_unique($p); 
  
  $ret =($raw) ? $p : implode(", ", $p);  
  
  
  return $ret;
}

/**
 * Formatting string and inserting links
 *
 * @param unknown_type $str
 * @param unknown_type $url
 * @param unknown_type $addon
 */
function format_str($str, $url, $addon="") {
  $ar = explode(",", $str);
  
  $p = array();
  foreach($ar as $w) {
    $w = $this->wcut($w);
    if ($this->wcheck($w)) {
      $p[] = $w;
    }
  }
 
  $p = array_unique($p); 
  
  $ar = array();
  foreach($p as $w) {
    $e = urlencode($w);
    $ar[] = "<a href='{$url}{$e}{$addon}'>{$w}</a>";
  }
  $ret = implode(", ", $ar);
  
  return $ret;
}
  
function parse_title($str) {
  $ar = explode(" ", $str);
  
  $p = array();
  foreach($ar as $w) {
    $w = $this->wcut($w);
    if ($this->wcheck($w)) {
      $p[] = $w;
    }
  }
 
  $p = array_unique($p); 
  
  return $p;
}
 
function wcut($w) {  
  
    $w = $this->tolower($w);
    
  $w = trim(preg_replace("#[^A-Za-zА-Яа-я0-9\.\ \_\-]#msi", "", $w));  
  
  $lim = 255;
  $i = 0;
  while (preg_match("#^(.*)(\.|_|\-)$#msi", $w, $m)) { $i++; if ($i>$lim) die("loop ".__FILE__.__LINE__);
    $w = $m[1];
  }
  
  $i = 0;
  while (preg_match("#^(\.|_|\-)(.*)$#msi", $w, $m)) { $i++; if ($i>$lim) die("loop ".__FILE__.__LINE__);
    $w = $m[2];
  }
  

  
  return $w;
}

function tolower($w) {
  
  $w = mb_strtolower($w);
  $w = str_replace("Ч", "ч", $w);

  return $w;
}

function wcheck($w) {
  $ret = true;
  
  if ($w=='') {
    return false;
  }
  
  if (strlen($w)<=1) {
    return false;
  }
  
  if ($this->filter($w)) {
    return false;
  }
  
  return $ret;
}

function update_records($page, $object, $ar, $h=0) {
  global $sv, $std, $db;
  
  $p =  array();
  $p['page'] = $page;
  $p['object'] = intval($object);
  $p['hidden'] = intval($h);
  $cr_on = addslashes($sv->date_time);
  
  
  //t($ar);
  $s = array();
  foreach($p as $k=>$v) {
    $p[$k] = addslashes($v);
    $s[] = "`{$k}`='{$p[$k]}'";    
  }
  
  
  $in_db = array();
  $db->q("SELECT * FROM {$this->t} 
  WHERE page='{$p['page']}' AND object='{$p['object']}' AND hidden='{$p['hidden']}'", __FILE__, __FILE__);
  while ($d = $db->f()) {
    $in_db[] = $d['tag'];
  }
  
  
  $to_insert = array();
  foreach($ar as $tag) {
    if (!in_array($tag, $in_db)) {
      $to_insert[] = $tag;
    }
  }
  
  $to_delete = array();
  foreach ($in_db as $tag) {
    if (!in_array($tag, $ar)) {
      $to_delete[] = $tag;
    }    
  }   
   
  $str = implode(", ", $s);
  foreach($to_insert as $tag) {
    $tag = addslashes($tag);
    $sql = "INSERT INTO {$this->t} SET {$str}, tag='{$tag}', created_at='{$cr_on}'";
    $db->q($sql, __FILE__, __LINE__);
  }
  
  $str = implode(" AND ", $s);
  foreach($to_delete as $tag) {
    $tag = addslashes($tag);
    $sql = "DELETE FROM {$this->t} WHERE {$str} AND tag='{$tag}'";
    $db->q($sql, __FILE__, __LINE__);
  }
    
  // t($db->log, 30);
  
}

function filter($w) {

if (preg_match("#^[0-9]{1,3}$#msi", $w)) {
  return 1;
}
  
$w = $this->tolower($w);

$ar = array(
'and',
'by',
'quotединый',
'of',
'notopic',
'test', 'the',
'бля', 'бы', 'без', 'было', 'бля33', 'бля333333',
'вы','вам', 'вот', 'вс', 
'голая','года', 
'да', 'для', 'до', 'должен', 'дня',  'даже',
'еще', 'есть',
'же',
'за', 'зарубежные',
'из', 'или',
'как', 'кто', 'комментариев', 'картинках',
'ли',
'мы', 'моя', 'мире', 'мне', 'морика', 'мои', 'мой', 'меня', 'мирquot',
'на', 'нет', 'не', 'ну', 'но','новые', 'новый', 'новая', 
'который','кто', 
'от', 'об', 'он', 'один', 
'по', 'пока', 'просто', 'пиздец', 'под', 'поймут', 'про', 'первый', 
'раз', 
'со', 'себе','сижу', 'срать','спит', 'свом', 'самый', 'софте',
'тот', 'то', 'тоже', 'только', 'так', 'такой', 'темы', 
'часто', 'что', 'чем', 'часть',
'этот', 'этим', 'это',
'секс',
'порно',
'занематься',
'любюанна',
'жоский'
);



$r = 0;
foreach($ar as $x) {
  if (!strcasecmp($x, $w))  {    
    $r = 1; // ec("{$w} равно {$x}");
  }  
  //else { ec("{$w} <> {$x}"); }
}
//$r = (in_array($w, $f)) ? 1 : 0;
return $r;
  
}

function get_html($ar, $url="./?tag=", $url_add="") {
  
  
  $tr = array();
  foreach($ar as $d) {
    $u = urlencode($d['tag_name']);
    $tr[] = "<a href=\"{$url}{$u}{$url_add}\" class=\"{$d['tag_class']}\">{$d['tag_name']}</a>";
    
  }
  
  $ret = "
  <table width=100%><tr><tD align=center style='padding: 5px 20px 5px 20px;'>
  ".implodE(" &nbsp; \n", $tr)."
  </td></tr></table>";
  
  return $ret;
  
}

function object_tags($str) {

  $ex = explode(",",$str);
  
  $ar = array();
  foreach($ex as $k=>$v) {
    $v = trim($v);
    $url = urlencode($v);
    if ($v!='') {
      $ar[] = "<a href='./?tag={$url}'>{$v}</a>";
    }
  }
  
  $ret = implode(", ", $ar);
  
  return $ret;
}
 
/**
 * сохранение разных типов данных
 */
 
/**
 * Посты на блоге
 *
 * @param unknown_type $post
 * @param unknown_type $tags
 * @param unknown_type $title
 * @param unknown_type $topic
 */
function set_post_tags($post, $tags, $title, $topic) {
global $sv, $std, $db;
    

  // поле для тегов 
  $tags_ar = $this->parse_str($tags, 1);  
  $this->update_records("post", $post, $tags_ar, 0);
  
  
  // другие источники
  $p = array();
  
  $title_ar = $this->parse_title($title);
  $p = array_merge($p, $title_ar);
  
  $topic_ar = $this->parse_title($topic);
  $p = array_merge($p, $topic_ar);
  
  $p = array_unique($p); 
  
  $this->update_records("post", $post, $p, 1);
  
}

/**
 * универсальное сохранение
 *
 * @param unknown_type $post
 * @param unknown_type $tags
 * @param unknown_type $title
 * @param unknown_type $topic
 */
function save_object_tags($page, $object, $tags, $title="", $topic="") {
global $sv, $std, $db;
    

  // поле для тегов 
  $tags_ar = $this->parse_str($tags, 1);  
  $this->update_records($page, $object, $tags_ar, 0);  
  
  // другие источники
  $p = array();
  
  $title_ar = $this->parse_title($title);
  $p = array_merge($p, $title_ar);
  
  $topic_ar = $this->parse_title($topic);
  $p = array_merge($p, $topic_ar);
  
  $p = array_unique($p); 
  
  $this->update_records($page, $object, $p, 1);
  
}

  
function aims_cloud($lim=1) {
  global $sv, $std, $db;

  $sv->load_model('cache'); 

  $page = 'aim';
  $c_code = 'tag_cloud_aims';
 
  $sv->m['cache']->sync($c_code);
  
  $log = array();
  
  $tags = array(); $tags_big = array();
  $db->q("SELECT tag, count(tag) as count  fROM {$this->t} WHERE page='{$page}' GROUP BY tag", __FILE__, __LINE__);
  
  while ($d = $db->f()) {
    if ($d['count']<=$lim) continue;    
    if ($this->filter($d['tag'])) continue;    
    $log[] = "{$d['tag']} = {$d['count']}";    
    $tags[] = array('tag_name'=>$d['tag'], 'tag_count' => $d['count']);
    
  }

  $tags = cloud_tags($tags);  
  $html = $this->get_html($tags, "/photo/tag/", "/");
  $sv->m['cache']->write($c_code, $html);
  
  return $log;
}

function articles_cloud($lim=1) {
  global $sv, $std, $db;

  $sv->load_model('cache'); 

  $page = 'article';
  $c_code = 'tag_cloud_articles';
 
  $sv->m['cache']->sync($c_code);
  
  $log = array();
  
  $tags = array(); $tags_big = array();
  $db->q("SELECT tag, count(tag) as count  fROM {$this->t} WHERE page='{$page}' GROUP BY tag", __FILE__, __LINE__);
  
  while ($d = $db->f()) {
    if ($d['count']<=$lim) continue;    
    if ($this->filter($d['tag'])) continue;        
    
    
    $log[] = "{$d['tag']} = {$d['count']}";    
    $tags[] = array('tag_name'=>$d['tag'], 'tag_count' => $d['count']);
    
  }

  $tags = cloud_tags($tags);  
  $html = $this->get_html($tags, "/articles/tag/", "/");
  $sv->m['cache']->write($c_code, $html);
  
  return $log;
}




} // end of model



/****************************************************************************************/
// Sorts a list of tags by their count ascending.

function tag_asort($tag1, $tag2)
{
  
   if($tag1['tag_count'] == $tag2['tag_count'])
   {
       return 0;
   }
   return ($tag1['tag_count'] < $tag2['tag_count']) ? -1 : 1;
}

/****************************************************************************************/
// Sorts a list of tags alphabetically by tag_name

function tag_alphasort($tag1, $tag2)
{
   if($tag1['tag_name'] == $tag2['tag_name'])
   {
       return 0;
   }
   return ($tag1['tag_name'] < $tag2['tag_name']) ? -1 : 1;
}

/****************************************************************************************/
// Assigns classes to each given tag.

function cloud_tags($tags)
{
	$tag_sizes = 10;

	usort($tags, "tag_asort");
	if(count($tags) > 0)
	{
		// Start with the sorted list of tags and divide by the number of font sizes (buckets).
		// Then proceed to put an even number of tags into each bucket.  The only restriction is
		// that tags of the same count can't span 2 buckets, so some buckets may have more tags
		// than others.  Because of this, the sorted list of remaining tags is divided by the
		// remaining 'buckets' to evenly distribute the remainder of the tags and to fill as
		// many 'buckets' as possible up to the largest font size.

		$total_tags = count($tags);
		$min_tags = $total_tags / $tag_sizes;

		$bucket_count = 1;
		$bucket_items = 0;
		$tags_set = 0;
		foreach($tags as $key => $tag)
		{
			$tag_count = $tag['tag_count'];

			// If we've met the minimum number of tags for this class and the current tag
			// does not equal the last tag, we can proceed to the next class.

			if(($bucket_items >= $min_tags) and $last_count != $tag_count and $bucket_count < $tag_sizes)
			{
				$bucket_count++;
				$bucket_items = 0;

				// Calculate a new minimum number of tags for the remaining classes.

				$remaining_tags = $total_tags - $tags_set;
				$min_tags = $remaining_tags / $bucket_count;
			}

			// Set the tag to the current class.
      if (!isseT($tags[$key]['tag_class'])) {
        $tags[$key]['tag_class'] = "";
      }
			$tags[$key]['tag_class'] = 'tag'.$bucket_count;
			$bucket_items++;
			$tags_set++;

			$last_count = $tag_count;
		}
		usort($tags, 'tag_alphasort');
	}

	return $tags;
}





?>