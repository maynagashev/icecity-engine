{* TOTAL={$smarty.foreach.msgs.total} / MSGS_COUNT={$sv->msgs_count} *}
<style>
{literal}
  div.msgs_box {display: block; margin: 5px 0; padding:10px; border: 1px solid green;  background-color: #ddffdd;}
  div.msgs_box div {display: block; margin: 2px 0; padding: 5px;}
  div.msgs_box div.err {background-color: #ffdddd;}
  
{/literal}
</style>
{if $sv->msgs_count>0}
<div class="msgs_box">
{foreach from=$sv->msgs item='d' name='msgs'}
  <div {if $d.err}class='err'{/if}>{$d.text}</div>
{/foreach}
</div>
{/if}