
{include file='parts/err_box.tpl'}

{$ar.s.err_box}

{if !$ar.s.submited || $ar.s.err}

    <div class="textbox">
      <form action="{$ar.action_url}" method="POST" enctype="multipart/form-data">
        {include file='forum/form_answer.tpl'}
      </form>
    </div>         
        
    {if $ar.last.count>0}
      {include file='forum/last_posts.tpl'}
    {/if}

{/if}