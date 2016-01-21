
{if $m->allow_newtopic}
  <div class='topic-opts'>
    <a href='{$m->c_forum_url}?newtopic'>Создать тему</a> 
    &nbsp; &nbsp;
    <a href='{$m->c_forum_url}?answer&topic={$ar.d.id}'>Ответить</a> 
  </div>
{/if}



{include file='parts/pl.tpl'}

<table width="100%" cellpadding="5" cellspacing="1" class="ct" style='margin-bottom: 0;'>
  <tr class="t-head"><tD colspan="2">{$ar.d.title}{if $ar.d.description}, {$ar.d.description}{/if}</td></tr>
</table>

  {assign var='d' value=$ar.first}
    {include file='forum/post_row.tpl'}
  
  {foreach from=$ar.list item=d}  
    {include file='forum/post_row.tpl'}
  {/foreach}



{include file='parts/pl.tpl'}