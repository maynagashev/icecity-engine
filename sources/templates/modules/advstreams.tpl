<h3 style='margin-bottom:0;'>Рекламные места</h3>     
{if $m->code=='default'} 



<table width=100% cellpadding="5">

{if $ar.submenu}<tr><td>{$ar.submenu}</td></tr>{/if}

<tr><td><a href='{su act=$sv->act code=create}'>{if $m->custom_titles.create}{$m->custom_titles.create}{else}Создать{/if}</a></td></tr>
<tr><tD>{include file='parts/pl.tpl'}</td></tr>
<tr><td>

  <table width=100% cellpadding="4">
  <tr bgcolor="#efefef">
    <th>Вкл.</th>
    <th>#</th>
    {foreach from=$ar.fields item=f}
   
      <th><A href='{su act=$sv->act code=$m->code}&setsort={$f}'>{$ar.m->fields[$f].title}</a>
      {if $ar.m->c_order==$f}<span style='font-size:120%;'>{if $ar.m->c_dir=='asc'}&uarr;{else}&darr;{/if}{/if}
      </th>
    {/foreach}
    <th colspan=2>Действия</th>
  </tr>
  

<form action="{u act=$sv->act code=$m->code}" method="post" enctype="multipart/form-data">
  
  {foreach from=$ar.list item=d}
    <tr bgcolor={if !$d.active}#ffcccc{elseif $d.active && $d.count>0}#ccffcc{/if}>
      <td width="1%"><input type='checkbox' name='new[items][{$d.id}][active]' {if $d.active}checked{/if}></td>
      <td width=1% align="right">{$d.i}.</td>
      {foreach from=$ar.fields item=f name=a}     
        <td {if $smarty.foreach.a.iteration>1}align=center{/if}>
        {if $f=='sort'}
          <input type='text' name='new[items][{$d.id}][sort]' value='{$d.sort}' size=3 style='text-align:center;'>
        {else}
          {if $d[$f]!=''}{$d[$f]}{else}<span style='color:red;'>UNDEFINED</span>{/if}
        {/if}  
        </td>
      {/foreach}
     
      <td nowrap><a href='{su act=$sv->act code=edit id=$d.id}'>Редактировать</a></td>
      <td nowrap><a href='{su act=$sv->act code=remove id=$d.id}'>Удалить</a></td>
    </tr>
  {/foreach}
  
  {if $ar.count<=0} 
    <tr><td colspan=3><i>Список пуст.</td></tr>
  {/if}
  
  
  </table> 
</td></tr>
<tr><tD>{include file='parts/pl.tpl'}</td></tr>

<tr bgcolor="#dddddd">
  <td>
    <input type="submit" value="Применить изменения">
  </td>
</tr>
</table>
</form>





{else}
{include file="scaffold/`$m->code`.tpl"}
{/if}
