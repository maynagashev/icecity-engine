<h3 style='margin-bottom:0;'>Редактор новостей</h3>  
<div class="html">

{if $m->code=='edit'}<div style='text-align:right;margin-top: 5px;'>{$ar.markitup->tinymce_button}</div>{/if}

{include file="scaffold/`$m->code`.tpl"}
</div>

{if $m->code=='edit'}
          
  {$ar.markitup->js}
  
  <div style='padding: 0 10px;margin-top: 20px;'>
  <div><b>Прикрепленные файлы</b></div>
  {$ar.attach.iframe}
  </div>

{/if}
