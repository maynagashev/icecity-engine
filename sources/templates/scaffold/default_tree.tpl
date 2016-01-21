<table width=100% cellpadding="5">

{if $m->slave_mode}
  <tr><tD>
    Родительский объект:
    {foreach from=$m->master_info item=html}
      {$html}
    {/foreach}
  </tr></tr>
{/if}

{if $ar.submenu}<tr><td>{$ar.submenu}</td></tr>{/if}

<tr><td><a href='{su act=$sv->act code=create}{$m->slave_url_addon}'>{if $m->custom_titles.create}{$m->custom_titles.create}{else}Создать{/if}</a></td></tr>

{if $sv->msgs_count>0}<tr><td>{include file='parts/err_box.tpl'}</td></tr>{/if}

{if $ar.err_box || $ar.s.err_box}<tr><td>{$ar.err_box}{$ar.s.err_box}</td></tr>{/if}

<tr><tD style='padding-bottom: 0;'>{include file='parts/pl.tpl'}</td></tr>
<tr><td style='padding-top: 0;'>

  {* Поиск и селекторы *}
  <table width="100%" cellpadding="0" cellspacing="0" style='margin-bottom: 10px;'>
    {if $m->design_selectors_bottom}
    <tr valign="bottom"><td bgcolor="#efefef" style=''>{$ar.search}</td></tr>
    <tr valign="bottom"><td>{$ar.selectors}</td></tr>    
    {else}
    <tr valign="bottom">
      <td width="50%">{$ar.search}</td>
      <td width="50%" align="right">{$ar.selectors}</td>
    </tr>
    {/if}
  </table>
  
  {if $ar.layout=='list'}
    
    {* список *}
    {include file='scaffold/parts/default_list.tpl'}
  
  {else}
  
    {* дерево *}
    {include file='scaffold/parts/default_tree.tpl'}
      
      
  {/if}   
  
</td></tr>

<tr><tD>  
  <table width="100%"><tr> 
    <td>{include file='parts/pl.tpl'}</td>
    <td align="right" style=''>Кол-во элементов в списке: 
    {if $ar.layout=='list'}
      <b>{if $ar.pl.size}{$ar.pl.size}{else}0{/if}</b>{if $ar.all_count && $ar.pl.size<>$ar.all_count} из {/if}   
    {/if}
    <b>{$ar.all_count}</b>.</td>
  </table>
  </td></tr>
  
</table>