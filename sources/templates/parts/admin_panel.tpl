<table width="100%" bgcolor="white" cellpadding="5">
  <tr><td id="admin-panel">
    <a href="{u act='admin'}">Админцентр</a>
    <a href="{u act='maccount'}">Пользователи</a>
    {if $sv->user.sadmin}<a href="{u act='mmail'}">Переписка</a>{/if}
    <a href="{u act='mfile'}">Файлы</a>
    <a href="{u act='mfile' code='active'}">Активные закачки</a>
    <a href="{u act='scans'}">Сканы</a>
    {if $sv->user.sadmin}<a href="{u act='scans' code='ps'}">Процессы</a>{/if}
    <a href="{u act='config'}">Настройки</a>
  </td></tr>

</table>