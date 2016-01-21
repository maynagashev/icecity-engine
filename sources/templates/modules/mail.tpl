<table width=80% align="center" cellpadding="10" style='margin-top:10px;'><tr><td>

{literal}
<style>
.tabs_active { 
  border-left: 1px solid #cccccc;
  border-top: 1px solid #cccccc;
  border-right: 1px solid #cccccc;
  font-size: 120%;
}
.tabs_not_active { 
  border-bottom: 1px solid #cccccc;
  background-color: #efefef;
  font-size: 120%;
}
.tabs { 
  border-bottom: 1px solid #cccccc;
  
}

</style>
{/literal}

{if $sv->code!=='new'}

<table width=100% cellpadding=10 cellspacing="0" style='margin-bottom:5px;'><tr>  
  <td class=tabs{if $sv->code!='inbox'}_not{/if}_active><a href='{u act=$sv->act code=inbox}'>Входящие</a></td>  
  <td class=tabs{if $sv->code!='sent'}_not{/if}_active><a href='{u act=$sv->act code=sent}'>Исходящие</a></td>  
  <td class=tabs{if $sv->code!='trash'}_not{/if}_active><a href='{u act=$sv->act code=trash}'>Корзина</a></td>  
  <td class=tabs width=90%>&nbsp;</td>

</tr></table>
{/if}



{if $sv->code=='new'}   

<table align=center cellpadding=0 cellspacing=5 width=500 border=0>
  <tr><td>
    <h3>Новое сообщение для <a href='/user/{$ar.recipient.id}/'>{$ar.recipient.login}</a></h3> 
  </td></tr>    
  <tr>
    <td>
    
    {if $ar.s.err_box!=''}<p>{$ar.s.err_box}</p>{/if}
      
    {if !$ar.s.submited || $ar.s.err}
          <table width=100%>          
            <form action="/mailto/{$ar.recipient.id}/" method="POST" enctype="multipart/form-data">
            <input type='hidden' name='new[todo]' value='send'>
            
            {if !$ar.auth}
              <tr><td>
                <small>Ваше имя / логин<br></small>
                <input type='text' size=66 name='new[sender_name]' value='{$ar.s.v.sender_name}'>            
              </td></tr> 
            {/if}
            
            
            <tr><td>
            <small>Заголовок сообщения<br></small>
            <input type='text' size=66 name='new[title]' value='{if $ar.s.v.title}{$ar.s.v.title}{else}{if $ar.answer!==false}Re: {$ar.answer.title}{/if}{/if}'>    
            </td></tr>          
            
            <tr><td>
            <small>Текст сообщения<br></small>
            <textarea cols=70 rows=10 name='new[text]'>{if $ar.s.v.text}{$ar.s.v.text}{else}{if $ar.answer!==false && $ar.quote}[quote]{$ar.answer.text}[/quote]{/if}{/if}</textarea>            
            </td></tr>
            
            {if !$ar.auth}
              <tr><td>
                <small>Ваши контактные данные, куда писать ответ?<br></small>
                <input type='text' size=66 name='new[sender_contacts]' value='{$ar.s.v.sender_contacts}'>
              </td></tr> 
            {/if}
            
            <tr><td style='padding-top: 10px;'><input type='submit' value='Отправить'></td></tr>
            </form>
          </table>
    {/if}
  
    </td>
        
  </tr>
