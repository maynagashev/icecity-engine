<div class="topcontentpage">{$ar.content}</div>

<div class="contentbox">

{foreach from=$ar.pinned.list item=d}
  {include file='blocks/news_row.tpl'}  
{/foreach}
{foreach from=$ar.news.list item=d}
  {include file='blocks/news_row.tpl'}
{/foreach}

{if $ar.pinned.count<=0 && $ar.news.count<=0}
	
{/if}


{include file='blocks/news_months.tpl'}

</div> 