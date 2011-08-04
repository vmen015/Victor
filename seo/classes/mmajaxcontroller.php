<?php


class mmajaxcontroller extends controller {
    
    public $config;
	public $data = array();    
	public $me;
	public $loggedin = false;
    
	public function __construct($config=null) {
        parent::__construct($config);
		if ($config) {
		    $this->config = $config;
		    $this->data['config'] = $config;
		}
		$this->_loadHelpers();
        $this->session = session::instance($this->config);		
	}
    
    public function before() {
        if ($this->session->get('loggedin',false)) {
            $this->loggedin = $this->session->get('loggedin');            
            $this->me = $this->session->get('user', array());            
        }        
    }
    
    public function after() {
        
    }    
        
    public function _loadHelpers() {
        // function load_class($class=null, $params=null, $path='models', $instantiate = TRUE) {
        load_class('Arr', null, 'libraries/helpers', false);
        load_class('Cache', null, 'libraries/cache', false);
        load_class('file', null, 'libraries/helpers', false);  
        load_class('str', null, 'libraries/helpers', false);
        load_class('Validate', null, 'libraries/helpers', false); 
    }    
    
}
