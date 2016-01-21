				
				{*
				<div class="b-block b-block-poll">
					<p class="title">Опрос</p>
					<div class="content">
					
						<form class="f-pool" action="#" method="post" accept-charset="utf-8">
							<p>Уровень Вашего ежемесячного дохода составляет:</p>
							<div>

								<input type="radio" id="f-pool-answer-1" class="f-radio" name="answer" value="1" />
								<label for="f-pool-answer-1">До 10 000 руб.</label>
							</div><div>
								<input type="radio" id="f-pool-answer-2" class="f-radio" name="answer" value="2" />
								<label for="f-pool-answer-2">От 10 000 до 15 000 руб.</label>
							</div><div>
								<input type="radio" id="f-pool-answer-3" class="f-radio" name="answer" value="3" />
								<label for="f-pool-answer-3">От 15 000 до 25 000 руб.</label>

							</div><div>
								<input type="radio" id="f-pool-answer-4" class="f-radio" name="answer" value="4" />
								<label for="f-pool-answer-4">От 25 000 до 40 000 руб.</label>
							</div><div>
								<input type="radio" id="f-pool-answer-5" class="f-radio" name="answer" value="5" />
								<label for="f-pool-answer-5">Свыше 40 000 руб.</label>
							</div><div>
								<input type="submit" class="f-button" value="Голосовать" />

							</div>
						</form>
						
						<p class="read-more"><a href="#" title="Архив опросов">Архив опросов</a></p>
					</div>
				</div>
				
				*}
				

{if $sv->parsed.poll!==false}
  <a name='active-poll'></a>
  {assign var="p" value=$sv->parsed.poll}	
  
<div class="b-block b-block-poll">
	<p class="title">Опрос</p>
    <div class="content">
    	
      <div class="f-pool">
        <p>{$p.question}</p>
    
        <div id='active-poll'>
        
          {if $p.voted}
          	<div class='results'>
          	  {$p.results}
          	</div>					 

          {else}
          
          	  <div class='choices' style='padding: 5px 0;'>  		
          	  {if $p.list!==false}			  					 
          			{foreach from=$p.list item=d}
          				<div style='margin:5px 0;'><input type="radio" name='poll-cid' class='poll-cid' value="{$d.id}"><span>{$d.title}</span></div>
          			{/foreach}
          	  {else}
          	   <div><i>Варианты ответов пока что отсутствуют.</i></div>
          	  {/if}
              </div>  					
              
          		<div class='results' style='display:none;'></div>          		
          	  <div id='msg' style='display:none;'></div>
          	  <div id='inputs'>
          			<input type="hidden" id="poll-id" value="{$p.id}">
          			<input type="submit" id="poll-submit" class="f-button" value="Голосовать">
          	  </div>
          	  
          {/if}
          
          <div id='wait' style="display:none;margin:0; padding:0;"><Table cellpadding="3" align="center"><tr><td style='border:0;'><img src='/i/wait16.gif' width="16" height="16"></td><td style='padding-right: 10px;border:0;'>отправка&nbsp;данных</td></tr></table></div>
        </div>
      </div>

					
		<p class="read-more"><a href="/polls" title="Архив опросов">Архив опросов</a></p>
	</div>
</div>
							
  {/if}	
			
  
  				