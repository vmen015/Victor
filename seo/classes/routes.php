<?php

class routes {

	public $uri_segments			= array('home', 'index');
	public $permitted_uri_chars		= 'a-z 0-9~%.:_\-';

	/**
	 * Create a URI string from $_SERVER values
	 */
	public function __construct($default_route = null) {
        if (isset($default_route) && !empty($default_route)) {
            $this->uri_segments = $default_route;
        }
		//The SERVER values to look for the path info in
		foreach(array('PATH_INFO', 'REQUEST_URI', 'ORIG_PATH_INFO') as $item) {

			//Try the REQUEST_URI
			if(empty($_SERVER[$item])) {
				continue;
			}

			// Remove the start/end slashes
			$string = trim($_SERVER[$item], '\\/');

			//If it is NOT a forward slash
			if(SITE_URL != '/') {
				// Remove the site path -ONLY ONE TIME!
				$string = preg_replace('/^'. preg_quote(trim(SITE_URL, '\\/'), '/'). '(.+)?/i', '', $string, 1);
			}

			//Remove the INDEX.PHP file from url
			$string = str_replace('index.php', '', $string);

			//If anything is left
			if($string) {
				break(1);
			}
		}
		
		//Clean and separate the URI string into an array
		$segments = explode('/', $string);

		foreach($segments as $key => $segment) {

			//Delete Bad Charaters from URI
			// $segment = preg_replace('/[^'. preg_quote($this->permitted_uri_chars). ']+/i', '', $segment);

			//If anything is left - add it to our array (allow elements that are ZERO)
			if($segment || $segment === '0') {
				$this->uri_segments[$key] = $segment;
			}
		}
	}

	// Returns the URI array element matching the key
	public function fetch($type=null) {
		//Only return it if it exists
		if (is_int($type) && isset($this->uri_segments[$type])) {
			return $this->uri_segments[$type];
		}
	}
}