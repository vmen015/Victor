<?php 

// custom outline include function 
// usage in tpl :
// {include view='views/mydir/errors' data=get_defined_vars()}  <= get_defined_vars() gets all defined vars at that moment and notice no extension on the view filename !
function outline_function_include($args) {
    $data = (isset($args['data']) && !empty($args['data'])) ? $args['data'] : $GLOBALS ;
    $o = new mmoutline($args['view'], $data);
    $o->render();
} 

class mmoutline {
  
  /*
  A tiny "template engine" for testing purposes.
  */
  
  protected static $engine;
  
  protected $template_path;
  protected $compiled_path;
  
  public $views_path;   //mmweb
  public $data = array();

  
  public function __construct($tpl, $data = array()) {
// echo 'tpl = '.$tpl;      
    // $this->template_path = dirname(__FILE__).'/templates/'.$tpl.'.php';
    // $this->compiled_path = dirname(__FILE__).'/compiled/'.$tpl.'.tpl.php';
    
        if (!isset($engine)) {
            mmoutline::init();
        }

        $this->data = $data;
        $finfo = pathinfo($tpl.'.php');
        $this->template_path = $tpl.'.php';
        // must be full path on system:        
        $this->compiled_path = SITE_DIR.$finfo['dirname'].'/_compiled/'.$finfo['filename'].'.tpl.php';        
  }
  
  public function getTest() {
    return "this message comes from the OutlineTest test class";
  }
  
/*
  public function includeView($path, $data = array()) {
      $file = Kohana::find_file('views',$path);
      $o = new MMwebOutline($file);
      return $o->render($data);
  }*/
  
  public function render($extract = true) {
    if (defined('RECOMPILE')) { 
	self::$engine->compile(
      $this->template_path,
      $this->compiled_path,
      true // force recompile
    );
	}
// echo $this->compiled_path;
    extract($this->data);        
    require self::$engine->load($this->compiled_path);
  }  
  
/*
  public function render($_vars = array()) {
    if (defined('RECOMPILE')) self::$engine->compile(
      $this->template_path,
      $this->compiled_path,
      true // force recompile
    );
    extract($_vars);
    require self::$engine->load($this->compiled_path);
  }
*/
  
  public static function trace($msg) {
    echo "<div style=\"color:#f00\"><strong>Outline</strong>: $msg</div>";
  }
  
  public static function init() {
    // self::$engine = new Outline(array(
    //   'trace_callback' => array(__CLASS__, 'trace')
    // ));
    self::$engine = new Outline(array(
        'quiet' => true, 'plugins' => array('system', 'custom')
    ));
  }
  
}