<h3>{$ar.breadcrumb}</h3>



{if $m->code=='public_index'}
  
  {$ar.catlist}
  
  {$ar.content}
  
  

{elseif $m->code=='public_cat'}


{if $ar.subcats.count>0}<h4>Подразделы</h4>{/if}
<div style='font-size: 120%;padding: 0px 0 20px 0;'>

<ol>
{foreach from=$ar.subcats.list item=d}
  <li><a href='{$d.url}'>{$d.title}</a></li>
{/foreach}
</ol>
</div>

{if $ar.count>0}
  <h4>Хиты продаж в разделе:</h4>
{/if}
  <div class="item_table">
    {html_table loop=$ar.td cols='3' table_attr=''}
  </div> 
  
  <div style='padding: 10px 0;'>{if $sv->view->page.id<>11}{$ar.content}{/if}</div>

{elseif $m->code=='public_subcat'}



  <div class="item_table">
    {html_table loop=$ar.td cols='3' table_attr=''}
  </div> 
  {if $ar.count<=0}
    <p>Раздел в стадии наполнения...</p>
  {/if}
  
  {if $sv->view->page.id<>11}{$ar.content}{/if}

  
  
{elseif $m->code=='public_details'}

    {assign var='d' value=$ar.d}
      <h1>{$d.title}</h1>
      
      <p>{$d.img_preview}</p>
      {$d.text|nl2br}
      {if $d.price}<p><b>Стоимость: <span  style='color:yellow;'>{$d.f_price}</span> руб.</b></p>{/if}
      
  

{/if}

