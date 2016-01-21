
	{if $sv->parsed.poll!==false}
	   <a name='active-poll'></a>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			{assign var="p" value=$sv->parsed.poll}
			<tr>
			    <td class="header_green"><b>Вопрос недели</b></td>
			</tr>
			</table>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
			    <td class="border_cell">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
					    <td class="question"><b>{$p.question}</b></td>
					</tr>
					<tr><td>
					<div id='active-poll'>
					{if $p.voted}
  					<div class='results'>
  					  {$p.results}
  					</div>					 
					{else}
  					  <div class='choices'>  		
  					  {if $p.list!==false}			  					 
      					{foreach from=$p.list item=d}
        					<div style='margin:5px 0;'><input type="radio" name='poll-cid' class='poll-cid' value="{$d.id}"><span>{$d.title}</span></div>
      					{/foreach}
      			  {else}
      			   <div><i>Варианты ответов пока что отсутствуют.</i></div>
      			  {/if}
              </div>  					
    					<div class='results' style='display:none;'>
    					</div>
    					
  					  <div id='msg' style='display:none;'></div>
  					  <div id='inputs'>
      					<input type="hidden" id="poll-id" value="{$p.id}">
      					<input type="submit" id="poll-submit" value="Голосовать">
  					  </div>
  					  
					{/if}
					<div id='wait' style="display:none;"><Table cellpadding="3" align="center"><tr><td style='padding-left: 23px;'><img src='/i/wait16.gif' width="16" height="16"></td><td style='padding-right: 10px;'>отправка&nbsp;данных</td></tr></table></div>
					</div>
					</td>
					</tr>
					<tr>
					    <td class="td_height" align="center"><a href="/polls/">Предыдущие опросы<img src="/i/arrow.gif" alt="" border="0"></a></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="shadow"><span class="voice_block_arrow"><img src="/i/voice_block_arrow.gif" alt="" width="25" height="20" border="0" align="top"></span><img src="/i/shadow.gif" alt="" width="170" height="5" border="0"></td>
			</tr>
			</table>
  {/if}	
			
  
  


