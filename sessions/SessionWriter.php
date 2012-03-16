<?php
abstract class SessionWriter {
  /**
   * Reads a session into the session object.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function read($hash) {
  }
  
  /**
   * Writes a session object.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function write($hash, $sessionObject) {
  }
  
  /**
   * Clears a session.
   * @param $hash The hash/key of the sessions.
   * Virtual Function, implementation would need to be defined in the concrete super class.
   */
  public function clear($hash) {
  }
  
  /**
   * Persists the session across HTTP requests.  Default is setcookie.
   */
  public function httpPersist($keyIdentifier, $sessionHash, $sessionExpires, $sessionPath) {
    setcookie($keyIdentifier, $sessionHash, $sessionExpires, $sessionPath === -1 ? $_SERVER['PATH_INFO'] : $sessionPath);
  }
  
  public function httpClear($keyIdentifier) {
    unset($_COOKIE[$keyIdentifier]);
    setcookie($keyIdentifier, NULL, -1);
  }
  
  public function getHttpPersistKey($keyIdentifier) {
  	if (isset($_COOKIE[$keyIdentifier])) {
  	  return $_COOKIE[$keyIdentifier];
  	}
  	
    return NULL;
  }
}