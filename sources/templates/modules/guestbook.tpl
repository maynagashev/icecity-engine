
<div class='guestbook'>
<h3>Гостевая книга</h3>
{if $ar.content}<div style='margin-bottom:20px;'>{$ar.content}</div>{/if}

{if $ar.s.err_box}<div style='margin-bottom: 10px;'>{$ar.s.err_box}</div>{/if}

  {if $ar.pl.pages>1}<div class='pagelist'>{include file='parts/pl.tpl'}</div>{/if}
  
  <Table width='100%' cellpadding=0 cellspacing=0 border=0>
  
  {foreach from=$ar.list item=d}
    <tr><td colspan='3' class='date'>{$d.f_date} <a name='cmntid=374'></a></td></tr>
    <tr>
      <td width='33%' class='name'>{$d.name}</td>
      <td width='33%' class='text' align='center'><b>{if $d.url}<noindex><a href='{$d.url}' target=_blank rel='nofollow'>{$d.f_url}</a></noindex>{/if}</td>
      <td width='33%' class='text' align='right'>{$d.img_email}</td>
      </tr>
    
    <tr><td colspan='3' class='text' style='padding:5px 0 30px 20px;'>{$d.f_text}</td></tr>
  {/foreach}
    
  </table>
  
  {if $ar.pl.pages>1}<div class='pagelist'>{include file='parts/pl.tpl'}</div>{/if}
  
<h4>Отправка сообщения</h4>  
  <div align=center style='border-top: 1px solid #cccccc;padding-top: 15px;'>{$ar.form}</div>
  
</div>