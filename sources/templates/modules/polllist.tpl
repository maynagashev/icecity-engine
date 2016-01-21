<div id="section-title"><h3>Архив голосований</h3></div>

{if $ar.content}{$ar.content}{/if}

<div id="polls">

  {foreach from=$ar.list item=d}
    <div class='poll'>
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr valign="top">
        <td width=10%><div class="date">{$d.f_date}</div>
        {if $d.active}<p align="center"><a href='#active-poll'>активное</a></p>{/if}
        </td>
        <td width="90%" class='poll-td'>
          <div class="question">{$d.question}</div>
          <div class="results">{$d.results}</div>   
        </td></tr>
      </table>
      
    </div>
  {/foreach}

</div>

<div class='page-list'>{include file='parts/pl.tpl'}</div>