
{assign var='d' value=$ar.d}
  <table cellpadding="5" class="ct" width="100%" style='margin-bottom:0;'>
    <tr class="t-head"><td>Профиль</td></tr>
  </table>
 
 <table class="pt{if $d.first} pt-first{/if}" width="100%">

  <tr>
    <td class="p-cell-user">
    
      <div class="p-user-info">
          <div class="p-user-name">{$d.login}</div>
          {if $d.img_avatar!=''}
            <div class="p-user-avatar">{$d.img_avatar}</div>
          {/if}
       
          <div class="p-user-status">{$d.f_group}</div>
          <div class="p-user-stats">
            <span>Сообщений: <span>{if $d.fposts>0}<a href='{$m->forum_url}/?userposts={$d.id}&mode=all'>{/if}{$d.fposts}</a></span></span>
            {if $d.f_time_reg!='-'}<span>Регистрация: <br><span>{$d.f_time_reg}</span></span>{/if}
            <span>Последний визит: <br><span>{$d.f_last_time}</span></span>   
          </div>
      </div>
      
    </td>
    <td class="p-cell-content" style='padding: 10px 20px;'> 
    
      <table class="it">
      {if $ar.table}{$ar.table}{else}<tr><td>Нет информации.</td></tr>{/if}
      </table>
      <div align="right">
      {if $d.topics_count>0}<a href="{$m->forum_url}/?userposts={$d.id}&mode=topics">Темы</a> &nbsp;{/if}
      {if $d.fposts>0}<a href="{$m->forum_url}/?userposts={$d.id}&mode=all">Сообщения</a> &nbsp;{/if}
        {if $d.last_fpost_url}<a href="{$d.last_fpost_url}">Последнее сообщение</a>{/if}
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="p-cell-footer">&nbsp;</td>
  </tr>
</table>