<a name='auth'></a>
{if $sv->user.session.account_id>0}
        

  <table width="100%" cellSpacing=1 cellPadding=0 border=0 style="">    
	
    <tr>
		<td align=center style='padding:5px;'>
	    <small>Вы вошли как <b>{$sv->user.session.login}</b>{* ({$sv->user.session.title}). *}
	    [ <a href="/log/?action=out" >Выход</a> ]</small>
	    </td></tr>
	   
	</table>

	
           
{else}      
  	<table width=200 cellspacing="5" cellpadding="0" border="0">
  	<tr><th colspan="2">Вход</th></tr>
  	</table>
  	<table width=200 cellspacing="15" cellpadding="0" border="0" style='margin-top:2px;'>
           <form action='{u act='log' code='in'}' method=post>
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
	  <table cellspacing="5" cellpadding="0" border="0" style='margin-top:10px;border-top: 1px solid #efefef;'>
	   <tr><td style='padding-top:5px;'>
	   {*
	     <li type="square" style='margin-bottom:4px;'><a href='{u act=registration}'>Регистрация</a></li>
	     <li type="square"><a href='{u act=pswrecovery}'>Восстановление пароля</a></li>  	   
	     
	   *}
	   </td></tr>
	  
	  </table>
	
{/if}