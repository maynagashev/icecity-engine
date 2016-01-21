<h3 style='margin-bottom:0;'>{$sv->modules->current_object->vars.title}</h3>      

<div class="{$m->markitup_type}">
{if $m->code=='edit' && $ar.markitup && !$m->load_markitup}<div style='text-align:right;margin-top: 5px;'>{$ar.markitup->tinymce_button}</div>{/if}
{include file="scaffold/`$m->code`.tpl"}  
</div>


{if $m->code=='edit' && $ar.attach && !$m->load_attaches}
          
  {$ar.markitup->js}
  
  <div style='padding: 0 10px;margin-top: 20px;'>
  <div><b>Прикрепленные файлы</b></div>
  {$ar.attach.iframe}
  </div>

{/if}
