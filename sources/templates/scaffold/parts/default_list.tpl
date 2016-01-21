    
      {* список *}
      <form action='{$url}' method="POST" enctype="multipart/form-data">
      <table width=100% cellpadding="4">
      <tr bgcolor="#efefef">
      {literal}
        <th><input type='checkbox' onclick='if (this.checked) { $("input:checkbox").attr("checked", true);} else { $("input:checkbox").attr("checked", false);}'></th>
      {/literal}
        <th>#</th>
        {foreach from=$ar.fields item=f name=a}
       
          <th {if $smarty.foreach.a.iteration>2}align=center{else}align=left{/if}><A href='{su act=$sv->act code=$m->code}&setsort={$f}{$m->slave_url_addon}'>{$ar.m->fields[$f].title}</a>
          {if $ar.m->c_order==$f}<span style='font-size:120%;'>{if $ar.m->c_dir=='asc'}&uarr;{else}&darr;{/if}{/if}
          </th>
        {/foreach}
        <th colspan=2>Действия</th>
      </tr>
      {foreach from=$ar.list item=d}
        <tr>
          <td width="1%" align="center"><input type='checkbox' name='new[selected][]' value='{$d.id}'></td>
          <td width=1% align="right">{$d.i}.</td>
          {foreach from=$ar.fields item=f name=a}     
            <td {if $smarty.foreach.a.iteration>2}align=center{/if}>
            {if $d[$f]!=''}{$d[$f]}{else}<span style='color:red;'>UNDEFINED</span>{/if}</td>
          {/foreach}
         
          <td nowrap><a href='{su act=$sv->act code=edit id=$d.id}{$m->slave_url_addon}'>Редактировать</a></td>
          <td nowrap><a href='{su act=$sv->act code=remove id=$d.id}{$m->slave_url_addon}'>Удалить</a></td>
        </tr>
      {/foreach}
      
      {if $ar.count<=0} 
        <tr><td colspan=3><i>Список пуст.</td></tr>
      {/if}
        <tr><td colspan="3">
          <input type="submit" value="Удалить отмеченные">
        </td></tr>
      </table>
      </form>
      {* конец списка *}