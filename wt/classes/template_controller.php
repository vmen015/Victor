<?php

class template_controller extends controller {
	
	public $template = '_default';
	public $data = array();
	
	public function __construct(){

		parent::__construct();

	}

	public function before() {
		// echo 'before';	
			
	}		

	public function after() {
		
		$this->data['js_vars'] = array('controller' => 'news', 'action'=> 'sport');
		
		$this->render();		
		
	}

	public function render() {

		$this->view($this->template, $this->data);

	}



}