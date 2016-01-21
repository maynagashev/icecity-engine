<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head><title>{include file='parts/title.tpl'}</title>
  <meta name="description" content="{if $sv->view->page.description}{$sv->view->page.description}{else}{$sv->view->page.title}{/if}" />
  <meta name="keywords" content="{$sv->view->page.keywords}" />
  
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

  <link rel="stylesheet" type="text/css" href="/style.css">
  <link rel="stylesheet" type="text/css" href="/css/custom.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  {foreach from=$sv->vars.styles item=fn}
    <link rel="stylesheet" type="text/css" href="/css/{$fn}">
  {/foreach}
  
  <script src="/sources/js/jquery-1.4.3.min.js" type="text/javascript"></script>
  {foreach from=$sv->vars.js item=fn}
    <script src="/sources/js/{$fn}" type="text/javascript"></script>
  {/foreach}
  <script defer type="text/javascript" src="/sources/js/pngfix.js"></script>

</head>
<body>

{include file="layouts/`$sv->view->layout_id`.tpl"}

</body>
</html>