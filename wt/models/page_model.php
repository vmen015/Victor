<?php

class page_model extends model {

	public function __construct($config = null) {
    
    	parent::__construct($config);
        $this->session = session::instance($config);
    
	}
	
	
	public function get($id = null, $fields = null){
		if(!$fields){
			$fields = ' * ';
		}
		
		if(isset($id) && !empty($id)){

			$sql = ' SELECT '. $fields .' FROM pages WHERE id = "'. $id .'" ';
			return $this->db->run($sql);	
			 
		}else { 
			
			
			$sql = ' SELECT '. $fields .' FROM pages';
			return $this->db->run($sql);
		}
	}
	
	
	public function get_by_title($title){
		
		if(isset($id) && !empty($title)){
		
			$sql = 'SELECT * FROM pages WHERE title = '. $title;
			return $this->db->run($sql);
		
		}else {
			return false;
		}
	}
	
	
	public function get_menu($id = null){
		
		if(!isset($id) && empty($id)){
			
			$sql = 'SELECT title FROM pages ORDER BY pos ASC';
			return $this->db->run($sql);
			
		}
	}
	
	public function add($insert){
		
		return $this->db->insert('pages', $insert);
		
	}
	
	public function update($update, $id){
		if($update == null){
			return false;			
		}else{
			$update = $this->db->update("pages", $update, 'id='.(int)$id);
		}
	}
	
	public function delete($id){
		
		$sql = 'DELETE FROM pages WHERE id ='.$id;
		return $this->db->run($sql);
			
	}
	
	
	
	// PAGE POSITIONS 
	public function get_highest_pos(){
		
		$sql = 'SELECT MAX(pos) FROM pages';
		$result = $this->db->run($sql);

		return $result[0]['MAX(pos)'];
	}
	
	public function update_pos($direction, $old_pos){
		
		if($direction == 'up'){ $new_pos = (int)$old_pos - 1; }
		if($direction == 'down'){ $new_pos = (int)$old_pos + 1; }
		
		// get id from other page
		$other_page_id = ' SELECT id FROM pages WHERE pos = '. $new_pos ;
		$result_other_page_id = $this->db->run($other_page_id);
		$id = $result_other_page_id[0]['id'];

		// finaly update the page with the new position
		$sql = ' UPDATE pages SET pos = '. $new_pos .' WHERE pos = '. $old_pos;
		$this->db->run($sql);
		
		// update other page to new pos
	 	$update_pos_by_id = ' UPDATE pages SET pos = '. $old_pos .' WHERE id = '. $id;
		return $this->db->run($update_pos_by_id);
		
		
	}
	
	
}