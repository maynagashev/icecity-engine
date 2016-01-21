{if $sv->vars.p_title}{$sv->vars.p_title}
{elseif $page.page_title && $sv->vars.site_title}{$page.page_title}{* - {$sv->vars.site_title}*}
{elseif $page.title && $sv->vars.site_title}{$page.title} - {$sv->vars.site_title}
{elseif $page.page_title && !$sv->vars.site_title}{$page.page_title}
{elseif $page.title && !$sv->vars.site_title}{$page.title}
{elseif !$page.title && $sv->vars.site_title}{$sv->vars.site_title} // {$sv->act}
{else}{$sv->act}
{/if}