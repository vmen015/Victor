<?php

/*

sage is dead simple, initiate the class with a path to your cache location and then use JG_Cache::get() and JG_Cache::set() 
to play with your data. Each cache has a key, which you set. 
If you’re caching something with user data, make sure to put their unique ID in it. 
This is Memcached style, really basic. tCache data defaults to last an hour, but that’s flexible in the get() method (the second argument is the number of seconds you want the data to stick around).


$cache = new JG_Cache('/path/to/cache');

$data = $cache->get('key');

if ($data === FALSE)
{
    $data = 'This will be cached';
    $cache->set('key', $data);
}

//Do something with $data


*/


class Cache {

    public function __construct($dir) {
        $this->dir = $dir;
    }

    private function _name($key) {
        return sprintf("%s/%s", $this->dir, sha1($key));
    }

    public function get($key, $expiration = 3600) {

        if ( !is_dir($this->dir) OR !is_writable($this->dir)) {
            return FALSE;
        }

        $cache_path = $this->_name($key);

        if (!@file_exists($cache_path)) {
            return FALSE;
        }

        if (filemtime($cache_path) < (time() - $expiration)) {
            $this->clear($key);
            return FALSE;
        }

        if (!$fp = @fopen($cache_path, 'rb')) {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';

        if (filesize($cache_path) > 0) {
            $cache = unserialize(fread($fp, filesize($cache_path)));
        } else {
            $cache = NULL;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $cache;
    }

    public function set($key, $data) {

        if ( !is_dir($this->dir) OR !is_writable($this->dir)) {
            return FALSE;
        }

        $cache_path = $this->_name($key);

        if ( ! $fp = fopen($cache_path, 'wb')) {
            return FALSE;
        }

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        } else {
            return FALSE;
        }
        fclose($fp);
        @chmod($cache_path, 0777);
        return TRUE;
    }

    public function clear($key) {
        $cache_path = $this->_name($key);

        if (file_exists($cache_path)) {
            unlink($cache_path);
            return TRUE;
        }

        return FALSE;
    }
    
    public function clearall() {
        if ( !is_dir($this->dir) OR !is_writable($this->dir)) {
            return FALSE;
        }
        if ($handle = opendir($this->dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    unlink($this->dir.'/'.$file);
                }
            }
            closedir($handle);
        }                
    }
}
