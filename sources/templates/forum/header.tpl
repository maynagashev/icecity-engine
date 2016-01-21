<div class='textbox'>
  <span>

    {if $m->f_act=='newposts'}<b>{/if}<a href='{$m->forum_url}/?newposts'>Новые сообщения</a></b> &nbsp;|&nbsp;
     
    {if $m->f_act=='active'}<b>{/if}<a href='{$m->forum_url}/?active'>Активные темы</a></b> &nbsp;|&nbsp;
    
    {if $m->f_act=='blank'}<b>{/if}<a href='{$m->forum_url}/?blank'>Сообщения без ответов</a></b> &nbsp;|&nbsp;
    
    {if $sv->cfg.forum_show_users}
      {if $m->f_act=='users'}<b>{/if}<a href='{$m->forum_url}/?users'>Пользователи</a></b> &nbsp;|&nbsp;
    {/if}
    {if $sv->user.session.account_id>0}
      <a href='/profile/'>Настройки учетной записи</a>
    {/if}
    {if $sv->cfg.forum_show_rules}
       &nbsp;|&nbsp; {if $m->f_act=='rules'}<b>{/if}<a href='{$m->forum_url}/?rules'>Правила</a></b>
    {/if}
    {*
    {if $m->f_act!='index'}
       &nbsp;|&nbsp; <a href='{$m->forum_url}/'>Вернуться на главную форума</a>
    {/if}    
    *}
  </span>
</div>