<?php

class mmdb extends db {
    
    public function __construct($dsn, $user="", $passwd="") {
        parent::__construct($dsn, $user="", $passwd="");
    }
    
    // overloaded select method to allow for different where types    
    public function select($table, $where = '', $bind = '', $fields = '*') {
        $sql = "SELECT " . $fields . " FROM " . $table;
// echo 'where = '.$where;
// echo (int)$where;        
        if ($where != '' && $where !== 0) {
            if (is_int($where) || is_numeric($where)) {
                $sql_where = ' WHERE id='.$where;
            } else if (is_array($where) && !empty($where)) {
                $sql_where = ' WHERE ';                
                $i = 0;
                foreach($where as $key => $value) {
                    if ($i > 0) {          
                        $sql_where .= ' AND ';
                    }                    
                    if (is_string($value)) {
                        $sql_where .= $key.' = "'.$value.'"';                        
                    } else {
                        $sql_where .= $key.' = '.$value;
                    }
                    $i++;
                }
            } else if (is_string($where)) {   
                $sql_where = ' WHERE '.$where;
            }
            $sql .= $sql_where;
        }
        $sql .= ';';
// echo $sql;        
        return $this->run($sql, $bind);
    }
    
    // overloaded update method to allow for different where types    
	public function update($table, $info, $where, $bind = '') {
		$fields = $this->filter($table, $info);
		$fieldSize = sizeof($fields);

		$sql = "UPDATE " . $table . " SET ";
		for($f = 0; $f < $fieldSize; ++$f) {
			if($f > 0)
				$sql .= ", ";
			$sql .= $fields[$f] . " = :update_" . $fields[$f]; 
		}
		
        // $sql .= " WHERE " . $where . ";";
        if ($where != '' && $where !== 0) {
            if (is_int($where) || is_numeric($where)) {
                $sql_where = ' WHERE id='.$where;
            } else if (is_array($where) && !empty($where)) {
                $sql_where = ' WHERE ';                
                $i = 0;
                foreach($where as $key => $value) {
                    if ($i > 0) {          
                        $sql_where .= ' AND ';
                    }                    
                    if (is_string($value)) {
                        $sql_where .= $key.' = "'.$value.'"';                        
                    } else {
                        $sql_where .= $key.' = '.$value;
                    }
                    $i++;
                }
            } else if (is_string($where)) {   
                $sql_where = ' WHERE '.$where;
            }
            $sql .= $sql_where;
        }        

		$bind = $this->cleanup($bind);
		foreach($fields as $field)
			$bind[":update_$field"] = $info[$field];
		
		return $this->run($sql, $bind);
	}
    
    // overloaded delete method to allow for different where types
	public function delete($table, $where, $bind = '') {
	    $sql = 'DELETE FROM '.$table.' ';
	    if ($where != '' && $where !== 0) {
	        if (is_int($where) || is_numeric($where)) {
	            $sql_where = ' WHERE id='.$where;
            } else if (is_array($where) && !empty($where)) {
                $sql_where = ' WHERE ';
                $i = 0;
                foreach($where as $key => $value) {
                    if ($i > 0) {          
                        $sql_where .= ' AND ';
                    }                    
                    if (is_string($value)) {
                        $sql_where .= $key.' = "'.$value.'"';                        
                    } else {
                        $sql_where .= $key.' = '.$value;
                    }
                    $i++;                    
                }
            } else if (is_string($where)) {
                $sql_where = ' WHERE '.$where;
            }
            $sql .= $sql_where;
	    }
	    $sql .= ';';
		return $this->run($sql, $bind);
	}
    
    public function get_row() {
        
    }
    
    public function get_rows() {
        
    }
    
	public function selectparentold($table, $where="", $bind="", $fields="*") {
		$sql = "SELECT " . $fields . " FROM " . $table;
		if(!empty($where)){
			$sql .= " WHERE " . $where;
		}
		$sql .= ";";
		return $this->run($sql, $bind);
	}    
    
}