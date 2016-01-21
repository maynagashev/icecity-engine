<div style='padding: 20px 10px;'>
<table cellpadding="10" width="100%">
  <tr valign="top">
{if $sv->user.session.account_id>0}
  <td>
    <div style='border: 1px solid #dddddd;font-size: 120%; text-align:center;padding: 30px; background-color: #efefef;'>
      Вы уже авторизованы как: <b>{$sv->user.session.login}</b>.
      <br><br>
      <a href='/log/?action=out'>Выйти</a>
      
    </div>    
  </td>  
{else}
  
    <td width="50%" style='border-right: 1px solid #dddddd;'>
      
      <table width="100%">
        <tr><td>
          

<p>Недавно на {$sv->cfg.site_title}? <a href='/registration/?return_url={$ar.return_url}'>Зарегистрируйтесь</a> и станьте полноправным участником сайта. Это займет всего лишь минуту.</p>

<p>Зарегистрированные пользователи могут:</p>

    <li>комментировать материалы;</li> 
    <li>учавствовать в обсуждениях на форуме.</li>

<p><a href='/about/'>О сайте {$sv->cfg.site_title}</a></p>

        
        </td></tr>      
      </table>
    
    </td>
    <td width="50%" bgcolor="#efefef">
    
    
    	<table width=200 cellspacing="15" cellpadding="0" border="0" style='margin-top:2px;'>
           <form action='/log/?action=in&return_url={$ar.return_url}' method=post>
							<tr>
							   <td align=right>Логин: </td>
							   <td><input size=12 type=text name=login></td>
							</tr>
							<tr>
							   <td align="right">Пароль:</td>
							   <td><input size=15 name=password type=password></td>							    
							</tr>
						  <tr><td colspan="2" align="center">
							 <input type="checkbox" name="foreign_pc" value="1"> чужой компьютер
							</td></tr>
							<tr>
							<td colspan="2" align="center"><input type="submit" value="Войти"></td>							    
							</tr>
		      </form>		
  	   </table>
          
    </td>
    
{/if}

  </tr>
</table>
</div>