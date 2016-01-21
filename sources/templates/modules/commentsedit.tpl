<h3 style='margin-bottom:0;'>Редактор комментариев</h3>     
{if $m->code=='moderation'}

{$ar.s.err_box} 
{if $sv->msgs_count>0}<div>{include file='parts/err_box.tpl'}</div>{/if}

<table width="100%" cellpadding="5" cellspacing="1" bgcolor="#cccccc" style="margin: 10px 0;">
<form action="{u act=$sv->act code=$sv->code}" method="POST">
<tr><td colspan="2">
  <b>Список непроверенных комментариев</b>
</td></tr>
{foreach from=$ar.list item=d}
<tr bgcolor="White">

  <td>
    <table width="100%">
    <tr><td>№ <b>{$d.id}</b> | дата: <b>{$d.f_date}</b> | Кто: <b>{$d.username}</b> | ip: {$d.ip} |  Email: <b>{$d.email}</b> | WWW: <b>{$d.www_cut}</b> | Trust: {$d.trust}    </td></tr>
    <tr><td bgcolor="#efefef" style='padding: 20px;'>{$d.text}</td></tr>
    <tr><td></td></tr>
    
    </table>
  </td>
  <td bgcolor="#efefef" width="20%" nowrap style='padding-left: 15px;'>
  {if !$d.trust}<input type='radio' name='new[items][{$d.id}][approve]' value="1" checked> опубликовать и <b>добавить</b> почту <b>в траст</b><br>{/if}
    <input type='radio' name='new[items][{$d.id}][approve]' value="3"> опубликовать<br>
    <input type='radio' name='new[items][{$d.id}][approve]' value="0"> пропустить<br>
    <input type='radio' name='new[items][{$d.id}][approve]' value="2"> удалить<br><br>
    <a href='{u act=$sv->act code='edit' id=$d.id}' target='_blank'>Редактировать</a>
  </td>  
</tr>
{/foreach}
<tr><td colspan="2" align="center">
  <input type="submit" value="Применить" name='new[submit]'>
</td></tr>
</table>
</form>

{else}
  {include file="scaffold/`$m->code`.tpl"}
{/if}  
  
  
