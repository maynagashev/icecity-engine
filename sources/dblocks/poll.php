<?php

$sv->load_model('poll');
$p = $sv->m['poll']->html_block();

// если доступен блок
if ($p) {
  
  // если голосовал то результат
	if ($p['voted']) {
	  $list = "<div class='results'>".$p['results']."</div>";
	}
	// иначе варианты
	else {	  
	 // варианты
   if ($p['list']!==false) {			  					 
     $vs = "";
		 foreach($p['list'] as $d) {
			 $vs.= "<div style='margin:5px 0;'><input type='radio' name='poll-cid' class='poll-cid' value='{$d['id']}'><span>{$d['title']}</span></div>";
		 }
   }
   else {
	   $vs = "<div><i>Варианты ответов пока что отсутствуют.</i></div>";
   }
		  
	  
	  $list = "
		  <div class='choices'>  		
		  {$vs}
      </div>  					
			<div class='results' style='display:none;'></div>
			
		  <div id='msg' style='display:none;'></div>
		  <div id='inputs'>
				<input type='hidden' id='poll-id' value='{$p['id']}'>
				<input type='submit' id='poll-submit' value='Голосовать'>
		  </div>
		  ";
		  
	}
  
  
$dblock_content = <<<EOD

<a name='active-poll'></a>

<div id='active-poll'>  
  <table width='200' cellspacing="0" cellpadding="0" border="0">
  <tr><td class="question"><b>{$p['question']}</b></td></tr>
  
  <tr><td>
    {$list}  
    <div id='wait' style="display:none;">
      <Table cellpadding="3" align=center>
        <tr><td><img src='/i/wait16.gif' width="16" height="16"></td>
            <td style='padding-right: 10px;color: gray;'>отправка&nbsp;данных</td></tr>
      </table>
    </div>
  </td></tr>
  
  <tr><td align='center'><a href="/polls/">Предыдущие опросы&nbsp;<img src="/i/arrow.gif" alt="" border="0"></a></td></tr>
  </table>
</div>			

EOD;
}
else {
  $dblock_content = "";
}

?>