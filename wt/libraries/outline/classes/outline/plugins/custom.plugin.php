<?php

class OutlinePlugin_custom extends OutlinePlugin {

	public function require_tag($args) {
		$this->compiler->code("require_once '$args';");
	}
	
	protected $include_num = 0;
	
    // public function include_tag($args) {
	    /*
echo 'INCLUDE: ';      
echo $args;      
		$tplname = trim($args);
		if (substr($tplname,0,1) != '$') $tplname = "'$tplname'";		
		$var = '$outline_include_' . ($this->include_num++);
echo $tplname;
		$this->compiler->code("$var = new Outline($tplname); require {$var}->get();");
		*/
        // $tplname = trim($args);
        // if (substr($tplname,0,1) != '$') {
        //     $tplname = "'$tplname'";     
        //      }
        //         echo $tplname;
        //         $this->compiler->code("");
        // echo $args;
        // print_r($data);
        // exit;
        // $this->compiler->code("");
		
    // }

    // --- Plugin registration:
    public static function register(&$compiler) {
        $compiler->registerTag('require', 'require_tag');
        // $compiler->registerTag('include', 'include_tag');
    }
	
}