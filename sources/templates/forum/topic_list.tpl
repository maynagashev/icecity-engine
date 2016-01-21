
{include file='parts/pl.tpl'}

<table width="100%" class="ct tl">

<tr class="t-head"><td colspan="6">{$sv->vars.p_title}</td></tr>

<tr class="t-subhead">
  <td class="t-left" width="1%" align="center" >#</td>  
  <td>Название темы</td>
  <td align="center" width="10%">Ответов</td>
  <td align="center" width="10%">Просмотров</td>
  <td class="t-right" width="20%" align="center">Обновление</td>
</tr>

{foreach from=$ar.list item=d name=a}
<tr>
  <td><img src='/i/forum/convert{if $d.pinned}_pinned{elseif $d.is_new}2{else}1{/if}.gif' border="0" width="16" height="16" vspace="3"></td>
  <td>
      <div>{if $d.pinned}<b class='pinned'>Важно:&nbsp;</b>{/if}<a href='{$d.url}'>{$d.title}</a></div>
      {if $d.description}<div class='description'>{$d.description}</div>{/if}
      <div class='author'>Автор: {$d.starter_name}</div>      
    </td>
  <td align="center">{$d.posts}</td>
  <td align="center">{$d.views}</td>
  <td><div><a href='{$d.last_post_url}'>{$d.f_last_post_time}</a></div><div class='last_poster'>{$d.last_poster_name}</div></td>
</tr>
{/foreach}



{if $smarty.foreach.a.total<=0}
  <tr>
    <td colspan='6' style='padding: 10px;'>Не найдено ни одной темы.</td>
  </tr>
{/if}


{if $m->moderators}
<tr>
  <td colspan="6" align="right" style='font-size: 90%;'>
    <b>Модераторы:</b> {$m->moderators_row}.
  </td>
</tr>
{/if}

</table>

{include file='parts/pl.tpl'}