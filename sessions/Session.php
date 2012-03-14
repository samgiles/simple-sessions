<?php
class Session {
  private $_objects;
  private $_idHash;
  
  private $_sessionWriter;
  
  private static $instance = NULL;
  
  public static function start(SessionWriter $sessionWriter, $sessionExpires = 0, $sessionPath = '/') {
    if (Session::$instance === NULL) {
  	 /**
  	  * We've got to tie a particular user to a Session, so we'll create a hash for them if we haven't already.
  	  */
      if (($new = (isset($_COOKIE['SESS']) && $_COOKIE['SESS'] !== NULL))) {
        // We must have a session hash key :)
        $sessionHash = $_COOKIE['SESS'];
      } else {
        $sessionHash = hash('sha256', $_SERVER['REMOTE_ADDR'] . time() . rand(0, 100));
        setcookie('SESS', $sessionHash, $sessionExpires, $sessionPath);
      }
      
      Session::$instance = new Session($sessionWriter, $sessionHash, $new);
  	}
  }
  
  private function __construct(SessionWriter $sessionWriter, $hash, $new = false) {
  	$this->_sessionWriter = $sessionWriter;
  	$this->_idHash = $hash;
  	if (!$new) {
      $this->_objects = $sessionWriter->read($hash);
  	} else {
      $this->_objects = array();
  	}
  }
  
  public function __destruct() {
    $this->_sessionWriter->write($this->_idHash, $this->_objects);
  }

  /**
   * Magic method used to retrieve objects stored in the Session.
   * @param $name Returns the value stored in the Session at this key.
   * @param $arguments No effect.
   */
  public function __call($name, $arguments) {
    if (isset($_objects[$name])) {
      return $_objects[$name];
    } else {
      return NULL;
    }
  }

  public static function get($key) {
    if (Session::sessionStarted()) {
      return SESSION::$instance->$key();
    }
    return NULL;
  }

  public static function set($key, $value) {
    if (Session::sessionStarted()) {
      Session::$instance->_objects[$key] = $value;
      return true;
    }
    return false;
  }
  
  private static function sessionStarted() {
    if (Session::$instance === NULL) {
      trigger_error('Session not started, start session with `Session::start($sessionWriter)`', E_USER_NOTICE);
      return false;
    }
    
    return true;
  }
}