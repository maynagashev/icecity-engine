<table width="100%" cellpadding="3" bgcolor="#efefef" cellspacing="1" border="0">
  <tr valign="top" bgcolor="white">
    <td width="2%" align="center" class="node">
      <a href='#' id="page-{$d.id}-switcher" class='page-switcher' style='display:{if $d.child_count>0}block{else}none{/if};'>{if $d.expanded}-{else}+{/if}</a>
    </td>
    <td width=48%>  
      <a href='{u act=$sv->act code=edit id=$d.id}'>{if $d.title!=''}{$d.title}{else}undefined{/if}</a> {if $d.child_count>0}({$d.child_count}){/if}
      <span class="backtext">{if $d.classname!='page'}{$d.classname}{/if}</span>                
      
      {if !$d.status}- <span style='color:red;'>отключена</span>{/if}
      
      <br /><small style='color: #999999;'>{$d.url}</small>
    </td>
    <td width="20%" nowrap align="center" class="backtext">{$d.place}</td> 
    <td align="right" width="30%" nowrap>
      <a href='{u act=$sv->act code=create}&parent_id={$d.id}'>Добавить подраздел</a>  &nbsp;&nbsp;&nbsp; 
      <a href='{u act=$sv->act code=remove id=$d.id}'>Удалить</a>
    </td>        
   
  </tr>              
</table>
