{**
 * Публичный модуль управления подпиской rev.2
 *
 *}
 


<div class='content'>
   <div tyle="padding: 20px 20px 20px 30px; border: 1px solid gray;">
   
    {if $sv->code=='form'}
      {if $ar.invalid}
        <p style='color:red;'>Введен несуществующий email.</p>
      {/if}
    
      <h4 style='margin: 0;'>Проверка адреса подписки:</h4>
      <div style='padding: 15px 0;'>
        <form action="{$ar.url}" method="POST">
          <label>Email:</label>
          <input type='text' name='email' size=40>
          <input type='submit' value='Проверить'>
        </form>
      </div>
      
    {elseif $sv->code=='notaccepted'}  
      {if $ar.s!==false}
        {$ar.s.err_box}
      {else}
        <div><b>Указанный адрес подписки есть в базе, но еще не активирован.</b></div>
        <p>
          <form action='{$ar.url}' method='post' enctype="multipart/form-data">
          <input type='hidden' name='email' value='{$ar.email}'>
          <input type="hidden" name='action' value='activate'>
          {$ar.email} &nbsp; <input type="submit" value="Выслать активационный код заново">
          </form>
        </p>
      {/if}
    
    {elseif $sv->code=='accepted'}
    
     {if $ar.s!==false}
        {$ar.s.err_box}
      {else}
        <div><b>Указанный адрес подписки есть в базе и является активным.</b></div>
        <p>
          <form action='{$ar.url}' method='post' enctype="multipart/form-data">
          <input type='hidden' name='email' value='{$ar.email}'>
          <input type="hidden" name='action' value='remove'>
          {$ar.email} &nbsp; <input type="submit" value="Удалить адрес из базы подписчиков">
          </form>
        </p>
      {/if}
    
    {elseif $sv->code=='notexists'}
      {if $ar.s!==false}
        {$ar.s.err_box}
      {else}
        <div><b>Указанный адрес не найден в базе подписчиков, хотите подписаться?</b></div>
        <p>
          <form action='{$ar.url}' method='post' enctype="multipart/form-data">
          <input type='hidden' name='email' value='{$ar.email}'>
          <input type="hidden" name='action' value='subscribe'>
          {$ar.email} &nbsp; <input type="submit" value="Подписаться на рассылку новостей">          
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input type='button' value="Отмена" onclick="window.location.href='/';">
          </form>
        </p>
      {/if}
    {elseif $sv->code=='msg'}
    
    msg
    
    {/if}
    
    
  </div>
</div>
  
 