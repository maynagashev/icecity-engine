
{if $ar.pl.res}
<table cellpadding=5 class='pagelist' border=0><tr>    
  <td class='text'>{if $ar.pl.back==0}Страница {$ar.pl.page} из {$ar.pl.pages}{else}Страницы:{/if}&nbsp;</td>
  {foreach from=$ar.pl.links item=d}
      {if $d.desc=='first'}
        <td class='num'><a href='{$d.url}'>первая</a></td>
      {elseif $d.desc=='prev'}
        <td class='num'><a href='{$d.url}'>&laquo;</a></td>          
      {elseif $d.desc=='do'}
        <td class='num'><a href='{$d.url}'>{$d.num}</a></td>          
      {elseif $d.desc=='current'}
        <td class='num selected'><a href='{$d.url}'>{$d.num}</a></td>          
      {elseif $d.desc=='posle'}
        <td class='num'><a href='{$d.url}'>{$d.num}</a></td>          
      {elseif $d.desc=='next'}
        <td class='num'><a href='{$d.url}'>&raquo;</a></td>          
      {elseif $d.desc=='last'}
        <td class='num'><a href='{$d.url}'>последняя</a></td>          
      {/if}
      
  {/foreach}
</tr></table>
{/if}