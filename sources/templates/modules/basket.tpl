{if $sv->msgs_count>0}<div>{include file='parts/err_box.tpl'}</div>{/if}

{literal}
<style>
  .basket td {padding: 5px 10px;}
  .order td {padding: 5px 10px;}
</style>
{/literal}


{if $m->code=='public_order'}

{if !$ar.s.no_form}
  <h1>Оформление заказа (шаг 2 из 2)</h1>
  <form action="/shop/basket/order/" method="POST" enctype="multipart/form-data">
  <table width="100%" class="order">
    <tr>
      <td>Ваше имя? *</td>
      <td><input type='text' name='new[username]' size=30 value='{$ar.s.order.v.username}'></td>
    </tr>
    <tr>
      <td>Ваш контактный телефон *</td>
      <td><input type='text' name='new[phone]' size=30 value='{$ar.s.order.v.phone}'></td>
    </tr>
    <tr>
      <td>Ваш контактный email *</td>
      <td><input type='text' name='new[email]' size=30 value='{$ar.s.order.v.email}'></td>
    </tr>
        
    <tr valign="top">
      <td>Сообщение для менеджера: <br>(необязательно)</td>
      <td><textarea name='new[text]' cols=30 rows="5">{$ar.s.order.v.text}</textarea></td>
    </tr>
    <tr>
      <td>&larr; <a href='/shop/basket/'>Назад в корзину</a></td>
      <td><input type='submit' value='Отправить заказ'></td>
    </tr>
  </table>  
{/if}
  
  
    
  </form>
  
  
{else}

<H1>Корзина с товарами</H1>
<form action="/shop/basket/" method="POST" enctype="multipart/form-data">
<table width="100%" class='basket' style='margin: 10px 0; '>
<tr bgcolor="#2F5D09"><td width="60%">Название товара</td><td align="center">Количество</td><td align="center">Стоимость</td><td align="center">Сумма</tr>
{foreach from=$ar.basket item='d' name=a}  
  <tr><td><a href='{$d.product.url}'>{$d.product.title}</a></td>
      <td align="center"><input type='text' size='2' name='new[count][{$d.id}]' value='{$d.count}' style='text-align:center;'> шт.</td>
      <td align="right"><b style='color:yellow;'>{$d.product.f_price}</b>&nbsp;руб.</td>
      <td align="right"><b style='color:yellow;'>{$d.f_sum}</b>&nbsp;руб.</td>
  </tr>
{/foreach}
{if $smarty.foreach.a.total==0}
  <tr><td colspan=3 align="center">Список пуст.</td></tr>
{/if}
<tr bgcolor='#47791B'><td>ИТОГО:</td> 
    <td colspan="2"></td>    
    <td align="right"><b style='color:yellow;'>{$sv->m.basket->last_f_sum}</b>&nbsp;руб.</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr><td><input type='button' onclick="if(confirm('Действительно удалить все содержимое корзины?')) window.location.href='/shop/basket/empty/';" value="Удалить все"></td>
    <td><input type='submit' value="Пересчитать"></td>
    <td></td>
    <td><input type='button' onclick="window.location.href='/shop/basket/order/';" value="Заказать!"></td>
</tr>
</table>
</form>

{/if}