<?php
require_once('models/page_model.php');

class page extends mmcontroller {
    
    public function __construct($arg) {
        parent::__construct($arg);
        $this->data['title'] = 'title';
        $this->data['menu'] = 'index';    
// $this->data['debug'] = true;     

      	$this->page_model = new page_model($this->config);
    
    }
    
    public function before() {
        parent::before();

    }
    
    public function after() {
        parent::after();
    }

    // default method index: 
    // used as fallback if no controller or method is found
    public function index($title = null) {
        // print_r(func_get_args()); // get all arguments in order
        $this->get_page($title);
		
    }



	public function get_page($title){
		
		// Select page 
		$data = $this->page_model->get($title);
		
		$this->data['view'] = 'page/index';   
		$this->data['data'] = $data[0];
		
		$this->data['title'] = Str::decode($data[0]['title']);
		$this->data['meta_tags'] = Str::decode($data[0]['meta_tags']);
		$this->data['meta_description'] = Str::decode($data[0]['meta_description']);
	}
	
}