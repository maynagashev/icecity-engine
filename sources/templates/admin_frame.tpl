<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>Админцентр {if $sv->vars.p_title!=''} - {$sv->vars.p_title}{/if}</title>
<link rel="stylesheet" href="/css/admin_style.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<script src="/sources/js/layout.js" type="text/javascript"></script>
<script src="/sources/js/jquery.min.js" type="text/javascript"></script>
<script src="/sources/js/custom.js" type="text/javascript"></script>
<script src="/sources/js/admin.js" type="text/javascript"></script>
{foreach from=$sv->vars.js item=fn}
<script type="text/javascript" language="JavaScript" src="/sources/js/{$fn}"></script>
{/foreach}
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">

{include file="modules/`$sv->act`.tpl"}

</body>
</html>