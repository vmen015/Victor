<?php

class mmcontroller extends controller {
    
	public $template = '_default';
	public $data = array();
	public $autorender = false;
	public $config;
    public $cache;    
    public $cache_refresh = false;
    public $session;
    public $me = array();
    public $loggedin = false;
    
	public function __construct($config=null) {
        parent::__construct($config);
		if ($config) {
		    $this->config = $config;
		    $this->data['config'] = $config;
		    if (isset($this->config['assets']) && is_array($this->config['assets'])) {
		        $this->_loadAssets();
		    }
		    if (isset($this->config['metatags']) && is_array($this->config['metatags'])) {
		        $this->_loadMetatags();
		    }
		    $this->session = session::instance($this->config);
		}
		$this->_loadHelpers();
		$this->cache = new Cache($this->config['cache']['path']);
		if (isset($_GET) && isset($_GET['refreshall'])) {
		    $this->_emptyCache();
		}
		if (isset($_GET) && isset($_GET['refresh'])) {		
		    $this->cache_refresh = true;
	    }	    
	}    
	
	public function before() {
		// echo 'before';	
		$this->data['controller'] = '';		
		$this->data['page'] = '';	
        $this->data['scripts_custom'] = array();
        $this->data['errors'] = array();
        if ($this->session->get('loggedin',false)) {
            $this->loggedin = $this->session->get('loggedin');             
        }
        $this->data['loggedin'] = $this->loggedin;    
    	$this->data['menu_links'] = $this->page_model->get_menu();

	}		

	public function after() {
				
		$this->data['controller'] = get_class($this);
		// don't supply full config to js (security)
		$this->data['js_runtime'] = array(
		    'controller' => $this->data['controller']
		    , 'page' => $this->data['page']
		    , 'site_url' => SITE_URL
		    , 'site_dir' => SITE_DIR
		);
        $this->data['scripts_custom'][] = '<script id="jsRuntime" type="text/javascript" charset="UTF-8"> mm.runtime='.json_encode($this->data['js_runtime']).'; </script>';		
		
		$this->render();		
	}

	public function render() {
        $this->data['render_time'] = microtime(true) - START_TIME;
        if (isset($this->config['outline']) && isset($this->config['outline']['integration']) && $this->config['outline']['integration']) {
            $this->_outlineView('views/'.$this->template, $this->data);
        } else {
    		$this->view($this->template, $this->data);
    	}
	}	
    
    // function used to compile the view with outline template syntax
    protected function _outlineView($template, $data) {
        mmoutline::init();
        $o = new mmoutline($template, $data);
        $o->render();
    }
    
    public function _loadHelpers() {
        // function load_class($class=null, $params=null, $path='models', $instantiate = TRUE) {
        load_class('Arr', null, 'libraries/helpers', false);
        load_class('Cache', null, 'libraries/cache', false);
        load_class('file', null, 'libraries/helpers', false);  
        load_class('str', null, 'libraries/helpers', false);
        load_class('Validate', null, 'libraries/helpers', false);                        
    }
    
	public function _loadAssets() {
	    if (isset($this->config['assets']['styles']) && is_array($this->config['assets']['styles'])) {
            $this->data['styles'] = array();	        
	        foreach($this->config['assets']['styles'] as $key => $style) {
	            if (strpos($style, 'http://') !== false || strpos($style, 'https://') !== false || strpos($style, '://') !== false) {
    	            $this->data['styles'][] = $style;
    	        } else {	            
    	            $this->data['styles'][] = SITE_URL.$style;
                }
            }
        }
	    if (isset($this->config['assets']['scripts']) && is_array($this->config['assets']['scripts'])) {
            $this->data['scripts'] = array();	        
	        foreach($this->config['assets']['scripts'] as $key => $script) {
	            if (strpos($script, 'http://') !== false || strpos($script, 'https://') !== false || strpos($script, '://') !== false) {
    	            $this->data['scripts'][] = $script;	                
	            } else {
    	            $this->data['scripts'][] = SITE_URL.$script;	                
	            }

            }
        }        
    }
    
    public function _loadMetatags() {
        if (isset($this->config['metatags']) && is_array($this->config['metatags']) && !empty($this->config['metatags'])) {
            $this->data['metatags'] = $this->config['metatags'];
        }
    }
    
    public function _emptyCache($key = null) {
        $this->cache->clearall();
    }
    
        
    public function _getYahooBlogsFeed($which) {
        
        if (isset($this->config['yahooblogfeeds'][$which]) && !empty($this->config['yahooblogfeeds'][$which])) {
        
            $feed = $this->cache->get('yahooblogsfeed_'.$which, $this->config['cache']['blogsfeed_expiration']);
        
            if ($this->cache_refresh || $feed === false) {
                $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, "http://pipes.yahoo.com/pipes/pipe.run?_id=0c92b34341bb61e94f06e4cf8839537c&_render=php");
                curl_setopt($ch, CURLOPT_URL, $this->config['yahooblogfeeds'][$which]);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $feed = curl_exec($ch);
                curl_close($ch);       

                $this->cache->set('yahooblogsfeed_'.$which, $feed);
            }
        
            $this->data['blogfeed'] = unserialize($feed);    
        } else {
            $this->data['blogfeed'] = array();
        }
            
         
    }
        
}