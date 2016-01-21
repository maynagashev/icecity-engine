<?php

$sv->load_model('news');
$sv->m['news']->news_url = "/news/";

$ar = $sv->m['news']->item_list("`status_id`='1'", "`date` DESC", 10, 1);

$tr = array();
foreach($ar['list'] as $d) {
  
  $time = strtotime($d['date']);
  $day = date("d", $time);
  $month = $std->time->rus_month(date("j", $time));
  $year = date("Y", $time);
  
  $text = ($d['ann']) ? $d['ann'] : $d['text'];
 // $text = trim(strip_tags($text));
  
  //t($d);
  
  $stitle = $std->text->cut($d['title'], 'cut', 'cut');
  $tr[] = <<<EOD
<div class="post" id="post-{$d['id']}">

<h2><a href="{$d['url']}" rel="bookmark" title="Permanent Link to {$stitle}">{$d['title']}</a></h2>
<div class="entry">

  <p>{$d['f_date_rus']}</p>
  {$text}
</div>
          
<div class="spacer"></div>
	<ul class="post-data">
	 <li class="comments"><a href="{$d['url']}#respond" title="Комментарий на {$stitle}">Комментариев нет</a></li>
	</ul>


</div>
  
EOD;
  
}

/*
<li class="posted">
Posted in <a href="http://animahen.ru/?cat=1" title="Просмотреть все записи в Новости" rel="category">Новости</a>  

</li>

*/
$dblock_content = implode("\n", $tr);



?>