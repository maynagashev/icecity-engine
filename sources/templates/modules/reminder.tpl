
<table width="100%" cellpadding="0" cellspacing="0" style="margin: 10px 0;">
  <tr valign="top">
    <td width="60%">
    <h3 class="title">Восстановление пароля</h3>
    {$ar.s.err_box}
    
    {if $ar.todo=='sendemail'}
    
      
      {if !$ar.s.submited || ($ar.s.submited && $ar.s.err)}   
      
          <div style='margin: 10px 20px;'>
          Введите свой логин для входа или email который вы указывали при регистрации, 
          на этот email придет письмо с инструкцией по восстановлению пароля.        
          </div>
          
          <form action="{$m->url}" method="post" enctype="multipart/form-data">
          <div style='margin: 20px 30px; padding: 10px;background-color: #efefef; border: 1px solid #cccccc;'>
            <table>
              <tr>
                <td>Логин: </td>
                <td><input type='text' id='use_login' name='new[login]' value='' size="30"></td>
                <td><input type='radio' value='login' name='new[use]' checked 
                onclick="$('#use_login').attr('disabled', false);$('#use_email').attr('disabled', true);"></td>
              </tr>
              <tr><td colspan="2" align="center">ИЛИ</td></tr>
              <tr>
                <td>Email: </td>
                <td><input type='text' id='use_email' name='new[email]' value='' size="30" disabled></td>
                <td><input type='radio' value='email' name='new[use]' 
                onclick="$('#use_login').attr('disabled', true);$('#use_email').attr('disabled', false);"></td>
              </tr>
              <tr><td></td><td style='padding: 10px 0 0 0;'><input type="submit" value="Далее"></td></tr>
            </table>
          </div>    
          </form>
          
      {/if}
    
    {else}
   
      {if $ar.s.show_form} 
        <div style='margin: 10px 20px;'>
        Введите новый пароль для учетной записи <b>{$ar.s.user.login}</b>:        
        </div>
           
        <form action="{$sv->m.restore->activate_url}{$ar.s.key}" method="post" enctype="multipart/form-data">
        <div style='margin: 20px 30px; padding: 10px;background-color: #efefef; border: 1px solid #cccccc;'>
          <table>
            <tr>
              <td>Новый пароль: </td>
              <td><input type='password' name='new[pass]' value='' size="30"></td>              
            </tr>
            <tr>
              <td>Подтверждение пароля: </td>
              <td><input type='password' name='new[pass2]' value='' size="30"></td>              
            </tr>          
            <tr><td></td><td style='padding: 10px 0 0 0;'><input type="submit" value="Далее"></td></tr>
          </table>
        </div>    
        </form>
      {/if}
        
    {/if}
    
    </td>
   
  </tr>
</table>