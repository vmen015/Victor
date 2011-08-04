<?php

class str{
	
	public static function encode($str){
		$str = str_replace(" ", "_", trim($str));
		return $str;
	}
	
	public static function decode($str){
		$str = trim(str_replace("_", " ", $str));
		return $str;
	}
	
	public static function clean($str) {
	    $str = self::normalize(trim($str));
		$str = preg_replace("/[^a-zA-Z0-9-_\. ]/",'',$str);		// remove everything which is still no alphanum or dash or underscore or space
		return $str;	    
	}
	
	public static function encode_filename($str, $lowercase = true){
		// normalize accented chars
		$str = self::normalize($str);	
		// lowercase
		if ($lowercase) {
			$str = strtolower($str);		
		}
		
		$str = preg_replace("/[^a-zA-Z0-9-_\.]/",'',$str);		// remove everything which is still no alphanum or dash or underscore
		return $str;
	}
	
	
	public static function normalize ($input) {
	    $table = array(
	        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
	        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
	        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
	        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
	        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
	        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
	        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
	    );
    
	    return strtr($input, $table);
	}
	
	
}