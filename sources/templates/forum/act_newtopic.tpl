{include file='parts/err_box.tpl'}

{$ar.s.err_box}

{if !$ar.s.submited || $ar.s.err}

  <div class="textbox">
    
    <form action="{$m->c_forum_url}?newtopic" method="POST" enctype="multipart/form-data">
    <table cellpadding="5">
        <tr>
          <td>
            <label for="new_title">Заголовок</label>
            <input type='text' id='new_title' name='new[title]' value="{$ar.s.v.title}" size="70">
          </td>
        </tr>
        
        <tr>
          <td>
            <label for='new_desc'>Краткое описание</label>
            <input type='text' id='new_desc' name='new[description]' value="{$ar.s.v.description}" size="70">
          </td>
        </tr>    
        
       
        <tr>
          <td class='bbcode'>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td><label for='new_text' style='vertical-align:bottom;'>Текст сообщения</label></td>
              <td style='vertical-align:bottom;' align="right" style='padding-right: 20px;'>{$ar.markitup}</td>
            </tr>
          </table>
          <textarea id='new_text' cols="70" rows="10" name='new[text]'>{$ar.s.v.text}</textarea>
        </td></tr>
        <tr>
          <tD>
            <input type="submit" value="Создать тему">
          </td>
        </tr>
      </table>  
    </form>
    
  </div>
{/if}