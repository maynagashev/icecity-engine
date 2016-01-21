<div class="nb" style='margin: 20px 0; padding: 0 20px;'>

{if $m->code=='public_profile'}

  <h5>Основная информация</h5>
  <ul>
    <li><a href='{$m->root_url}password/'>Изменить пароль</a></li>
    <li><a href='{$m->root_url}avatar/'>Загрузка аватара</a></li>    
  </ul>
  
  {if $ar.s.err_box}<div style='margin: 10px 0;'>{$ar.s.err_box}</div>{/if}
  
  <form action="{$m->root_url}" enctype="multipart/form-data" method="POST">
  {$ar.table}
  
  
  {*
  <table cellpadding="10" style="margin: 10px 0;">
    <tr>
      <td><b>Пользователь</b></td>
      <td><b>{$ar.s.v.login}</b></td>
    </tr>
    <TR>
      <td>Имя</td>
      <td><input type="text" size=40 name='new[name]' value="{$ar.s.v.name}"></td>
    </TR>  
    <TR>
      <td>Отчество</td>
      <td><input type="text" size=40 name='new[fathername]' value="{$ar.s.v.fathername}"></td>
    </TR>         
    <TR>
      <td>Фамилия</td>
      <td><input type="text" size=40 name='new[surname]' value="{$ar.s.v.surname}" maxlength="12"></td>
    </TR>     
    <TR>
      <td>Email</td>
      <td><input type="text" size=40 name='new[email]' value="{$ar.s.v.email}"></td>
    </TR>
    
    <TR>
      <td colspan="2"><input type="submit" value="Применить"></td>
    </TR> 
  </table>
  *}
  </form>
  
{elseif $m->code=='public_password'}

  <h5>Изменение пароля</h5>
  
  <ul>
    <li><a href='{$m->root_url}'>Редактировать основную информацию</a></li>
    <li><a href='{$m->root_url}avatar/'>Загрузка аватара</a></li>
  </ul>
    
  {if $ar.s.err_box}<div style='margin: 10px 0;'>{$ar.s.err_box}</div>{/if}
  
  <form action="{$m->url}" enctype="multipart/form-data" method="POST">
  <table cellpadding="10" style="margin: 10px 0;">
    <TR>
      <td>Введите новый пароль</td>
      <td><input type="password" size=40 name='new[password]' value=""></td>
    </TR>  
    <TR>
      <td>Подтверждение пароля</td>
      <td><input type="password" size=40 name='new[password2]' value=""></td>
    </TR>     
   
    <TR>
      <td colspan="2"><input type="submit" value="Применить"></td>
    </TR> 
  </table>
  </form>  
  
{elseif $m->code=='public_avatar'}

  <h5>Загрузка аватара</h5>
  <ul>    
    <li><a href='{$m->root_url}'>Редактировать основную информацию</a></li>
    <li><a href='{$m->root_url}password/'>Изменить пароль</a></li>
  </ul>
  
  {if $ar.s.err_box}<div style='margin: 10px 0;'>{$ar.s.err_box}</div>{/if}
  
  <form action="{$m->url}" enctype="multipart/form-data" method="POST">
  <table cellpadding="10" style="margin: 10px 0;">    
    <TR>
      <td>Выберите файл</td>
      <td>
        <input name='avatar' type=file>
        <br><span style='color:gray;'>{$ar.ext_row}</span>
        <input type='hidden' name='new[file_submit_avatar]'>

      </td>
    </TR>  
    <tr>
      <td style='vertical-align:top;'>Текущий аватар</td>
      <td>{$ar.d.img_avatar}</td>
    </tr>
    <TR>
      <td></td><td><input type="submit" value="Применить"></td>
    </TR> 
  </table>
  
  </form>  

   
{/if}
</div>