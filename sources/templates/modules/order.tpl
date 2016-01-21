{if $m->code=='public_history'}
  <h3>История заказов</h3>

{elseif $m->code='public_view'}
  <h3>Информация о заказе</h3>

{elseif $m->code='public_purchase'}
  <h3>Оплата заказа</h3>

{/if}