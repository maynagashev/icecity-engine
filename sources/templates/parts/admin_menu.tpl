
<table width="200" cellpadding="10" cellspacing="5">
{foreach from=$sv->parsed.admin_menu key=k item=d}
  <tr><td class=admin_menu{if $sv->act==$k}_s{/if}><a href='{u act=$k}'>{$d.title}</a></td></tr>
{/foreach}
{if $sv->parsed.admin_menu_virtuals}<tr><td style="padding:0;"><hr noshade size="1"></td></tr>{/if}
{foreach from=$sv->parsed.admin_menu_virtuals key=k item=d}
  <tr><td class=admin_menu{if $d.selected}_s{/if}><a href='{$d.url}'>{$d.title}</a></td></tr>
{/foreach}
{if $sv->parsed.admin_menu_shortcuts}<tr><td style="padding:0;"><hr noshade size="1"></td></tr>{/if}
{foreach from=$sv->parsed.admin_menu_shortcuts key=title item=url}
  <tr><td class=admin_menu><a href='{$url}'>{$title}</a></td></tr>
{/foreach}
</table>