{include file='parts/pl.tpl'}

<table width="100%" class="ct">
<tr class="t-head">
  <td colspan="4">Пользователи</td>
</tr>
<tr class="t-subhead">
  <td class="t-left">Имя</td>
  <td>Сообщений</td>
  <td>Регистрация</td>
  <td class="t-right">Последний визит</td>
</tr>
{foreach from=$ar.list item=d}
  <tr>
    <td>
      <a href='{$m->forum_url}/?user={$d.id}'>{$d.login}</a>{if $d.img_avatar}<div style='margin-top: 5px;'>{$d.img_avatar}</div>{/if}
    </td>
    <td>{if $d.fposts>0}<a href='{$m->forum_url}/?userposts={$d.id}'>{$d.fposts}</a>{else}{$d.fposts}{/if}</td>
    <td>{$d.f_time_reg}</td>
    <td>{$d.f_last_time}</td>
  </tr>
{/foreach}
</table>

{include file='parts/pl.tpl'}