</table>  
{* ======================================================================= *} 
{elseif $sv->code=='inbox'}   
    
  
  <table width=100% cellpadding=5 cellspacing=1>
  <form action='{u act=$sv->act code=$sv->code}' method="POST" enctype="multipart/form-data">
  <input type="hidden" name='new[todo]' value='delete'>
  <tr bgcolor=#efefef>
    <td width=1%>&nbsp;</td>
    <td width=1%><b>#</td>
    <td height=30%><b>Заголовок</td>
    <td width=24% align=center><b>От кого</td>
    <td width=34% align=center><b>Дата</td>
    <td width=1%><input type='button' value='&darr;' id='btn-select-all'></td>    
  </tr>
  {if $ar.size>0}
  {foreach from=$ar.list item=d}
    <tr>
    <td align=center>{if $d.time_read=='0'}<img src='/i/notread.gif' width=16 height=16 border=0>{else}&nbsp;{/if}</td>
    <td align=right>{$d.k}.</td>
    <td align=left>{if $d.time_read=='0'}<b>{/if}<a href='{u act=$sv->act code=view id=$d.id}'>{if $d.title==''}&lt;без заголовка&gt;{else}{$d.title}{/if}</a></td>
    <td align=center>{if $d.sender>0}<a href='/user/{$d.sender}/'>{$d.login}</a>{else}{$d.ip}{/if}</td>
    <td align=center>{$d.f_time}</td>
    <td align=center><input type='checkbox' name='new[selected][{$d.id}]'></td>
    
    </tr>
    
  {/foreach}
  <tr><td colspan=6 align=right>
  <input type='submit' value='Удалить отмеченные'>
  </td></tr>  
  </form>
  {else}
  <tr><td colspan=6 align=center><i>Нет сообщений.</td></tr>
  {/if}
  </table>
  {include file='parts/pl.tpl'}

{* ======================================================================= *}   
{elseif $sv->code=='sent'}   
    
 
  <table width=100% cellpadding=5 cellspacing=1>
  <form action='{u act=$sv->act code=$sv->code}' method="POST" enctype="multipart/form-data">
  <input type="hidden" name='new[todo]' value='delete'>   
  <tr bgcolor=#efefef>
    <td width=1%>&nbsp;</td>
    <td width=1%><b>#</td>
    <td height=30%><b>Заголовок</td>
    <td width=24% align=center><b>Кому</td>
    <td width=34% align=center><b>Дата</td>
    <td width=1%><input type='button' value='&darr;' id='btn-select-all'></td>    
  </tr>
  {if $ar.size>0}
  {foreach from=$ar.list item=d}
    <tr>
    <td align=center>{if $d.time_read=='0'}<img src='/i/notread.gif' width=16 height=16 border=0>{else}&nbsp;{/if}</td>
    <td align=right>{$d.k}.</td>
    <td align=left>{if $d.time_read=='0'}<b>{/if}<a href='{u act=$sv->act code=view id=$d.id}'>{if $d.title==''}&lt;без заголовка&gt;{else}{$d.title}{/if}</a></td>
    <td align=center>{if $d.recipient>0}<a href='/user/{$d.recipient}/'>{$d.login}</a>{else}{$d.ip}{/if}</td>
    <td align=center>{$d.f_time}</td>
    <td align=center><input type='checkbox' name='new[selected][{$d.id}]'></td>
    </tr>
  {/foreach}
  <tr><td colspan=6 align=right>
  <input type='submit' value='Удалить отмеченные'>
  <div style='margin-top: 5px;'><small>
  <span style='color:red;'>Внимание</span>, если удалить отправленное, но еще непрочитанное <br>
  получателем сообщение, то отправка этого сообщения будет 
  отменена,<br> а само сообщение будет удалено, без возможности восстановления.</small></div>
  </td></tr>
  </form>  
  {else}
  <tr><td colspan=6 align=center><i>Нет сообщений.</td></tr>
  {/if}
  </table>
  {include file='parts/pl.tpl'}


