<?php
include_once ('SessionWriterTest.php');
include_once (dirname(__FILE__) . '/../../sessions/session_writers/FileSessionWriter.php');

class DummyObject {
  public $a = "Test";
  public $b = "Test2";
  public $c = "Test3";
  public $d = array("A" => "Test4");
}

class FileSessionWriterTest extends SessionWriterTest {
  
  public function __construct() {
  	parent::__construct(new FileSessionWriter($_SERVER["HOME"] . '/session_tests'));
  }
  
  public function testWrite() {
  	try {
  	  unlink($_SERVER["HOME"] . '/session_tests/SESS_A.s');
  	  unlink($_SERVER["HOME"] . '/session_tests/SESS_B.s');
  	  unlink($_SERVER["HOME"] . '/session_tests/SESS_C.s');
  	} catch(Exception $e) {
  	
  	}
  	
    $testObjects = array(
    		'A' => array('a' => 0, 'b' => 1, 'c' => 2),
    		'B' => array('a' => 2, 'b' => 1, 'c' => 0),
    		'C' => array('obj' => new DummyObject())
    	);
    $this->_writer->write('A', $testObjects['A']);
    $this->_writer->write('B', $testObjects['B']);
    $this->_writer->write('C', $testObjects['C']);
    
    $this->assertFileExists($_SERVER["HOME"] . '/session_tests/SESS_A.s');
    $this->assertFileExists($_SERVER["HOME"] . '/session_tests/SESS_B.s');
    $this->assertFileExists($_SERVER["HOME"] . '/session_tests/SESS_C.s');
    return $testObjects;
  }
  
  /**
   * @depends testWrite
   */
  public function testRead(array $testObjects) {
  	$object = $this->_writer->read('A');
  	
  	$this->assertArrayHasKey('a', $object);
  	$this->assertArrayHasKey('b', $object);
  	$this->assertArrayHasKey('c', $object);
  	$this->assertEquals($object['a'], 0);
  	$this->assertEquals($object['b'], 1);
  	$this->assertEquals($object['c'], 2);
  	
  	$object = $this->_writer->read('B');
  	$this->assertArrayHasKey('a', $object);
  	$this->assertArrayHasKey('b', $object);
  	$this->assertArrayHasKey('c', $object);
  	$this->assertEquals($object['a'], 2);
  	$this->assertEquals($object['b'], 1);
  	$this->assertEquals($object['c'], 0);
  	
  	$object = $this->_writer->read('C');
  	$this->assertArrayHasKey('obj', $object);
  }
}