{if $ar.content}<div class="topcontentpage">{$ar.content}</div>{/if}


{if $m->code=='public_list'}

  {foreach from=$ar.list item=d}
    {include file='blocks/news_row.tpl'}
  {/foreach}
  
  {if $ar.count<=0}
    <div style='font-size: 120%;'>Список пуст.</div>
  {/if}
  
  {include file='parts/pl.tpl'}
  
  <div style='margin:10px 0;padding-top: 10px; border-top: 1px solid #cccccc;'>{include file='blocks/news_months.tpl'}</div>
  
  
  
{elseif $m->code=='public_month'}

  <h4>Архив новостей</h4>
  <div style='margin:10px 0;'>{include file='blocks/news_months.tpl'}</div>
  
  {foreach from=$ar.list item=d}
    {include file='blocks/news_row.tpl'}
  {/foreach}
  
  {if $ar.count<=0}
    <div style='font-size: 120%;'>Список пуст.</div>
  {/if}
    
  {include file='parts/pl.tpl'}
  
  
  
{elseif $m->code=='public_item'}
  {assign var='d' value=$ar.d}
  <h4>{$d.title}</h4>
	<div class="newsdate">{$d.f_date}</div>
	<div class="newscontent">{if $d.text}{$d.text}{else}{$d.ann|nl2br}{/if}</div>
	
    {if $d.link}<noindex><p style='text-align:left;'>Источник: <a href='{$d.link}' target="_blank" rel="nofollow">{$d.link}</a></p></noindex>{/if}
  </div>
  
  <table width="100%" style='margin: 10px 0;'><tr>
    {if $ar.prev}<td width="1%" style='font-size:150%;'>&larr;</td><td width="49%"><A href='{$ar.prev.url}'>{$ar.prev.title}</a><br><small>{$ar.prev.f_date}</td>{/if}
    <td>&nbsp;</td>
    {if $ar.next}<td width="49%" align="right"><A href='{$ar.next.url}'>{$ar.next.title}</a><br><small>{$ar.next.f_date}</td><td width='1%' style='font-size:150%;'>&rarr;</td>{/if}
  </tr></table>
  
  <div style='margin: 30px 0;'>
  <h5>Архив новостей</h5>
  {include file='blocks/news_months.tpl'}
  </div>

{/if}