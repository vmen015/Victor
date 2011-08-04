<?php

require_once('models/page_model.php');

class admin extends mmcontroller {
    
    public function __construct($arg) {

        parent::__construct($arg);
        $this->data['title'] = 'title';
        $this->data['menu'] = 'index';    
	
		$this->page_model = new page_model($this->config);
    }
	
	public function before() {
        http_digest('Adminpaneel');
		parent::before();
        if ($this->session->get('loggedin',false)) {
            $this->data['loggedin'] = $this->session->get('loggedin');
            
			
        }


    }
    
    public function after() {
        parent::after();
    }

	public function index() {
       	$args = func_get_args();		
		
		if(empty($args)){
			
       		return $this->overview();

		}
    }

	public function overview() {
		
		$this->data['view'] = 'admin/index';    
        $this->data['title'] = 'Login';   
        $this->data['page'] = 'login'; 

	}

	
	
	
	
	////////////////////////////////////////////////////////
	// TUTORIALS
	////////////////////////////////////////////////////////
	public function page($arg = null, $arg2 = null) {
		
		if(isset($arg) && !empty($arg)) {
			
			if($arg== 'add'){
				$this->page_add($arg);
			
			} elseif($arg == 'edit'){
				$this->page_edit($arg2);
				
			} elseif($arg == 'delete'){
				$this->page_delete($arg2);
			}
		
		}else{			
			
			if (isset($_POST['pos_up'])) {
				$old_pos = (int) $_POST['old_pos'];
				$this->page_model->update_pos('up', $old_pos);
			}
			
			if (isset($_POST['pos_down'])) {
				$old_pos = (int) $_POST['old_pos'];
				$this->page_model->update_pos('down', $old_pos);
			}
			
			$this->data['pages'] = $this->page_model->get();
			$this->data['highest_pos'] = $this->page_model->get_highest_pos();
			
			$this->data['success'] = $this->session->get_once('success');
			$this->data['view'] = 'admin/page/overview';
			
		}
	
	}
	
	
	public function page_add($args = null) {
		
		$this->data['view'] = 'admin/page/form';
				
		if(isset($_POST) && !empty($_POST)){
		
			$post = $_POST;
			$errors = array();
			$insert = array();
			
			
			if(isset($post['titles']) && !empty($post['titles'])){
				$insert['title'] = Str::encode($post['titles']);
			} else {
				$errors[] = 'Geen titel ingevuld';
			}
			
			if(isset($post['meta_tags']) && !empty($post['meta_tags'])){
				$insert['meta_tags'] = $post['meta_tags'];
			} else {
				$errors[] = 'Geen tags ingevuld';
			}
			
			if(isset($post['meta_description']) && !empty($post['meta_description'])){
				$insert['meta_description'] = $post['meta_description'];
			} else {
				$errors[] = 'Geen Google omschrijving ingevuld';
			}
			
			
			if(isset($post['text']) && !empty($post['text'])){
				$insert['text'] = $post['text'];
			} else {
				$errors[] = 'Geen tekst ingevuld';
			}
			
			if(isset($errors) && !empty($errors)){
				
				$this->data['errors'] = $errors;
				$this->data['post'] = $post;
				
			}else{
				
				$insert['pos'] = $this->page_model->get_highest_pos() + 1;
				
				$this->page_model->add($insert);
				
				$this->session->set('success', 'Pagina aangemaakt.');
				$this->redirect('admin/page');
			
			}
		
		}
		
	}
	
	
	public function page_edit($id = null) {
		$this->data['page'] = 'admin_edit';
		// submit form
		if(isset($_POST) && !empty($_POST)){
		
			$post = $_POST;
			$errors = array();
			$update = array();
			
			$id = $post['id'];
			
			
			if(isset($post['titles']) && !empty($post['titles'])){
				$update['title'] = Str::encode($post['titles']);
			} else {
				$errors[] = 'Geen titel ingevuld';
			}
			
			if(isset($post['meta_tags']) && !empty($post['meta_tags'])){
				$update['meta_tags'] = $post['meta_tags'];
			} else {
				$errors[] = 'Geen tags ingevuld';
			}
			
			if(isset($post['meta_description']) && !empty($post['meta_description'])){
				$update['meta_description'] = $post['meta_description'];
			} else {
				$errors[] = 'Geen Google omschrijving ingevuld';
			}
			
			
			if(isset($post['text']) && !empty($post['text'])){
				$update['text'] = $post['text'];
			} else {
				$errors[] = 'Geen tekst ingevuld';
			}
			
			if(isset($errors) && !empty($errors)){
				
				$this->data['errors'] = $errors;
				$this->data['post'] = $post;
				
			}else{
				
				$this->page_model->update($update, $id);
				
				$this->session->set('success', 'Wijzigingen opgeslagen.');
				$this->redirect('admin/page');
			
			}
		
		}else{
			
			$page_data = $this->page_model->get_by_id($id);
			$this->data['page_data'] = $page_data[0];
			$this->data['view'] = 'admin/page/form';
			
		}
		
		
	}
	
	public function page_delete($id = null){
		
		$this->page_model->delete($id);
		
		$this->session->set('success', 'Pagina verwijderd.');
		$this->redirect('admin/page');
	}

}