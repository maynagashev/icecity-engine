<style>
{literal}
.page-switcher { text-decoration: none; font-size: 140%; }
.page-childs { padding: 5px 0 10px 30px; }
{/literal}
</style>  


{if $sv->code=='default'}


<h3 >Список страниц сайта</h3>    

{if $sv->msgs_count>0 && $sv->code!='makeroot'}<div style='margin:10px 0 10px 0;'>{include file='parts/err_box.tpl'}</div>{/if}

{if $ar.root.d}   

  {assign var='d' value=$ar.root.d}
  {include file='parts/page_row.tpl'}
  
  <div class="page-childs" id='page-{$d.id}-childs' style='display:{if $d.expanded}block{else}none{/if};'>     
  
    {foreach from=$ar.list item=d}          
      
      {include file='parts/page_row.tpl'}      
      <div class="page-childs" id='page-{$d.id}-childs' style='display:none;'></div>     
            
    {/foreach}

  </div>
{/if}


{elseif $sv->code=='makeroot'}
  <h3 class='nomargin'>Создание главной страницы</h3>     
  {include file='scaffold/create.tpl'}
  
{elseif $sv->code=='edit'}

<div id='content'>

  <h3 class='nomargin'>Редактирование страницы "{$ar.d.title}" 
  | <a href="{u act=$sv->act}{$m->slave_url_addon}">список</a> 
  | <a href='{u act=$sv->act code=$m->code id=$sv->id}{$m->slave_url_addon}'>обновить без отправки данных</a>
  | <a href='{u act=$sv->act code=remove id=$ar.d.id}{$m->slave_url_addon}'>удалить страницу</a></h3>     

  
  {if $ar.err_box}<div style='margin-top: 10px;'>{$ar.err_box}</div>{/if}
  <form action="{u act=$sv->act code=$sv->code id=$sv->id}" enctype="multipart/form-data" method="post" id='text-form'>
  
  <div class="form-area">
   
    <p class="title">
      <label>Заголовок страницы</label>
      <input type='text' class="textbox" name='new[title]' value="{$ar.v.title}">
    </p>    
  
    <div id="extended-metadata" class="row" style="display:none;">    
      <table class="fieldset" border="0" cellpadding="0" cellspacing="0">        
        <tr>
          <td class="label"><label for="page_slug">Идентификатор страницы в строке адреса (англ.)</label></td>
          <td class="field"><input class="textbox" id="page_slug" maxlength="100" name="new[slug]" size="100" value="{$ar.v.slug}"></td>
        </tr>       
        <tr>
          <td class="label"><label for="page_sitemap">Показывать в карте сайта?</label></td>
          <td class="field"><input type="checkbox" id="page_sitemap" name="new[sitemap]" {if $ar.v.sitemap}checked{/if}></td>
        </tr>   
        <tr>
          <td class="label"><label for="page_comments_in">Комментарии включены?</label></td>
          <td class="field"><input type="checkbox" id="page_comments_in" name="new[comments_on]" {if $ar.v.comments_on}checked{/if}></td>
        </tr>           
        <tr>
          <td class="label"><label for="page_replycount">Количество комментариев</label></td>
          <td class="field"><input class="textbox" id="page_replycount" name="new[replycount]" size="10" value="{$ar.v.replycount}"></td>
        </tr>           
        <tr>
          <td class="label"><label for="page_title">TITLE страницы</label></td>
          <td class="field"><input class="textbox" id="page_title"  name="new[page_title]" size="100" value="{$ar.v.page_title}"></td>
        </tr>         
        <tr>
          <td class="label"><label for="page_description">META-DESCRIPTION страницы (для поисковиков)</label></td>
          <td class="field"><input class="textbox" id="page_description"  name="new[description]" size="100" value="{$ar.v.description}"></td>
        </tr>    
        <tr>
          <td class="label"><label for="page_keys">META-KEYWORDS страницы (для поисковиков)</label></td>
          <td class="field"><input class="textbox" id="page_keys"  name="new[keywords]" size="100" value="{$ar.v.keywords}"></td>
        </tr>                     
      </table>     
    </div>    
    
    <p class="more-or-less">
      <small><a id="more-extended-metadata" onclick="$('#extended-metadata').toggle();$('#less-extended-metadata').toggle();$('#more-extended-metadata').toggle();return false;" href="#">больше</a>
      <a id="less-extended-metadata" onclick="$('#extended-metadata').toggle();$('#less-extended-metadata').toggle();$('#more-extended-metadata').toggle();" href="#" style="display: none;">скрыть</a>
      </small>
    </p>
    
    <div>&nbsp;</div>
    
    <div id='parts-box'>
     
      {* закладки *}
      <div class="tabs" id="tabs">{foreach from=$ar.parts item=d key=k}<A href="javascript:select_part('part-{$k}')" class="tab{if $d.name==body} here{/if}">{if $d.name=='body'}Основной текст{elseif $d.name=='preview'}Текст анонса{else}{$d.name}{/if}</A>{/foreach}{* <A href="javascript:select_part('part-uploads')" class="tab">Файлы</A>*}</div>
       
<div class='parts' id='parts'>
  <div id='part-0' class="part">
  
        
  {*  текстовое поле с маркитапом *}
  <div id=page-options>
  Фильтр применяемый к тексту при отображении: 
  <select name='new[parts][0][filter_id]' id="filter_select{$k}" 
{* 
        {literal}
          onchange="
            if(confirm('Переключить режим ввода?')) {                      
              $('#text-form').submit();
            } else { 
            {/literal}                      
              $('#filter_select{$k}').selectOptions('{$d.filter_id}');                       
            {literal}
            }"
        {/literal}
*}        
>{$ar.parts.0.filter_opts}</select>
  
  &nbsp;&nbsp;&nbsp;&nbsp; {$ar.markitup->tinymce_button}
  </div>
 
  {$ar.markitup->js}
  
  
  <div class="html">
  <textarea style="width:100%;"  name='new[parts][0][content]' id='textarea{$k}'>{$ar.parts.0.v_content}</textarea>
  </div>          

  {$ar.attach.iframe}

        </div>    
      
            
              
        
      </div>
      
    </div>
    
    <div id="page-options">
      <table><tbody>
        <tr>
          <td class="page-option">
            Компоновка:&nbsp;<select name='new[layout_id]'>{$ar.opts.layout_id}</select>
          </td>
          <td class="page-option">
            Тип страницы:&nbsp;<select name='new[classname]'>{$ar.opts.classname}</select>
          </td> 
          <td class="page-option">
            Статус:&nbsp;<select name='new[status_id]'>{$ar.opts.status_id}</select>
          </td>       
        </tr>      
      </tbody></table>
    </div>
    
    {if $ar.d.updated_at}
    <div id='update-info'>
      Последнее редактирование: {$ar.last_update} - <b>{$ar.last_login}</b>
    </div>
    {/if}
    
  </div> 

  <p class="buttons">

    <input class="button" name="commit" value="Сохранить" type="submit"> 
    <input class="button" name="continue" value="Сохранить и продолжить редактирование" type="submit"> 
    или <a href="{u act=$sv->act}">Отмена</a>
    
  
  </p>
  </form>
  
</div>  
  

{elseif $sv->code=='newchild'}

  <h3 class='nomargin'>Создание нового раздела</h3>     
  {include file='scaffold/create.tpl'}
  
  
{else}  
   {include file="scaffold/`$m->code`.tpl"}
  
{/if}