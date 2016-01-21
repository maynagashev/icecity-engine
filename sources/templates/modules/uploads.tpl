{if $m->code=='default'}
<div align=right style='margin-right:10px;'>
  <a href="{su act=$m->act id=$m->id}">Обновить фрейм</a>
</div>
{/if}

{if $m->code=='default'}

  <table width=100% cellpadding="5">  
  {if $ar.submenu}<tr><td>{$ar.submenu}</td></tr>{/if}
  
  <tr><td><a href='{su act=$sv->act code=create}'>{if $m->custom_titles.create}{$m->custom_titles.create}{else}Создать{/if}</a></td></tr>
  <tr><tD>{include file='parts/pl.tpl'}</td></tr>
  <tr><td>
  
  <table width=100% cellpadding="4">
 
  {foreach from=$ar.list item=d}
    <tr bgcolor="#{if $d.active}ffffff{else}ffcccc{/if}">
     
     
      <td valign="top" width=90% style='padding-left:20px;'>
        <table width="100%" cellpadding="5">
          <tr><td width="10%">Файл:</td><td>{$d.filename}</td></tr>
          <tr><td>Название:</td><td>{if $d.title==''}-{else}{$d.title}{/if}</td></tr>
          <tr><td nowrap>Копирайт:</td><td>{if $d.author==''}-{else}{$d.author}{/if}</td></tr>
          <Tr><td>Текст:</td><td valign="top">{if $d.text==''}-{else}{$d.text}{/if}</td></Tr>
        </table>
      
      </td>
      <td valign=top nowrap style='padding:3px 10px 10px 10px;'><a href='{su act=$sv->act code=edit id=$d.id}'>Редактировать</a><Br><br><a href='{su act=$sv->act code=remove id=$d.id}'>Удалить</a></td>
    </tr>
  {/foreach}
  
  {if $ar.count<=0} 
    <tr><td colspan=3><i>Список пуст.</td></tr>
  {/if}
  
  </table> 
  </td></tr>
  <tr><tD>{include file='parts/pl.tpl'}</td></tr>
  </table>


{else}
  {include file="scaffold/`$m->code`.tpl"}
{/if}