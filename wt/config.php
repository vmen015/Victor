<?php

return array(
    
    'default_route' => array('page','index')    // controller, method
    , 'site_url' => array(
        'localhost' => '/Webmedic/Victor/wt/'
        , 'demo' => '/mmmvc/'
        , 'default' => '/'
    )
    , 'cookie' => array(
        'lifetime' => 60 * 60 * 24 * 14
        , 'path' => '/'
        , 'domain' => 'localhost'
        , 'secure' => false        
        , 'httponly' => true
    )
	, 'http_digest_users' => array(
		'victor' => 'k4n44lw3g'
	)
    , 'session' => array(
        'name' => 'mmmvc'
    )
    , 'admin' => array(
        'email' => 'vm@mediamedics.nl'
    )
    , 'contact' => array(
        'email' => 'vm@mediamedics.nl'
        , 'subject' => 'Website contact msg ' 
    ) 
    , 'email' => array(
        'smtp' => 'mail.mediamedics.nl'
        , 'port' => 587
        , 'user' => 'vvr@mediamedics.nl'
        , 'password' => 'v1ct0r'
    )
    , 'db' => array(
        'driver' => 'sqlite'
        , 'sqlite_db_file' => 'data/database.sqlite'
    )
    , 'cache' => array(
        'path' => 'cache'
        , 'expiration' => 60 * 60 * 24 * 7  // 1 week?
        , 'blogsfeed_expiration' => 60 * 60 * 3  // 3 hours?
    )
    , 'salts' => array(
        'prefix' => 'zYxAbc'
        , 'suffix' => 'dEfwWu'
    )
    , 'blacklist' => array(
        'username' => array('home', 'index', 'test', 'login', 'logout', 'signup', 'ajax', 'player', 'admin', 'upload', 'profile', 'account', 'song','songs', 'track','tracks')
    )
    , 'directories' => array(
        'audio' => 'audio/'
    )
    , 'assets' => array(
        'styles' => array(
            'assets/css/mm/mmBase.css', 'assets/css/mm/mmLayout.css'
            , 'assets/css/mm/mmObjects.css'
            // , 'assets/css/plugins/scrollerbar/ScrollerBar.css'
            , 'assets/css/plugins/tooltip/ToolTip.css'
			// texteditor
			, 'assets/css/plugins/MooEditable/MooEditable.css'
			, 'assets/css/plugins/MooEditable/MooEditable.Extras.css'
			
            , 'assets/css/app/app.css'
            , 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:regular,bold'
            , 'http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold'
        )
        , 'scripts' => array(
            // 'assets/js/mootools/mootools-core-1.3.js'
			'assets/js/plugins/modernizr/modernizr-1.7.min.js'
            ,'https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools-yui-compressed.js'
            , 'assets/js/mootools/mootools-more-1.3.2.1-yc.js'
            , 'assets/js/mm/mm.js', 'assets/js/mm/mmMain.js', 'assets/js/mm/mmObjects.js', 'assets/js/mm/mmUtils.js'
            , 'assets/js/mm/mmPlaceholder.js'
            , 'assets/js/mm/mmTextareaGrow.js'                                
            , 'assets/js/app/app.js'
            , 'assets/js/plugins/tooltip/Source/ToolTip.js'
            // , 'assets/js/plugins/array-sortby/array-sortby.js'
            // , 'assets/js/plugins/Drag.Flick.js', 'assets/js/plugins/Fx.Push.js'
            , 'assets/js/plugins/slideshow/Loop.js', 'assets/js/plugins/slideshow/SlideShow.js'
            , 'assets/js/plugins/slideshow/Tabs.js'                
            , 'assets/js/plugins/wallpaper/Wallpaper.js', 'assets/js/plugins/wallpaper/Wallpaper.Fill.js'            
			// texteditor
			, 'assets/js/plugins/MooEditable/MooEditable.js'
			, 'assets/js/plugins/MooEditable/MooEditable.UI.MenuList.js'
			, 'assets/js/plugins/MooEditable/MooEditable.Extras.js'
			
            // , 'assets/js/plugins/scrollerbar/ScrollerBar.js'
            // , 'assets/js/plugins/twitter/Request.Twitter.js'
            // , 'assets/js/plugins/iframeformrequest/Source/iFrameFormRequest.js'
            // , 'assets/js/plugins/element.file.js'
            , 'assets/js/mm/init.js'
        )
    )
    , 'metatags' => array(
        // array(
        //     'name' => 'MobileOptimized'
        //     , 'content' => 'width'
        // )
        // , array(
        //     'name' => 'HandheldFriendly'
        //     , 'content' => 'true'            
        // )
        // , array(
        //     'name' => 'viewport'
        //     // , 'content' => 'width=device-width, user-scalable=yes, initial-scale=1, maximum-scale=1, minimum-scale=1'            
        //     , 'content' => 'width=device-width,initial-scale=1.0'   // to use in combination with the scalefix script in _default.php
        // )        
        // , array(
        //     'name' => 'apple-mobile-web-app-capable'
        //     , 'content' => 'yes'
        // )
    )
    , 'outline' => array(
    	// Whether to use Outline templates instead of regular PHP view files
     	'integration' => true	       
    )
    
);