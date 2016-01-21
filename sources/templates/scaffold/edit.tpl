
{if $m->load_markitup}<div style='text-align:right;margin-top: 5px;'>{$ar.markitup->tinymce_button}</div>{/if}

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
<tr><td><i>{if $m->custom_titles.edit}{$m->custom_titles.edit}{else}Редактирование записи{/if}</i> 
| <a href='{su act=$sv->act}{$m->slave_url_addon}'>Вернуться к списку</a>
| <a href='{su act=$sv->act code=$m->code id=$sv->id}{$m->slave_url_addon}'>Обновить без отправки данных</a>
| <a href='{su act=$sv->act code=remove id=$sv->id}{$m->slave_url_addon}'>Удалить</a>
</td></tr>

{if $sv->msgs_count>0}<tr><td>{include file='parts/err_box.tpl'}</td></tr>{/if}

{if $ar.s.err_box}<tr><td>{$ar.s.err_box}</td></tr>{/if}
<tr><td>
<form action="{su act=$sv->act code=$m->code id=$sv->id}&submited=1{$m->slave_url_addon}" method="POST" enctype="multipart/form-data" style='margin:0;padding:0;'>
  {$ar.form}
</form>
</td></tr>

</table>


{if $m->load_markitup}
  {$ar.markitup->js}
{/if}

{if $m->load_attaches}      
  
  <div style='padding: 0 10px;margin-top: 20px;'>
  <div><b>Прикрепленные файлы</b></div>
  {$ar.attach.iframe}
  </div>

{/if}