<div>
{foreach from=$ar.months item=months key=year} 
  <div>{$year} год: 
 
  {foreach from=$months item=d key=month}
    {if $ar.year==$year && $ar.month==$month}<b>{/if}<a href='{$m->news_url}month/?id={$month}&year={$year}'>{$d.title}</a>{*<sup>{$d.count}</sup>*}</b>&nbsp;
  {/foreach}
  </div>
{/foreach}
</div>