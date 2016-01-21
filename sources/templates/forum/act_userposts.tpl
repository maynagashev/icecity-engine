
{include file='parts/pl.tpl'}

{foreach from=$ar.list item=d name=a}  
 
  <table width="100%" cellpadding="5" cellspacing="1" class="ct" style='margin-bottom: 0;{if $smarty.foreach.a.iteration>1}margin-top: 4px;{/if}'>
    <tr class="t-head"><tD colspan="2">{$d.title}{if $d.description}, {$d.description}{/if}</td></tr>
  </table>
  {include file='forum/post_row.tpl'}
  
{/foreach}

{if $ar.count<=0}
  <table width="100%" cellpadding="5" cellspacing="1" class="ct" style='margin-bottom: 0;'>
    <tr class="t-head"><tD colspan="2">{$ar.title} пользователя</td></tr>
  </table>
  <div class="textbox" style='margin-top:1px;margin-bottom: 25px;'>Список пуст.</div>
  

{/if}

{include file='parts/pl.tpl'}