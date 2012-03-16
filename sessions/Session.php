<?php
require_once('SessionWriter.php');
/**
 * A Session object. A Session object must be started using the static method `start`.
 * @author Samuel Giles
 * @version 0.1
 */
class Session {
  private static $HTTP_PERSIST_NAME = 'SESS';
  private $_objects;
  private $_idHash;
  
  private $_sessionWriter;
  private $_dontWrite = true;
  
  private static $instance = NULL;
  
  /**
   * Starts a Session given a SessionWriter, this checks for a cookie that can identify a session with a client/request, if one hasn't been started yet then 
   * a cookie will be set.
   * @param SessionWriter $sessionWriter The SessionWriter that is used for reading and writing the sessions.
   * @param int $sessionExpires The time the session expires. This is a Unix timestamp so is in number of seconds since the epoch. If left as 0 or undefined the session will last until the client closes their browser/client.
   * @param string $sessionPath The path on the server in which the session will be available on. If set to '/', the session will be available within the entire domain. If set to '/foo/', the session is only be available within the /foo/ directory and all sub-directories such as /foo/bar/ of domain. The default value is the current directory that the session is started in.
   */
  public static function start(SessionWriter $sessionWriter, $sessionExpires = 0, $sessionPath = -1) {
    if (Session::$instance === NULL) {
  	  //We've got to tie a particular user to a Session, so we'll create a hash for them if we haven't already.
  	  $httpPersistedHash = $sessionWriter->getHttpPersistKey(Session::$HTTP_PERSIST_NAME);
      if ($httpPersistedHash !== NULL) {
        $new = false;
      } else {
        $httpPersistedHash = hash('sha256', $_SERVER['REMOTE_ADDR'] . time() . rand(0, 100));
        $sessionWriter->httpPersist(Session::$HTTP_PERSIST_NAME, $httpPersistedHash, $sessionExpires, $sessionPath === -1 ? $_SERVER['PATH_INFO'] : $sessionPath);
      	$new = true;
      }
      
      Session::$instance = new Session($sessionWriter, $httpPersistedHash, $new);
  	}
  }
  
  private function __construct(SessionWriter $sessionWriter, $hash, $new = false) {
  	$this->_sessionWriter = $sessionWriter;
  	$this->_idHash = $hash;
  	$this->_objects = array();
  	
  	if (!$new) {
      $this->_objects = $sessionWriter->read($hash);
  	}
  }
  
  public function __destruct() {
  	if ($this->_dontWrite === false) {
      $this->_sessionWriter->write($this->_idHash, $this->_objects);
  	}
  }


  public static function get($key) {
    if (Session::sessionStarted()) {
      $key = (string)$key;
      
      if (isset(Session::$instance->_objects[$key])) {
        return Session::$instance->_objects[$key];
      }
      
    }
    return NULL;
  }

  public static function set($key, $value) {
    if (Session::sessionStarted()) {
      Session::$instance->_objects[$key] = $value;
      Session::$instance->_dontWrite = false;
      return true;
    }
    return false;
  }
  
  public static function clear() {
    if (Session::sessionStarted()) {
      $session = Session::$instance;
      $session->_sessionWriter->clear($session->_idHash);
      $session->_sessionWriter->httpClear(Session::$HTTP_PERSIST_NAME);
      $session->_dontWrite = true;
      return true;
    }
    
    return false;
  }
  
  public static function sessionStarted() {
    if (Session::$instance === NULL) {
      trigger_error('Session not started, start session with `Session::start($sessionWriter)`', E_USER_NOTICE);
      return false;
    }
    
    return true;
  }
}