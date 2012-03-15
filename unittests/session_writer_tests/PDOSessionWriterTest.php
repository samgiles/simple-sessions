<?php
include_once ('SessionWriterTest.php');
include_once (dirname(__FILE__) . '/../../sessions/session_writers/PDOSessionWriter.php');
include_once('DummyObject.php');


class PDOSessionWriterTest extends SessionWriterTest {
  
  public function __construct() {
  	$pdo = new PDO('\:dsn', '\:uname', '\:pword');
  	parent::__construct(new PDOSessionWriter($pdo));
  }
  
  public function testWrite() {
    $testObjects = array(
    		'A' => array('a' => 0, 'b' => 1, 'c' => 2),
    		'B' => array('a' => 2, 'b' => 1, 'c' => 0),
    		'C' => array('obj' => new DummyObject())
    	);
    $this->_writer->write('A', $testObjects['A']);
    $this->assertTrue($this->hasWrote('A', $testObjects['A']), "A");
    
    $this->_writer->write('B', $testObjects['B']);
    $this->assertTrue($this->hasWrote('B', $testObjects['B']), "B");
    
    $this->_writer->write('C', $testObjects['C']);
    $this->assertTrue($this->hasWrote('C', $testObjects['C']), "C");
    
    return $testObjects;
  }
  
  /**
   * @depends testWrite
   */
  public function testClear(array $testObjects) {
  	$this->assertTrue($this->hasWrote('A', $testObjects['A']), "A");
    $this->_writer->clear('A');
    $this->assertTrue($this->hasRemoved('A', $testObjects['A']), "A");
    
    $this->assertTrue($this->hasWrote('B', $testObjects['B']), "B");
    $this->_writer->clear('B');
    $this->assertTrue($this->hasRemoved('B', $testObjects['B']), "B");
    
    $this->assertTrue($this->hasWrote('C', $testObjects['C']), "C");
    $this->_writer->clear('C');
    $this->assertTrue($this->hasRemoved('C', $testObjects['C']), "C");
  }
  
  /**
   * Tests whether the array was written to the database.  Also tests the read functionality.
   * @param string $key
   * @param array $array
   */
  public function hasWrote($key, $value) {
    $object = $this->_writer->read($key);
   
    if (!$object || $object === NULL) {
      return false;
    }
 
    return $object == $value;

  }
  
  public function hasRemoved($key, $value) {
    return !$this->hasWrote($key, $value);
  }
}