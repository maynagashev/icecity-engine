{literal}
<style>
  .n-details { margin-bottom: 20px; }
  .n-details h1 { margin: 0;}
  div.n-title { font-size: 110%; font-weight: bold; margin-bottom: 5px;}
  div.n-date { color: gray; font-size: 90%;}
  div.n-text { padding: 10px 0 20px 20px; font-size: 12px;}
  div.n-text p {padding: 0 0 5px 0; font-size: 12px;}
  div.n-header { font-size: 120%; font-weight: bold; margin: 20px 0 10px 0;;}
  div.comments {padding: 10px 20px;}
  .newsline h1 {margin: 0 0 20px 0;}
  .newsline {padding: 0 20px;}
  .newsline hr {margin-bottom: 20px;}
</style>
{/literal}

{if $ar.content}<div class="topcontentpage">{$ar.content}</div>{/if}


{if $m->code=='public_list'}
<div class='newsline'>
<h1>Новости проекта Абакан24.ру</h1>


  {foreach from=$ar.list item=d}
    {include file='blocks/news_row.tpl'}
  {/foreach}
  
  {if $ar.count<=0}
    <div style='font-size: 120%;'>Список пуст.</div>
  {/if}
  
  {include file='parts/pl.tpl'}
  
  <div style='margin:10px 0;padding-top: 10px;'>{include file='blocks/news_months.tpl'}</div>
  
</div>  
  
{elseif $m->code=='public_month'}

  <div class="n-header">Архив новостей</div>
  <div style='margin:10px 0;background-color: #efefef; padding: 10px 10px;'>{include file='blocks/news_months.tpl'}</div>
  
  {foreach from=$ar.list item=d}
    {include file='blocks/news_row.tpl'}
  {/foreach}
  
  {if $ar.count<=0}
    <div style='font-size: 120%;'>Список пуст.</div>
  {/if}
    
  {include file='parts/pl.tpl'}
  
  
  
{elseif $m->code=='public_item'}
  {assign var='d' value=$ar.d}
  <div class='n-details'>
    <div class="n-title"><h1>{$d.title}</h1></div>
  	<div class="n-date">{$d.f_date}</div>
  	<div class="n-text">{if $d.text}{$d.text}{else}{$d.ann|nl2br}{/if}</div>
  	
    <table width="100%">
      <tr>
        <td>{if $d.f_copyright}Источник: {$d.f_copyright}{/if}</td>
        <td align="right">
         
        </td>
      </tr>
    </table>  
    </div>
  </div>
  
  {if $m->comments_on}
    <div class='comments'>
    {include file='blocks/comments.tpl'}
    </div>
  {/if}
  

{/if}
