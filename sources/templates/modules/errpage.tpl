  

<strong>

{if $ar.errm=='draft'}
    
    {php} header("HTTP/1.0 307"); {/php}    
    Запрашиваемая страница временно не доступна, 
    возможно она находится в стадии редактирования.
    
{elseif $ar.errm=='hidden'}

    {php} header("HTTP/1.0 303"); {/php}    
    Запрашиваемая страница отключена.
    
{elseif $ar.errm=='removed'}

    {php} header("HTTP/1.0 303"); {/php}    
    Запрашиваемая страница не найдена, возможно она была перемещена.
    
{elseif $ar.errm=='forbidden'}

    {php} header("HTTP/1.0 403 Forbidden"); {/php}    
    Ошибка 403 - доступ запрещен.
    
{elseif $ar.errm=='notfound'}

    {php} header("HTTP/1.0 404 Not Found"); {/php}    
    Ошибка 404 - Запрашиваемая страница не найдена.
    
{else}
  
  {$ar.errm}
  
{/if}  

</strong>    

<br><br><a href='{$ar.return_url}'>Перейти на главную.</a>


    

