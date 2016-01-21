<a name='post{$d.id}'></a>
<table class="pt{if $d.first} pt-first{/if}" width="100%">

  <tr>
    <td class="p-cell-user">      
      <div class="p-user-info">
          <div class="p-user-name"><a href='{$m->forum_url}/?user={$d.author_id}'>{$d.author_name}</a></div>
          {if $d.user.img_avatar!=''}
            <div class="p-user-avatar">{$d.user.img_avatar}</div>
          {/if}
          {if $d.user.group_id<>1}
            <div class="p-user-status">{$d.user.f_group}</div>
          {/if}
          <div class="p-user-stats">
          {if $m->f_act!='userposts'}<span>Сообщений: <span><a href='{$m->forum_url}/?userposts={$d.author_id}&mode=all'>{$d.user.fposts}</a></span></span>{/if}
            {if $d.user.f_time_reg!='-'}<span>Регистрация: <br><span>{$d.user.f_time_reg}</span></span>{/if}      
          </div>
      </div>
      
    </td>
    <td class="p-cell-content"> 
    
      <div class='p-date'>
        {$d.f_date}
        {if $d.url}<div style='display: inline;float:right;'>
          <a rel="nofollow" title="Ccылка на это сообщение" 
              onclick="prompt('Скопируйте в буфер обмена адрес ссылки на это сообщение', this.href); return false;" href="{$d.url}">#{$d.id}</a></div>
        {/if}
      </div>
      <div class="p-text">
        {$d.f_text}
      </div>
      
      
    </td>
  </tr>
  <tr>
    <td class="p-cell-contacts">
      &nbsp;
    </td>
    <td class="p-cell-actions">    
    
    {if $m->f_act=='userposts'}
    
      <div align="right"><A href='{$d.url}'>Перейти</A></div>
    
    {elseif $m->f_act=='answer'}
    
    {else}
    
        <div style='float:right;'>
            
            {if $d.can_edit}
              <input type="button" value="Редактировать" onclick="window.location.href='{$d.url_edit}';">
            {/if}
            {if 2==1 && $d.can_delete}
              <input type="button" value="Удалить" onclick="window.location.href='{$d.url_delete}';">
            {/if}
            &nbsp; &nbsp; &nbsp;
            {if $d.can_answer}
              <input type="button" value="Цитировать" onclick="window.location.href='{$d.url_quote}';">
              <input type="button" value="Ответить" onclick="window.location.href='{$d.url_answer}';">    
            {/if}
        </div>
      {/if}
      
      
    </td>
  </tr>

</table>