{* ======================================================================= *}   
{elseif $sv->code=='trash'}   
    
 
  <table width=100% cellpadding=5 cellspacing=1>
  <form action='{u act=$sv->act code=$sv->code}' method="POST" enctype="multipart/form-data">
  <input type="hidden" id='input_todo' name='new[todo]' value='recover'>   
  <tr bgcolor=#efefef>
    <td width=1%>&nbsp;</td>
    <td width=1%><b>#</td>
    <td height=30%><b>Заголовок</td>
    <td width=1%>&nbsp;</td>    
    <td width=22% align=center><b>Кому | От кого</td>
    <td width=36% align=center><b>Дата</td>
    <td width=1%><input type='button' value='&darr;' id='btn-select-all'></td>    
  </tr>
  {if $ar.size>0}
  {foreach from=$ar.list item=d}
    <tr>
    <td align=center>{if $d.time_read=='0'}<img src='/i/notread.gif' width=16 height=16 border=0>{else}&nbsp;{/if}</td>
    <td align=right>{$d.k}.</td>
    <td align=left>{if $d.time_read=='0'}<b>{/if}<a href='{u act=$sv->act code=view id=$d.id}'>{if $d.title==''}&lt;без заголовка&gt;{else}{$d.title}{/if}</a></td>
    <td align=center><img src='/i/{if $ar.uid==$d.to}icon_inbox.gif{else}icon_sent.gif{/if}' width=16 height=16 border=0></td>
    
    <td align=center>
      {if $ar.uid==$d.recipient}
        {if $d.sender>0}<a href='/user/{$d.sender}/'>{$d.sender_login}</a>
        {else}{$d.ip}{/if}
      {else}
       {if $d.recipient>0}<a href='/user/{$d.recipient}'>{$d.recipient_login}</a>
       {else}{$d.ip}{/if}
      {/if}
      </td>
    <td align=center>{$d.f_time}</td>
    <td align=center><input type='checkbox' name='new[selected][{$d.id}]'></td>
    </tr>
  {/foreach}
  <tr><td colspan=6 align=right>
  <input type='submit' value='Восстановить отмеченные'>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type='button' value='Удалить отмеченные' onCLick="$('#input_todo').val('drop'); this.form.submit();">
  
  </td></tr>
  </form>  
  {else}
  <tr><td colspan=6 align=center><i>Нет сообщений.</td></tr>
  {/if}
  </table>
  {include file='parts/pl.tpl'}

    
{* ======================================================================= *}  
{elseif $sv->code=='view'}            
  
  <center><h3>Сообщение {if $ar.d.sender>0 && $ar.d.recipient>0}
                            {if $ar.d.sender==$ar.uid}для{else}от{/if} <a href='/user/{$ar.u.id}/'>{$ar.u.login}</a>
                        {elseif $ar.d.sender==0}
                          от гостя<br>{$ar.d.name} ({$ar.d.ip})
                        {elseif $ar.d.recipient==0}
                          на ip {$ar.d.ip}
                          
                        {/if}
                        </h3>
  
  
  <table align=center cellpadding=0 cellspacing=5 width=550 border=0>
    
  <tr>
    <td width=90% valign=top style='border-right: 3px solid #dddddd;'> 
      <table width=100%>
        <tr><td>
          <table width=100% cellpadding=10>              
            <tr><td>
           <b>{$ar.d.title}</b>
            
            </td></tr>          
            <tr><td style='padding:10px; border: 1px solid #efefef;'>
            {$ar.d.text|nl2br}
            
            </td></tr>
            <tr><td><small>
            Отправлено: {$ar.d.f_time} <br>
            Прочитано: {$ar.d.read_time}     
    
            </td></tr>
      {if $ar.d.code=='recipient'}      
      <tr><td style='padding: 10px;'><b>
        [ <a href='{$ar.answer_link}'>Ответить</a> ]
          &nbsp;&nbsp;&nbsp;
        [ <a href='{$ar.answer_link_quote}'>Ответить с цитатой</a> ]      
      </b>
      </td></tr>
      {/if}
          </table>          
          
        </td><td>
      </table>
      
    </td>
    <tD valign=top width=10% style='padding-top:10px;'>{$ar.u.img_avatar}
      <table width="100%" align="center" style='margin-top:10px;'>
   
      {if $ar.u.blog_posts>0}
      <tr><tD width=1%><img src='i/icons/main/icon_home.gif' width=16 height=16></td>
          <td nowrap><a href='{u act=blog id=$ar.u.id}' target="_blank">Блог</a> ({$ar.u.blog_posts})</td></tr>    
      {/if}
      
      {if $ar.u.photo_albums>0}
      <tr><tD width=1%><img src='i/icons/main/icon_photo.gif' width=16 height=16></td>
          <td nowrap><a href='{u act=photo code=user id=$ar.u.id}' target="_blank">Галерея</a> ({$ar.u.photo_albums} / {$ar.u.photo_images})</td></tr>       {/if}
      </table>
    
    
    </td>
    
  </tr>
</table>  
  
{else}  
<h3>Ваш почтовый ящик</h3>

<a href='{u act=$sv->act code=inbox}'>Входящие</a> <br><br>
<a href='{u act=$sv->act code=sent}'>Исходящие</a> <br><br>
<a href='{u act=$sv->act code=trash}'>Корзина</a> 

  
  
          
{/if}          
</td></tr></table>