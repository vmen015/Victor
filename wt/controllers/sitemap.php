<?php

require_once('models/page_model.php');

class sitemap extends mmcontroller {
	
	public function __construct($arg) {

        parent::__construct($arg);
        $this->data['title'] = 'title';
        $this->data['menu'] = 'index';    
	
		$this->page_model = new page_model($this->config);
    }
	
	public function before() {
		parent::before();
    }
    
    public function after() {
        parent::after();
    }

	public function index() {
		
		$pages = $this->page_model->get();
		
		
		header("Content-Type: text/xml"); 
		$output = '';
		$output .= '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		$domain = 'http://'.$_SERVER['HTTP_HOST']; 
		foreach($pages as $page){
			$output .= "<url>";
				$output .= "<loc>".htmlspecialchars($domain.'/page/'.$page['title'], ENT_QUOTES)."</loc>";
			$output .= "</url>";
		}


		$output .= '</urlset> ';
		echo $output;
		
		exit;
	}
	
	
	
	

	

	
}

