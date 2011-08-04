<?php

class controller {
			
	//Singleton instance object
	public static $instance;
		
	//Set instance
	public function __construct($config=null) {
		//Set singleton instance
		self::$instance =& $this;
				
	}	

	public function before() {
		
	}	
	public function after() {

	}
	
	protected function redirect($url, $full = false) {
		$path = '';
		if ($full) {
			$path = $url;
		} else {
			$path = SITE_URL.$url;
		}
		header('Location: '.$path);
	}	
	
	/**
	 * This function is used to load views files.
	 *
	 * @param	String	file path/name
	 * @param	array	values to pass to the view
	 * @param	boolean	return the output or print it?
	 * @return	mixed
	 */
	public function view($__file = NULL, $__variables = NULL, $__return = FALSE) {

		if($__variables) {
			// Make each value passed to this view available for use
			foreach($__variables as $key => $variable) {
				$$key = $variable;
			}
		}

		// Delete them now
		$__variables = null;

		// If the file is not found
		if (!file_exists(SITE_DIR. 'views/'. $__file. '.php')) {
			return FALSE;
		}

		// We just want to print to the screen
		if( ! $__return) {
			include(SITE_DIR. 'views/'. $__file. '.php');
			return;
		}
		
		//Buffer the output so we can return it
		ob_start();

		// include theme file
		include(SITE_DIR. 'views/'. $__file. '.php');

		//Get the output
		$buffer = ob_get_contents();
		@ob_end_clean();

		//Return the view
		return $buffer;
	}
	
	//Return this classes instance
	public static function &get_instance() {
		return self::$instance;
	}
}