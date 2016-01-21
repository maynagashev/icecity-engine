
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td style='padding-bottom:10px;'>
    <span style='color:gray;'>{$ar.d.description}</span>
    
    </td>
    <Td align="right">
    
      {if $m->allow_newtopic}
        <div align="right" style='margin: 0 0 10px 0;'>
          <a href='{$m->c_forum_url}?newtopic'>Создать тему</a>
        </div>
      {/if}    
    
    </Td>
  </tr>
</table>

{include file='forum/topic_list.tpl'}


{if $m->is_moderator} 
  {*
  <div class="textbox">
    опции модератора
  </div>
  
  *}
{/if}

