<?php

require_once('libraries/sqlite/class.db.php');
require_once('classes/mmdb.php');

class model {
	
	public $db;
	public $config;
	
	public function __construct($config = null){
	    if ($config) {
            $this->config = $config;        
    	} else {
    	    $this->config = include_once('config.php');
    	}
    	if ($this->config['db']['driver'] == 'sqlite' && isset($this->config['db']['sqlite_db_file']) && !empty($this->config['db']['sqlite_db_file'])) {
    		$this->db = new mmdb('sqlite:'.$this->config['db']['sqlite_db_file']);
    	}
	}
	
}