<?php
class FileSessionWriter extends SessionWriter {

  private $_sessionSavePath;	
	
  public function __construct() {
    $this->_sessionSavePath = session_save_path();
  }
  
  public function read($hash) {
    $object = file_get_contents("{$this->_sessionSavePath}/SESS_$hash.s");
    
    if (!$object) {
      trigger_error("Couldn't open the requested session from the file.", E_WARNING);
      return false;
    }
    
    return json_decode($object);
  }

  public function write($hash, $sessionObject) {
    return file_put_contents("{$this->_sessionSavePath}/SESS_$hash.s", $sessionObject);
  }

}