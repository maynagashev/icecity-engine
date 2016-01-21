<div style='margin: 40px 0 20px 0;'>

  <table cellpadding="5" class="ct" width="100%" style='margin-bottom:0;'>
    <tr class="t-head"><td>Последние сообщения в теме</td></tr>
  </table>
 
 
  {foreach from=$ar.last.list item=d}  
    {include file='forum/post_row.tpl'}  
  {/foreach}
 
</div>      