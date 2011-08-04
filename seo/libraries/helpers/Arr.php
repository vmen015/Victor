<?php

class Arr {
    
    
    public static function multisort($theArray, $column, $sortdir = SORT_ASC, $sorttype = SORT_REGULAR) {
    	$sortby = array();
        $tn = $ts = $temp_num = $temp_str = array();    	
    	if (is_string($sortdir)) {
    		if (stripos($sortdir, 'asc') !== false) {
    			$sortdir = SORT_ASC;
    		} 
    		if (stripos($sortdir, 'desc' ) !== false) {
    			$sortdir = SORT_DESC;
    		}	
    		if (stripos($sortdir, 'random' ) !== false) {
    			$sortdir = 'random';
    		}    		
    	}
    	if (is_string($sorttype)) {
    	    if (stripos($sorttype, 'regular') !== false) {
    	        $sorttype = SORT_REGULAR;
    	    }
    	    if (stripos($sorttype, 'num') !== false) {
    	        $sorttype = SORT_NUMERIC;
    	    }
    	    if (stripos($sorttype, 'string') !== false) {
    	        $sorttype = SORT_STRING;
    	    }   
    	    if (stripos($sorttype, 'natural') !== false) {
    	        $sorttype = 'natural';
    	    }    	     	   
    		if (stripos($sorttype, 'random' ) !== false) {
    			$sortdir = 'random';
    		}    	             
	    }
	    if ($sortdir != 'random') {
	        if ($sorttype == 'natural') {
            	foreach ($theArray as $key => $row) {
            	    if (is_numeric(substr($row[$column], 0, 1))) {
            	        $tn[$key] = $row[$column];
            	        $temp_num[$key] = $row;
            	    } else {
            	        $ts[$key] = $row[$column];
            	        $temp_str[$key] = $row;
            	    }
            	}
                array_multisort($tn, $sortdir, SORT_NUMERIC, $temp_num); 
                array_multisort($ts, $sortdir, SORT_STRING, $temp_str);
                // print_r($temp_num);
                // print_r($temp_str);
                return array_merge($temp_num, $temp_str);    
            } else {
            	foreach ($theArray as $key => $row) {                
                    $sortby[$key] = $row[$column];                  
        	    }
                array_multisort($sortby, $sortdir, $sorttype, $theArray);               
                return $theArray;
            }        
    	} else {
	        shuffle($theArray);
        	return $theArray;	        
    	}

    }	
    
    public static function sort_col($table, $colname, $dir = SORT_ASC) {
        $tn = $ts = $temp_num = $temp_str = array();
        foreach ($table as $key => $row) {
            if(is_numeric(substr($row[$colname], 0, 1))) {
                $tn[$key] = $row[$colname];
                $temp_num[$key] = $row;
            } else {
                $ts[$key] = $row[$colname];
                $temp_str[$key] = $row;
            }
        }
        unset($table);

        array_multisort($tn, $dir, SORT_NUMERIC, $temp_num); 
        array_multisort($ts, $dir, SORT_STRING, $temp_str);
        return array_merge($temp_num, $temp_str);
    }
    
    // merges assoc arrays recursive with overwrite of elements with same key string
    public static function array_merge_recursive_overwrite($Arr1, $Arr2) {
        foreach($Arr2 as $key => $Value) {
            if(array_key_exists($key, $Arr1) && is_array($Value)) {
                $Arr1[$key] = Arr::array_merge_recursive_overwrite($Arr1[$key], $Arr2[$key]);
            } else {
                $Arr1[$key] = $Value;
            }
      }
      return $Arr1;
    }    
    
    
}