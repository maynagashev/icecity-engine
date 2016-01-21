
{if $sv->user.session.account_id>0 && $sv->parsed.show_admin_top}    
<table cellpadding="0" cellspacing="0" style="border: 1px solid gray;"><tr>
  <td bgcolor="white" style="padding:5px 15px;">
    {if $sv->parsed.not_approved_comments>=0}
      {if $sv->parsed.not_approved_comments>0}<b>{/if}<a href='{u act='commentsedit' code='moderation'}'>Непроверенные комментарии ({$sv->parsed.not_approved_comments})</a></b>
    {/if}
   
    {* 
      {if $sv->parsed.bposts>0}<b>{/if}<a href='{u act='bposts' code='premoderation'}'>Премодерация ({$sv->parsed.bposts})</a></b> &nbsp;|&nbsp;
      {if $sv->parsed.reports>0}<b>{/if}<a href='{u act='breports' code='read'}'>Жалобы модератору ({$sv->parsed.reports})</a></b> &nbsp;|&nbsp;
      <a href='/content/site_map/' target="_blank">Дерево категорий</a></b>
    *}
  </td></tr>    
</table>
{/if}