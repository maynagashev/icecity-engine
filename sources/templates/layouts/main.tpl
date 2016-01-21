{* layout_title="Главная страница" *}

<table class="content-tbl" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td class="maincontent-td">
		{include file="modules/`$sv->act`.tpl"}
	</td>
    <td width="20"></td>
    <td class="infoblock-td">
		#dblock(last_news)#
	</td>
</tr>
</table>