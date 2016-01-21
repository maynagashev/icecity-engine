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
<tr><td><i>{if $m->custom_titles.remove}{$m->custom_titles.remove}{else}Удаление записи{/if}</i> 
| <a href='{su act=$sv->act}{$m->slave_url_addon}'>Вернуться к списку</a></td></tr>

{if $sv->msgs_count>0}<tr><td>{include file='parts/err_box.tpl'}</td></tr>{/if}

{if $ar.s.err_box || $ar.err_box}<tr><td>{$ar.s.err_box}{$ar.err_box}</td></tr>{/if}

{if !$ar.s.submited || $ar.s.err}
<tr><td>
<form action="{su act=$sv->act code=$m->code id=$sv->id}&submited=1{$m->slave_url_addon}" method="POST" enctype="multipart/form-data" style='margin:0;padding:0;'>
{$ar.form}
</form>
</td></tr>
{/if}

</table>