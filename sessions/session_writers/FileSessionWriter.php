<?php
class FileSessionWriter extends SessionWriter {

  private $_sessionSavePath;	
	
  public function __construct($path = NULL) {
      $this->_sessionSavePath = ($path === NULL) ? session_save_path() : $path;
  }
  
  public function read($hash) {
    $object = file_get_contents("{$this->_sessionSavePath}/SESS_$hash.s");
    
    if (!$object) {
      trigger_error("Couldn't open the requested session from the file.", E_WARNING);
      return false;
    }
    
    return json_decode($object, true);
  }

  public function write($hash, $sessionObject) {
    return file_put_contents("{$this->_sessionSavePath}/SESS_$hash.s", json_encode($sessionObject));
  }

}