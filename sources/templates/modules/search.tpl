{literal}
<style>
  #search {margin: 20px;}
  #search td {border: 0; padding: 5px;}
  #search form {margin-bottom: 15px;}
  #search .results td { padding:2px;}
  #search .results td.date {text-align:right; color: #666666;}
</style>
<script language="JavaScript">
  function join_wh() {
    var a = [] ;
    $('input:checkbox:checked').each(function() { a.push($(this).val()); });
    $('#input_where').val(a.join(','));
  }
</script>

{/literal}

<div id='search'>



{if $m->code=='public_default'}

    <form action='{$m->root_url}query/' method="GET" enctype="multipart/form-data">    
      <div>
        <label for='input_q' style='display: block; margin-bottom: 10px;'>1) Введите строку для поиска:</label>
        <input type="text" name='q' size="40" id='input_q'>&nbsp;
        <input type='hidden' name='where' id='input_where'>
        <input type='submit' value="Поиск" onclick="join_wh();">
      </div>
    </form>    
  
    <div>
    2) Выберите область поиска:
    <table cellpadding="5">
      {foreach from=$ar.models item=t key=k}
        <tr><td><input type='checkbox' name='' value='{$k}' checked id="ch_{$k}" class="models"></td><td><label for='ch_{$k}'>{$t}</label></td></tr>
      {/foreach}
    </table>
    </div>

  
{elseif $m->code=='public_results'}
  
  <div class="results">
  
      <form action='{$m->root_url}query/' method="GET" enctype="multipart/form-data">    
        <div>
          <label for='input_q' style='display: block; margin-bottom: 5px;'>Строка для поиска:</label>
          <input type="text" value="{$ar.query}" name='q' size="40" id='input_q'>&nbsp;
          <input type='hidden' name='where' id='input_where'>
          <input type='submit' value="Поиск" onclick="join_wh();">
        </div>
      </form> 
      
      <h3>Результаты поиска: {$ar.pl.size}.</h3>
      
  
    <table width="100%">
    {foreach from=$ar.list item=d name=a}
      <tr>
        <td width="1%" align="right">{$d.i}.</td>
        <td><b><a href='{$d.url}'>{$d.title}</a></b></</td>
        <td class="date">{$d.date}</td>
     </tr>
     <tr><td></td>
        <td colspan="2"><div>{$d.description}</div></td>
      </tr>
      <tr><td colspan="3">&nbsp;</td></tr>
    {/foreach}
    {if $ar.count<=0}
      <tr><td>Ничего не найдено, попробуйте изменить условия поиска.</td></tr>
    {/if}
    </table>
  </div>
  {include file='parts/pl.tpl'}

{/if}


</div>