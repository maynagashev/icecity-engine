{assign var='c' value=$ar.comments}

<h4>Комментарии {if $d.replycount>0}({$d.replycount}){/if}</h4><a name='comments'></a>


{literal}
<script language="JavaScript">
  function comment_quote(id, login) {
    var text = $('#comment_text_'+id).text();
    $('#comment_text').val($('#comment_text').val()+'[quote='+login+']'+text+'[/quote]');
  }
</script>

<style>
  .t-comments {width: 100%; margin-bottom: 40px;}
  .t-comments td {padding: 5px 10px;}
  .t-comments .comment-header { background-color: #efefef; }
  .t-comments .comment-header table { width: 100%; }
  .t-comments .comment-header table td { padding: 0;  }
  .t-comments .comment-menu {margin: 5px 0; text-align:right; padding-right: 20px;color: #999999; font-size: 11px;}
  .t-comments .comment-menu a {margin-left: 20px;}
  .t-comments .not-approved td {background-color: #efefef;}
  .comment_text {padding:  10px;}
</style>
{/literal}


<table class='t-comments'>
  
  {if $c.count>0}
    {foreach from=$c.list item=d}
      <tr class='comment-header{if !$d.approved} not-approved{/if}'>
        <td colspan='2'>
          <table>
            <tr>
              <td><b>{if $d.www_full}<a href='{$d.www_full}' target="_blank">{$d.username}</a>{else}{$d.username}{/if}</b>
              {if !$d.approved}<b style='color:red; margin-left: 20px; font-size: 12px;'>Еще не проверено администратором.</b>{/if}
              </td>
              <td align="right"><a name='comment-{$d.id}'></a>{$d.f_time}</td>
            </tr>
          </table>
        </td>       
      </tr>
      <tr valign="top" class='comment-body{if !$d.approved} not-approved{/if}'>     
        <td width="105" align="center">{$d.img_avatar}</td>
        <td>
          <div class='comment_text' id='comment_text_{$d.id}'>{$d.f_text}</div>
          
          <div class='comment-menu'>            
            <a href='#' onclick="$('#comment_parent_id').val('{$d.id}'); $('#comment_text').focus(); return false;">Ответить</a>             
            <a href='#' onclick="$('#comment_parent_id').val('{$d.id}'); comment_quote('{$d.id}', '{$d.login}'); $('#comment_text').focus(); return false;">Цитировать</a>            
            <a href='{$d.c_url}' title='Прямая ссылка на этот комментарий'>Ссылка на комментарий</a>
          </div>
        </td>      
      </tr>
    {/foreach}
  {else}
    <tr><td>Комментариев пока нет.</td></tr>
  {/if}

</table>


{* ============= сообщения ================= *}
{if $c.s.err_box}<div style='margin: 10px 0;'>{$c.s.err_box}</div>{/if}
{if $sv->msgs_count>0}<div style='margin: 10px 0;'>{include file='parts/err_box.tpl'}</div>{/if}
{* ============= список ================= *}


{* ============= форма ================= *}
 
{if $sv->user.session.account_id>0 || $c.mode=='guest' || $c.mode=='both'}

  <form action="{$c.action_url}#comments" method="POST" enctype="multipart/form-data">
  <table width="100%" style="margin: 20px 0;" cellpadding="10">
  
  {if $c.mode=='guest'}
    <tr><td width="70">Имя *</td><td><input type='text' name='new[username]' size=50 value='{$c.s.v.username}'></td></tr>
    <tr><td>Email *</td><td><input type='text' name='new[email]' size=50 value='{$c.s.v.email}'> <small style='color:#999999;'>- не будет опубликован</small></td></tr>
    <tr><td>Сайт </td><td><input type='text' name='new[www]' size=50 value='{$c.s.v.www}'></td></tr>
  {/if}
  <tr><td colspan="2" style='padding-top: 10px;'>{$c.markitup} <input type='hidden' name='new[parent_id]' id='comment_parent_id' size="6" value="0"></td></tr>
  <tr><td colspan="2" class="bbcode"><textarea name='new[text]' cols="80" rows="8" id='comment_text'>{$c.s.v.text}</textarea></td></tr>
      
  <tr><td colspan="2"><input type='submit' value="Добавить комментарий"></td></tr>
  </table>
  </form>
  



{else}

  <div style='margin: 10px 0;border: 1px solid #999999;text-align:center;padding: 10px;'>
    Для написания комментариев необходимо <a href='/log/?return_url={$c.action_url}'>войти</a> или <a href='/registration/?return_url={$c.action_url}'>зарегистрироваться</a>.
  </div>

{/if}
