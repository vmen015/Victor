<?php

/*

Outline (Engine)
----------------

Copyright (C) 2007-2009, Rasmus Schultz <http://www.mindplay.dk>

Please see "README.txt" for license and usage information.
  
*/

class OutlineException extends Exception {
  
  /*
  General exception thrown by Outline classes.
  */
  
  public function __construct($message) {
    parent::__construct($message, -1);
  }
  
}

class Outline {
  
  /*
  This is the core engine class, which provides functions to compile
  and load/render Outline templates.
  */
  
  protected $config = array(
    "trace_callback" =>      null,             /* Callback function for engine trace messages (optional) */
    "quiet" =>               true,             /* Suppresses E_NOTICE and E_WARNING error messages */
    "file_mode" =>           0777,
    "dir_mode" =>            0777,
    "plugins" =>             array('system'),
    "bracket_open" =>        '{',
    "bracket_close" =>       '}',
    "bracket_comment" =>     null,
    "bracket_end_comment" => null,
    "bracket_ignore" =>      null,
    "bracket_end_ignore" =>  null,
  );
  
  public function __construct($config = null) {
    
    /*
    $config: optional array of engine configuration settings (see $config above)
    */
    
    if (is_array($config)) foreach ($config as $name => $value) {
      if (!array_key_exists($name, $this->config)) {
        throw new OutlineException("Outline::__construct() : invalid configuration option '$name'");
      }
      if (is_array($this->config[$name])) {
        $this->config[$name] += $value;
      } else {
        $this->config[$name] = $value;
      }
    }
    
    if ($this->config['bracket_comment']==null)
      $this->config['bracket_comment'] = $this->config['bracket_open'].'*';
    if ($this->config['bracket_end_comment']==null)
      $this->config['bracket_end_comment'] = '*'.$this->config['bracket_close'];
    if ($this->config['bracket_ignore']==null)
      $this->config['bracket_ignore'] = $this->config['bracket_open'].'ignore'.$this->config['bracket_close'];
    if ($this->config['bracket_end_ignore']==null)
      $this->config['bracket_end_ignore'] = $this->config['bracket_open'].'/ignore'.$this->config['bracket_close'];
    
  }
  
  public function __destruct() {
    foreach ($this as $index => $value) unset($this->$index);
  }
  
  public function getConfig() {
    return $this->config;
  }
  
  public function trace($msg) {
    if (!$this->config['trace_callback']) return;
    call_user_func($this->config['trace_callback'], $msg);
  }
  
  public function compile($template_path, $compiled_path, $force = false) {
    
    /*
    Compiles a template, if the compiled template at the given destination path
    if older than the template file at the given source path.
    
    Returns true if the template was built, false if it was already up-to-date.
    */
    
    if (!file_exists($template_path)) {
      throw new OutlineException("OutlineEngine::compile(): template file not found: {$template_path}");
    }
    
    if ($force || !file_exists($compiled_path) || (filemtime($template_path) > @filemtime($compiled_path))) {
      
      if (!@constant("OUTLINE_COMPILER")) {
        $this->trace("loading compiler");
        require OUTLINE_CLASS_PATH . "/OutlineCompiler.php";
      }
      
      $this->trace("compiling template '$template_path' to '$compiled_path'");
      
      try {
        $compiler = new OutlineCompiler($this);
        @mkdir(dirname($compiled_path), $this->config['dir_mode'], true);
        $source = $compiler->compile($template_path);
        OutlineUtil::write_file($compiled_path, $source, $this->config['file_mode']);
        $compiler->__destruct(); unset($compiler);
      } catch (OutlineCompilerException $e) {
        throw new OutlineException("Outline::compile() : error compiling template '$template_path', line " . $e->getLineNum() . " - " . $e->getMessage());
      }
      
      return true;
      
    }
    
    return false;
    
  }
  
  public function load($compiled_path) {
    
    /*
    Prepares to load and run a compiled template.
    
    This method *must* be used with a require statement - the
    compiled template will run in whatever context you need
    it to, but requires the call to load() in order to prepare
    the runtime environment for the compiled template.
    
    Example:
    
      $test = new Outline();
      require $test->load('my_template.tpl.php');
    */
    
    OutlineRuntime::ready($this, $compiled_path);
    
    return $compiled_path;
    
  }
  
}

class OutlineRuntime {
  
  /*
  This class provides a runtime support stack for compiled templates.
  
  NOTE: You should never manually invoke any method in this class.
  */
  
  protected static $stack = array();
  
  public static function ready(Outline & $outline, $compiled_path) {
    
    /*
    This method is called by Outline::load() to prepare the runtime
    environment for the compiled template.
    */
    
    self::$stack[] = new OutlineRuntime(
      & $outline,
      OutlineUtil::normalize_path($compiled_path)
    );
    
  }
  
  public static function start($compiled_path, $context) {
    
    /*
    This method is called at the beginning of a compiled template - it
    returns the OutlineRuntime instance for use by plugin runtimes.
    */
    
    $runtime = end(self::$stack);
// echo '<hr>';    
// echo $runtime->compiled_path;
// echo '<hr>';    
// echo OutlineUtil::normalize_path($compiled_path);
// echo '<hr>';
    if ($runtime->compiled_path != OutlineUtil::normalize_path($compiled_path)) {
      throw new OutlineException('OutlineRuntime::start() : runtime stack entry mismatch');
    }
    
    if ($runtime->config['quiet']) {
      $runtime->error_level = error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING | E_STRICT ^ E_STRICT);
    }
    
    $runtime->context = $context;
    
    return $runtime;
    
  }
  
  public static function finish($compiled_path) {
    
    /*
    This method is called at the end of a compiled template - it destroys
    the runtime environment of the finished template, and returns the
    previous OutlineRuntime (if present) so that the finishing template
    can restore the parent runtime environment.
    */
    
    $runtime = array_pop(self::$stack);
    
    if ($runtime->compiled_path != $compiled_path) {
      throw new OutlineException('OutlineRuntime::finish() : runtime stack entry mismatch');
    }
    
    if ($runtime->config['quiet']) {
      error_reporting($runtime->error_level);
    }
    
    $runtime->__destruct();
    unset($runtime);
    
    return count(self::$stack) ? end(self::$stack) : null;
    
  }
  
  // --- Runtime API:
  
  public $outline;
  public $compiled_path;
  
  protected $config;
  protected $context;
  protected $error_level;
  
  public function __construct(Outline & $outline, $compiled_path) {
    $this->compiled_path = $compiled_path;
    $this->outline = & $outline;
    $this->config = $outline->getConfig();
  }
  
  public function __destruct() {
    foreach ($this as $index => $value) unset($this->$index);
  }
  
  public function init_runtime($runtime) {
    require_once OUTLINE_PLUGIN_PATH.'/'.$runtime.'.runtime.php';
    $class = 'OutlineRuntime_'.$runtime;
    @$this->$runtime = new $class($this);
  }
  
}

interface IOutlineRuntime {
  
  /*
  This interface is implemented by plugin runtimes.
  */
  
  public function __construct(OutlineRuntime & $runtime);
  
}
