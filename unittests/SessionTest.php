<?php
include_once (dirname(__FILE__) . '/../sessions/Session.php');

class MockSessionWriter extends SessionWriter {
  public function httpPersist($keyIdentifier, $sessionHash, $clear, $sessionExpires, $sessionPath) {
  	// ok we set it :p
  	return true;
  }
  
  public function getHttpPersistKey($keyIdentifier) {
  	return 'mockhashmockhashmockhashmockhashmockhashmockhashmockhashmockhash';
  }
}

class SessionTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
  	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
  	$_SERVER['PATH_INFO'] = '/';
    Session::start(new MockSessionWriter());
    $this->assertTrue(Session::sessionStarted(), "Session didn't start in setUp()");
  }
  
  public function testSessionGetSet() {
    $a = "Some String";
    Session::set('a', $a);
    $this->assertTrue(Session::get('a') === $a);
    
    $b = array('some' => array('complex' => 'array'));
    Session::set('b', $b);
    $this->assertTrue(Session::get('b') == $b);
  }
}