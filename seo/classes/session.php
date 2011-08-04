<?php

class session {
    
	public static $instance;

    protected $_data = array();
    
	/**
	 * @var  bool  session destroyed?
	 */
	protected $_destroyed = FALSE;    
    
	/**
	 * Creates a singleton session of the given type. Some session types
	 * (native, database) also support restarting a session by passing a
	 * session id as the second parameter.
	 *
	 *     $session = Session::instance();
	 *
	 * [!!] [Session::write] will automatically be called when the request ends.
	 *
	 * @param   string   type of session (native, cookie, etc)
	 * @param   string   session identifier
	 * @return  Session
	 * @uses    Kohana::config
	 */
	public static function instance($config) {
		if (!isset(session::$instance)) {            
            session::$instance = $session = new session($config);
			// Write the session at shutdown
            register_shutdown_function(array($session, 'write'));
		}

		return session::$instance;
	}
	        
    public function __construct($config, $id = null) {
        $this->config = $config;
		session_set_cookie_params($this->config['cookie']['lifetime'], SITE_URL, $this->config['cookie']['domain'], $this->config['cookie']['secure'], $this->config['cookie']['httponly']);

		// Do not allow PHP to send Cache-Control headers
		session_cache_limiter(FALSE);

		// Set the session cookie name
		session_name($this->config['session']['name']);

		if ($id){
			// Set the session id
			session_id($id);
		}

		// Start the session
		session_start();

		// Use the $_SESSION global for storing data
		$this->_data =& $_SESSION;        		
    }
    
	/**
	 * @return  string
	 */
	public function id() {
		return session_id();
	}    
    
	/**
	 * Get a variable from the session array.
	 *
	 *     $foo = $session->get('foo');
	 *
	 * @param   string   variable name
	 * @param   mixed    default value to return
	 * @return  mixed
	 */
	public function get($key, $default = NULL) {
		return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
	}
	
	/**
	 * Get and delete a variable from the session array.
	 *
	 *     $bar = $session->get_once('bar');
	 *
	 * @param   string  variable name
	 * @param   mixed   default value to return
	 * @return  mixed
	 */
	public function get_once($key, $default = NULL) {
		$value = $this->get($key, $default);

		unset($this->_data[$key]);

		return $value;
	}	
    
	/**
	 * Set a variable in the session array.
	 *
	 *     $session->set('foo', 'bar');
	 *
	 * @param   string   variable name
	 * @param   mixed    value
	 * @return  $this
	 */
	public function set($key, $value) {
		$this->_data[$key] = $value;

		return $this;
	}   
	
	/**
	 * Removes a variable in the session array.
	 *
	 *     $session->delete('foo');
	 *
	 * @param   string  variable name
	 * @param   ...
	 * @return  $this
	 */
	public function delete($key){
		$args = func_get_args();

		foreach ($args as $key) {
			unset($this->_data[$key]);
		}

		return $this;
	}	
    
    public function regenerate() {
		// Regenerate the session id
		session_regenerate_id();

		return session_id();
	}    
	
	/**
	 * Sets the last_active timestamp and saves the session.
	 *
	 *     $session->write();
	 *
	 * [!!] Any errors that occur during session writing will be logged,
	 * but not displayed, because sessions are written after output has
	 * been sent.
	 *
	 * @return  boolean
	 * @uses    Kohana::$log
	 */
	public function write() {
		if (headers_sent() OR $this->_destroyed){
			// Session cannot be written when the headers are sent or when
			// the session has been destroyed
			return FALSE;
		}

		// Set the last active timestamp
		$this->_data['last_active'] = time();

		try {
			return $this->_write();
		} catch (Exception $e) {
			// Log & ignore all errors when a write fails
            // Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e))->write();
			return FALSE;
		}
	}	
	
	/**
	 * @return  bool
	 */
	protected function _write() {
		// Write and close the session
		session_write_close();

		return TRUE;
	}
	
	/**
	 * Completely destroy the current session.
	 *
	 *     $success = $session->destroy();
	 *
	 * @return  boolean
	 */
	public function destroy() {
		if ($this->_destroyed === FALSE){
			if ($this->_destroyed = $this->_destroy()) {
				// The session has been destroyed, clear all data
				$this->_data = array();
			}
		}

		return $this->_destroyed;
	}	

	/**
	 * @return  bool
	 */
	protected function _destroy() {
		// Destroy the current session
		session_destroy();
		// Did destruction work?
		$status = ! session_id();

		if ($status) {
			// Make sure the session cannot be restarted
            // Cookie::delete($this->_name);
		}

		return $status;
	}	
}