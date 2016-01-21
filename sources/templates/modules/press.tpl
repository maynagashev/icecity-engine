<h3 style='margin-bottom:0;'>Пресс-релизы и публикации в СМИ</h3>  
<div class="html">
{include file="scaffold/`$m->code`.tpl"}
</div>

{if $m->code=='edit'}
          
  {$ar.markitup}
  
  <div style='padding: 0 10px;margin-top: 20px;'>
  <div><b>Прикрепленные файлы</b></div>
  {$ar.attach.iframe}
  </div>

{/if}
