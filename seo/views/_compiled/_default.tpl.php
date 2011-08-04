<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo $title; ?></title>
    
	<?php if ($meta_tags && !empty($meta_tags)) { ?>
		<meta content="<?php echo $meta_tags; ?>" name="keywords">
    <?php } ?>
	<?php if ($meta_tags && !empty($meta_tags)) { ?>
		<meta content="<?php echo $meta_description; ?>" name="description">
    <?php } ?>


    <?php if (isset($styles) && !empty($styles)) { ?> 
        <?php foreach ($styles as $key => $style) { ?>
            <link id="css_<?php echo $key; ?>" rel="stylesheet" href="<?php echo $style; ?>" type="text/css" media="screen" charset="utf-8">
        <?php } ?>
    <?php } ?>

    

    <!--[if lte IE 9]>
    <link id="css_ie" rel="stylesheet" href="<?php echo SITE_URL;?>assets/css/app/ie.css" type="text/css" media="screen" charset="utf-8">
    <![endif]-->    
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->    
    
    <?php if (isset($scripts) && !empty($scripts)) { ?>
        <?php foreach ($scripts as $key => $script) { ?>
            <script charset="utf-8" src="<?php echo $script; ?>"></script>
        <?php } ?>
    <?php } ?>

	
    <!-- EXAMPLE TO TEST OUTLINE WITH UNDEFINED VARS ! -->
    <?php foreach ($shit as $sh) { ?>
        <?php echo $sh; ?>
    <?php } ?>
    
</head>
<body>    
    
<?php if (!isset($loggedin)) { ?>
    <?php $loggedin = false; ?>
<?php } ?>

<?php $defined_vars = get_defined_vars(); ?>

<?php echo outline_function_include(array("view" => 'views/_background', "data" => $defined_vars)); ?>
    
<div id="lBody" class="rel z-2 wp-100 mlr-auto">    
    
    <?php echo outline_function_include(array("view" => 'views/_header', "data" => $defined_vars)); ?>    
	
	<?php if ($adminpanel) { ?>
	    <?php echo outline_function_include(array("view" => 'views/admin/menu', "data" => $defined_vars)); ?> 
	<?php } ?>
	
	
	
    <?php if ($errors) { ?>
        <?php echo outline_function_include(array("view" => 'views/_errors', "data" => $defined_vars)); ?>
    <?php } ?>

	<div style="width:960px; margin:0 auto;">
	    <?php if (isset($view)) { ?>
	        <?php echo outline_function_include(array("view" => 'views/'.$view, "data" => $defined_vars)); ?>    
	    <?php } ?>
	</div>

    <?php echo outline_function_include(array("view" => 'views/_footer', "data" => $defined_vars)); ?>    


</div>

<?php if ($debug) { ?>
    <?php echo outline_function_include(array("view" => 'views/_debug', "data" => $defined_vars)); ?>
<?php } ?>

<?php foreach ($scripts_custom as $key => $script) { ?>
    <?php echo $script; ?>
<?php } ?>


</body>
</html><?php $_ = OutlineRuntime::finish(__FILE__); ?>