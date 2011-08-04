<?php
define('START_TIME', microtime(true));

// Absolute file system path to the root
define('SITE_DIR', realpath(dirname(__FILE__)). '/');

error_reporting(E_ALL);
ini_set('display_errors', true);

$config = include_once('config.php');

define('HTTP_DIGEST', false);
$http_digest_users = $config['http_digest_users'];

// Leave '/' unless this site is in a subfolder ('/subfolder/')
// print_r($_SERVER);
if (isset($_SERVER['HTTP_HOST'])) { 
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('SITE_URL', $config['site_url']['localhost']);    
    } else if ($_SERVER['HTTP_HOST'] == 'demo.mediamedics.nl') {
        define('SITE_URL', $config['site_url']['demo']);            
    } else {
        define('SITE_URL', $config['site_url']['default']);    
    }
} else {
    define('SITE_URL', '/');        
}

require_once('classes/routes.php');
require_once('classes/session.php');
require_once('classes/cookie.php');
require_once('classes/controller.php');
require_once('classes/mmcontroller.php');
require_once('classes/mmajaxcontroller.php');
require_once('classes/model.php');

cookie::$path = $config['cookie']['path'];
cookie::$domain = $config['cookie']['domain'];
cookie::$secure = $config['cookie']['secure'];
cookie::$httponly = $config['cookie']['httponly'];


if (!isset($_GET['bypass'])) define('RECOMPILE', true);
try {
	require_once('libraries/outline/classes/outline/engine.php');
} catch (Exception $e) {	
	// Re-throw the exception
	throw $e;
}
require_once('classes/mmoutline.php');

//Load the routes class
$routes = load_class('routes', $config['default_route'], 'classes');

//Get method
$method = $routes->fetch(1);

// Load the controller (or die on failure) and check for matching method
// if(! $controller = load_class($routes->fetch(0), NULL, 'controllers')
//  OR !in_array($method, get_class_methods($controller))) {
//  die(include(SITE_DIR. 'views/404.php'));
// }

// print_r($routes->uri_segments);
// echo '<hr>';

// try controller
if (!$controller = load_class($routes->fetch(0), $config, 'controllers')) {
// echo $routes->uri_segments[0];    
    if ($controller = load_class($config['default_route'][0], $config, 'controllers')) {
        array_unshift($routes->uri_segments, $config['default_route'][0]);
        $method = $routes->fetch(1);
    }    
    if (!$controller) {
        die(include(SITE_DIR.'views/404.php'));
    }
    if (!in_array($method, get_class_methods($controller))) {
// echo 'default = '.$config['default_route'][1];         
        // method not found
        if (in_array($config['default_route'][1], get_class_methods($controller))) {            
            // default method found
            $new = array($config['default_route'][0], $config['default_route'][1]);
            if (isset($routes->uri_segments[2]) && $routes->uri_segments[2] == $config['default_route'][1]) {
                array_pop($routes->uri_segments);
            }
            array_shift($routes->uri_segments);               
            $routes->uri_segments = array_merge($new, $routes->uri_segments);
            $method = $config['default_route'][1];
        } else {
            die(include(SITE_DIR.'views/404.php'));
        }
    }
} else {
// echo 'method = '.$method;    
    if (!in_array($method, get_class_methods($controller))) {
// echo 'default = '.$config['default_route'][1];         
        if (in_array($config['default_route'][1], get_class_methods($controller))) {
            // array_push($routes->uri_segments, $method);     
            $new = array($routes->fetch(0), $config['default_route'][1]);
            array_shift($routes->uri_segments);    
            $routes->uri_segments = array_merge($new, $routes->uri_segments);            
            $method = $config['default_route'][1];
        } else {
            die(include(SITE_DIR.'views/404.php'));
        }
    }
}


// final uri_segments are ready for call_user_func_array
// print_r($routes->uri_segments);

// echo 'controller = '.$controller.' <br />';
// echo 'method = '.$method.' <br />';

// Call the requested method and pass URI segments
// call_user_func_array(array(&$controller, $method), array_slice($routes->uri_segments, 2));

call_user_func_array(array(&$controller, 'before'), array($method));
call_user_func_array(array(&$controller, $method), array_slice($routes->uri_segments, 2));
call_user_func_array(array(&$controller, 'after'), array());
// Done!



/**
* Class registry
*
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
*
* @param	string	class name being requested
* @param	mixed	parameters to pass to the class constuctor
* @param	string	folder to look for class in
* @param	bool	optional flag that lets classes get loaded but not instantiated
* @return	mixed
*/
function load_class($class=null, $params=null, $path='models', $instantiate = TRUE) {

	static $objects = array();
	
	//If this class is already loaded
	if(!empty($objects[$class])) {
		return $objects[$class];
	}
	
	// If the class is not already loaded
	if ( ! class_exists($class)) {
	
		// If the requested file does not exist
		if (!file_exists(SITE_DIR. $path . '/'. $class . '.php')) {
			return FALSE;
		}
		
		//Require the file
		require_once(SITE_DIR. $path . '/'. $class . '.php');
		
	}
	
	//If we just want to load the file - nothing more
	if ($instantiate == FALSE) {
		return TRUE;
	}
	
	return $objects[$class] = new $class(($params ? $params : ''));
}




//-- HTTP DIGEST --------------------------------------------------------
function http_digest_parse($txt) {
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4] ;
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data ;
}

function http_digest($realm = NULL, $username = NULL, $password = NULL){
global $http_digest_users;
// print_r($http_digest_users);
$users = array();

if($username !== NULL AND $password !== NULL){
$users[$username] = $password;
}

    if (isset($http_digest_users) && !empty($http_digest_users)) {
     foreach($http_digest_users as $us => $pw) {
     $users[$us] = $pw;
     }
    }
    // $users['wmadmin'] = 'k4n44lw3g';
    // print_r($users);

$realm = $realm !== NULL ? $realm : 'Restricted Area' ;

if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
     header('HTTP/1.1 401 Unauthorized');
     header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
     die('Unauthorized Access');
}

if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])){
        header('HTTP/1.1 401 Unauthorized');
     header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
     die('Incorrect credentials!');
}

$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response){
     header('HTTP/1.1 401 Unauthorized');
     header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
     die('Incorrect credentials!, this location is only accessible for authorized users');
}
}
