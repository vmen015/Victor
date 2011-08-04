<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{$title}</title>
    
	{if $meta_tags && !empty($meta_tags)}
		<meta content="{$meta_tags}" name="keywords">
    {/if}
	{if $meta_tags && !empty($meta_tags)}
		<meta content="{$meta_description}" name="description">
    {/if}


    {if isset($styles) && !empty($styles)} 
        {foreach $styles as $key => $style}
            <link id="css_{$key}" rel="stylesheet" href="{$style}" type="text/css" media="screen" charset="utf-8">
        {/foreach}
    {/if}

    

    <!--[if lte IE 9]>
    <link id="css_ie" rel="stylesheet" href="<?php echo SITE_URL;?>assets/css/app/ie.css" type="text/css" media="screen" charset="utf-8">
    <![endif]-->    
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->    
    
    {if isset($scripts) && !empty($scripts)}
        {foreach $scripts as $key => $script}
            <script charset="utf-8" src="{$script}"></script>
        {/foreach}
    {/if}

	
    <!-- EXAMPLE TO TEST OUTLINE WITH UNDEFINED VARS ! -->
    {foreach $shit as $sh}
        {$sh}
    {/foreach}
    
</head>
<body>    
    
{if !isset($loggedin)}
    {set $loggedin = false}
{/if}

{set $defined_vars = get_defined_vars()}

{include view='views/_background' data=$defined_vars}
    
<div id="lBody" class="rel z-2 wp-100 mlr-auto">    
    
    {include view='views/_header' data=$defined_vars}    
	
	{if $adminpanel}
	    {include view='views/admin/menu' data=$defined_vars} 
	{/if}
	
	
	
    {if $errors}
        {include view='views/_errors' data=$defined_vars}
    {/if}

	<div style="width:960px; margin:0 auto;">
	    {if isset($view)}
	        {include view='views/'.$view data=$defined_vars}    
	    {/if}
	</div>

    {include view='views/_footer' data=$defined_vars}    


</div>

{if $debug}
    {include view='views/_debug' data=$defined_vars}
{/if}

{foreach $scripts_custom as $key => $script}
    {$script}
{/foreach}


</body>
</html>