
<table cellpadding="5">
    <tr>
      <td>
        <label for="new_title">Заголовок</label>
        <input type='text' id='new_title' name='new[title]' value="{$ar.s.v.title}" size="70">
      </td>
    </tr>
   
    {if $m->f_act=='editpost' && $ar.d.new_topic}
      <tr>
        <td>
          <label for="new_desc">Описание темы</label>
          <input type='text' id='new_desc' name='new[description]' value="{$ar.s.v.description}" size="70">
        </td>
      </tr>    
    {/if}
    
    <tr>
      <td class='bbcode'>
        <table width="100%" cellpadding="0" cellspacing="0" style='margin-bottom: 0px;'>
          <tr>
            <td style='vertical-align:bottom;'><label for='new_text' style='margin-bottom:0;'>Текст сообщения</label></td>
            <td align="right" style='padding-right: 20px;vertical-align:bottom;'>{$ar.markitup}</td>
          </tr>
        </table>
        <textarea cols="70" rows="10" id='new_text' name='new[text]'>{$ar.s.v.text}</textarea>
    </td></tr>
    <tr>
      <td><input type="submit" value="Сохранить"></td>
    </tr>
  </table>  