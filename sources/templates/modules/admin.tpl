{literal}
<style>
.info td {font-size: 100%;}
</style>
{/literal}

<div class='info'>
<table width="100%">
<tr>
  <td width="50%" valign="top">
  
    <table cellpadding="5">
    <th colspan="3" align="left">Недавние авторизации:</th>
    {foreach from=$ar.list item=d}
      <tr {if $d.status==0}bgcolor=#ffcccc{/if}><td>{$d.f_time}</td><tD>{$d.title}</td><td>{$d.ip}</td></tr>
    {/foreach}
    </table>
    {include file='parts/pl.tpl'}

  </td>
  <td width="50%" valign="top">
    <table cellpadding="5">
    <th colspan="3" align="left">Сейчас онлайн:</th>
    {foreach from=$ar.online item=d name=a}
      <tr valign="top">
        <td align="right">{$smarty.foreach.a.iteration}.</td>
        <tD><a href='{u act='sessions' code='edit' id=$d.id}'>{if $d.login}{$d.login}{else}<span style='color:red;'>Guest</span>{/if}</a><br>
        <small>{$sv->m.account->groups[$d.group_id]}</small></td>
        <td>{$d.ip} &nbsp;&nbsp; {$d.f_time}
        <br><small><A href='{$d.lastact}' target="_blank">{$d.lastact}</a></small>
        </td>
        
      </tr>
      <tr><td></td><td colspan="2" style='padding-top:0;'><small>{$d.agent}</small></td></tr>
    {/foreach}
    </table>
  </td>
</tr>

<tr><tD colspan="2" align="center" style='padding-top: 20px;'>

{$ar.selector} 
</td></tr>
</table>
</div>