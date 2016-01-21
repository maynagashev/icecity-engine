 <script type = "text/javascript" src = "/sources/js/tree.js"></script>  
 <script type = "text/javascript"/">
   tree_model = '{$m->name}';
   tree_module = '{$sv->act}';
 </script>
 <style>
  {literal}
  .page-switcher { text-decoration: none; font-size: 140%; }
  .page-childs { padding: 5px 0 10px 30px; }
  {/literal}
</style>  

 
 <div style='margin: 20px 0;'>

    {foreach from=$ar.list item=d}  
      {include file='scaffold/parts/tree_row.tpl'}      
      <div class="page-childs" id='page-{$d.id}-childs' style='display:none;'></div>
    {/foreach}

</div>