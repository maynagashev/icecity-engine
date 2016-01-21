{if $m->f_act!='index'}

<div class="bread">
{foreach from=$m->nav item=d name=a}
  
  {if $smarty.foreach.a.iteration>1}<span>&raquo;</span>{/if}
  
  {if $d.url}
    <a href='{$d.url}'>{$d.title}</a>
  {else}
    {$d.title}
  {/if}
  
  
{/foreach}

</div>

{/if}