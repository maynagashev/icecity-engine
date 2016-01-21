<table width="100%" cellpadding="5" cellspacing="1" bgcolor="#efefef" class='ct'>
<tr class='t-head'><td colspan="5">Форумы</td></tr>

{foreach from=$ar.list item='c' key='cat_id'}

    <tr class='t-subhead'>    
      <td width="49%" class='t-left' colspan="2">
        {if $cat_id>0}<b><a href='{$c.d.url}'>{$c.d.title}</b>{else}Название форума{/if}
      </td>
      <td align="center" width="5%">Темы</td>
      <td align="center" width="5%">Сообщения</td>
      <td width="35%" class='t-right' nowrap align="center">Обновление</td>  
    </tr>
    
    {foreach from=$c.forums item=d}
    
        <tr valign="top">
          <td><img src='/i/forum/convert{if $d.is_new}2{else}1{/if}.gif' border="0" width="16" height="16" vspace="3"></td>
          <td><a href='{$sv->view->root_url}{$d.slug}/'>{$d.title}</a><br>
              <span style='font-size: 90%;'>{$d.description}</span></td>
          <td align="center">{$d.topics}</td>
          <td align="center">{$d.posts}</td>
          <td>{$d.last_topic_url}</td> 
        </tr>
        
    {/foreach}
    
{/foreach}
 
<tr class="t-subhead"><td colspan="5">&nbsp;</td></tr>
</table>
