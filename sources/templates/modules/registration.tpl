<div style='padding: 20px 50px;'>
  <h3>Регистрация</h3>
  
  
  {if $ar.s.err_box!=''}
    <div class="err-box">{$ar.s.err_box}</div>
  {/if}
  
  
  
  {if !$ar.s.submited || $ar.s.err}
      
      <div>
      
        <form action='{$sv->view->d.url}/?return_url={$ar.return_url}' method='post'>
        <table class='cells' width="520" border="0" cellpadding="10" cellspacing="1">        
          <tr>  
            <td>Логин <span class="red">*</span><br><small class="gray">используется для входа</small></td>
            <td><input type='text' name='new[login]' size=20 value="{$ar.s.v.login}"></td></tr>

           <tr>
            <td>Пароль <span class="red">*</span><br><small class="gray">не короче 4 символов</small></td>
          	<td><input type='password' name='new[password]' size=20></td></tr>	
          
          <tr>
            <td>Подтверждение пароля <span class="red">*</span><br><small class="gray">еще раз пароль</small></td>
          	<td><input type='password' name='new[password_confirm]' size=20></td></tr>
               	
           <tr>
            <td>E-mail <span class="red">*</span><br><small class="gray">необходим для восстановления пароля</small></td>
          	<td><input type='text' name='new[email]' size=30 value="{$ar.s.v.email}"></td></tr>	
           
          <tr>
            <td colspan="2" align="center">{$ar.captcha.html}</td>
          </tr>          	
          <tr><td align=center colspan=2><b>Все поля являются обязательными для заполнения.</b></td></tr>	
          <tr><td align=center colspan=2><input type='submit' value='Продолжить'></td></tr>	
                    
        </table>	
        </form>	
      </div>
  {else}
    <h3>Поздравляем!</h3>
    {if $m->auth_after_reg}
      <p>Вы успешно зарегистрированы и <b>авторизованы</b> на нашем сайте.</p>
      <META HTTP-EQUIV="Refresh" CONTENT="3; URL={$ar.return_url}">
      <p>Сейчас вы будете <a href='{$ar.return_url}'>перенаправлены</a>.</p>
    {else}
      <p>Вы успешно зарегистрированы на нашем сайте.</p>
      <p><a href='/log/?return_url={$ar.return_url}'>Войти</a></p>
    {/if}
    
    
    </a>  
  {/if}

</div>