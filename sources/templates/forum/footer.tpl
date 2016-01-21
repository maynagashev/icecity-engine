{assign var='s' value=$m->stats}
<div class="textbox">
{if $m->f_act=='topic'}
  Читают тему (гостей: <b>{$ar.online.guests_count}</b>, пользователей: <b>{$ar.online.users_count}</b>) &nbsp;{$ar.online.users_row}
{else}
  Сейчас на форуме (гостей: <b>{$s.guests_count}</b>, пользователей: <b>{$s.online_users.count}</b>) &nbsp;{$s.online_users.row}
{/if}
</div>

<div class="textbox">
  <table width="100%">
    <tr>
      <td>
        Всего зарегистрированных пользователей: <b>{$s.accounts_count}</b><br>
        Приняло участие в обсуждении: <b>{$s.accounts_with_posts}</b>
      </td>
      <td align="right">
        Всего тем: <b>{$s.topics_count}</b><br>
        Всего сообщений: <b>{$s.posts_count}</b>
      </td>
    </tr>
  </table>
</div>


{if $sv->user.session.account_id>0}

{else}

  <div class="textbox">
    Для того чтобы полноценно участвовать в дискуссиях, необходимо <a href='/registration/'>зарегистрироваться</a> и авторизоваться.<br>
    В данный момент вы <b>не</b> можете создавать новые темы и <b>не</b> можете отвечать в темах.
  </div> 

{/if}