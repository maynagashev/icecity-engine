<table width=100% cellpadding="5">
<tr><td><i>Загрузка изображений</i></td></tr>

{if $sv->msgs_count>0}<tr><td>{include file='parts/err_box.tpl'}</td></tr>{/if}

{if $ar.s.err_box}<tr><td>{$ar.s.err_box}</td></tr>{/if}

{if !$ar.s.submited || $ar.s.err}
<tr><td>
<form action="{u act=$sv->act code=$sv->code id=$sv->id}" method="POST" enctype="multipart/form-data" style='margin:0;padding:0;'>
{$ar.form}
</form>
</td></tr>
{/if}

</table>