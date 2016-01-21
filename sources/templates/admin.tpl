<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>Админцентр {$sv->vars.site_title} [{$sv->act}] {if $sv->vars.p_title!=''} - {$sv->vars.p_title}{/if}</title>
<link rel="stylesheet" href="/css/admin_style.css" type="text/css">
{foreach from=$sv->vars.styles item=fn}
<link rel="stylesheet" type="text/css" href="/css/{$fn}">
{/foreach}

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<script src="/sources/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="/sources/js/jquery.select.js" type="text/javascript"></script>
<script src="/sources/js/custom.js" type="text/javascript"></script>
<script src="/sources/js/admin.js" type="text/javascript"></script>
{foreach from=$sv->vars.js item=fn}
<script type="text/javascript" language="JavaScript" src="/sources/js/{$fn}"></script>
{/foreach}
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">


<Table width=100% cellpadding="5" cellspacing="0" bgcolor="#efefef" style='border-bottom:1px solid gray;'>
<tr><td>
  <table cellpadding="5"><tr>
  {if $sv->act=='log' || $sv->act=='auth'}
    <td><b>Icecity Engine v{$sv->vars.version} - Авторизация</b></td>  
  {else}
    {foreach from=$sv->menu.admin_main key=k item=d}
      <td>{if $sv->parsed.main_menu_id==$k}<b>{/if}<a href='{u act=$d.act code=$d.code id=$d.id}'>{$d.title}</a></td>
    {/foreach}  
  {/if}
  </tr></table>
</td>

<td align="right">

  <table cellpadding="5"><tr>
  <td style="padding: 0 30px 0 0;">{include file='parts/admin_top.tpl'}</td>
  <td><a href='{$sv->vars.site_url}'>Перейти на сайт</a> &rarr;</td>
  {if $sv->user.session.account_id>0}    
    <td style='padding-left:20px;'><a href='{u act=log code=out}'>Выход</a></td>  
  {/if}
  </tr></table>
  
</td>
</tr>
</Table>

<table width=100% cellpadding="0" cellspacing="0" height="80%"><tr>

<td valign="top" width="98%" style='padding:20px 30px 20px 30px;'>
{if $sv->modules->current_object->name}
  {include file="modules/`$sv->modules->current_object->name`.tpl"}
{else}
  {include file="modules/`$sv->act`.tpl"}
{/if}
</td>
<td valign=top style='padding:2px 7px 0 7px;'>{include file='parts/admin_menu.tpl'}
{if $sv->parsed.admin_sidebar}
  <div style='margin: 20px 0;'>{$sv->parsed.admin_sidebar}</div>
{/if}
</td>
</tr></table>
<div style='text-align: right; padding: 10px 25px 10px 0;'>

<a href='http://www.icecity.ru' target="_blank"><img src='/i/stuff/icecity.gif' width='87' height='27' border='0'></a>
{* <a href='http://www.icecity.ru' target="_blank"><img src='/i/stuff/engine.gif' width='80' height='15' border='0'></a>  *}

</div>

</body>
</html>