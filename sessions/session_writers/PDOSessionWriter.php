<?php
class PDOSessionWriter extends SessionWriter {
  
  private $_pdoObject;
  private $_pdoReadStatement = NULL;
  private $_pdoWriteStatement = NULL;
  
  private $_inDatabase = false;
  
  public function __construct(PDO $pdoObject) {
    $this->_pdoObject = $pdoObject;
  }

  public function read($hash) {
  	if ($this->_pdoReadStatement === NULL) {
  	  $this->_pdoReadStatement = $this->_pdoObject->prepare("SELECT `SESSION`.`VALUE` FROM `SESSION` WHERE `SESSION`.`KEY`=:hash LIMIT 1;");
  	}
    $this->_pdoReadStatement->bindValue(':hash', $hash, PDO::PARAM_STR);
    $this->_pdoReadStatement->execute();
    $row = $this->_pdoReadStatement->fetch(PDO::FETCH_ASSOC);
    
    // Below we're setting a flag so we know whether to make an INSERT or UPDATE SQL statement when we write the session back in.
    if (!$row) {
      $this->_inDatabase = false;
      return NULL;
    }
    
    $this->_inDatabase = true;
    $object = unserialize($row['VALUE']);
    echo "\nREAD\n";
    print_r($object);
    return $object;
  }

  public function write($hash, $sessionObject) {
    if ($this->_pdoWriteStatement === NULL) {
      if ($this->_inDatabase) {
        $this->_pdoWriteStatement = $this->_pdoObject->prepare("UPDATE `SESSION` `SESSION`.VALUE`=:object WHERE `SESSION`.`KEY`=:hash");
      } else {
        $this->_pdoWriteStatement = $this->_pdoObject->prepare("INSERT INTO SESSION VALUES (:hash, :object)");
      }
    }
    
    $this->_pdoWriteStatement->bindValue(':hash', $hash, PDO::PARAM_STR);
    $object = serialize($sessionObject);
    echo "\nWRITE\n";
    print_r($object);
    $this->_pdoWriteStatement->bindValue(':object', $object, PDO::PARAM_LOB);
    // Tidy up return interfaces.. We dont NEED to return anything but should probably tidy them up.
    return $this->_pdoWriteStatement->execute();
  }
}