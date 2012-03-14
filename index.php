<?php
/**
 * Test Script..
 * 
 * 
 *  --- Benchmarking functions ---
 *  This uses the benckmarking methods used in Official PHP source: php-src/Zend/bench.php
 */
 
if (function_exists("date_default_timezone_set")) {
  date_default_timezone_set("UTC");
}

function getmicrotime() {
  $t = gettimeofday();
  return ($t['sec'] + $t['usec'] / 1000000);
}

function start_test() {
  ob_start();
  return getmicrotime();
}

function end_test($start, $name) {
  global $total;
  $end = getmicrotime();
  ob_end_clean();
  $total += $end-$start;
  $num = number_format($end-$start,3);
  $pad = str_repeat(" ", 24-strlen($name)-strlen($num));

  echo $name.$pad.$num."\n<br>";
	ob_start();
  return getmicrotime();
}

function total() {
  global $total;
  $pad = str_repeat("-", 24);
  echo $pad."\n<br>";
  $num = number_format($total,3);
  $pad = str_repeat(" ", 24-strlen("Total")-strlen($num));
  echo "Total".$pad.$num."\n";
}

/**
 * Dummy Object Abstract
 */
abstract class AbstractDummyObject {
  protected $_protectedVal;
  private $_privateVal;
  public $_publicVal;
	
  function __construct($protected, $private, $public) {
    $this->_protectedVal = $protected;
    $this->_privateVal = $private;
    $this->_publicVal = $public;
  }
	
  function __toString() {
    return "Protected {$this->_protectedVal} - Private {$this->_privateVal} - Public {$this->_publicVal}";  
  }
}

class DummyObject extends AbstractDummyObject {
  protected $_valueA;
  protected $_valueB;
  protected $_valueC;

  public function __construct() {
    parent::__construct("PROTECTED", "PRIVATE", "PUBLIC");
	$this->_valueA = array("test_array" => array("this" => "is", "a" => "test"));
	$this->_valueB = "A VALUE";
	$this->_valueC = 31238;
  }

  public function __toString() {
    return 'Parent Object: ' . parent::__toString() . ", Super Object: A: {$this->_valueA} - B: {$this->_valueB} - C: {$this->_valueC}"; 
  }
}

/**
 * Benchmarks
 */

function sessionwrite($n) {
  $i = -1;
  while ($i++ < $n) {
    if (!isset($_SESSION[$i])) {
      $_SESSION[$i] = array('some_value' => $i, 'a_fixed_value' => 'some_value');
      $_SESSION[$i << 1] = new DummyObject();
    }
  }
}

function sessionread($n) {
  $i = -1;
  while ($i++ < $n) {
    if (isset($_SESSION[$i])) {
      $f = $_SESSION[$i];
      $g = $_SESSION[$i << 1];
    }
  }
}

/**
 * Test functions  - ?phpsess  <-  QS php sessions
 */

function readtests() {
  $t0 = $t = start_test();
  sessionread(20000);
  $t = end_test($t, "sessionread(20000)");
  total($t0, "Total");
}

function writetests() {
  $t0 = $t = start_test();
  sessionwrite(10000);
  $t = end_test($t, "sessionwrite(20000)");
  total($t0, "Total");
}


/**
 * Run tests
 */
if (isset($_GET['phpsess']) && isset($_GET['write'])) {
  session_start();
  writetests();
} else if (isset($_GET['phpsess']) && isset($_GET['read'])) {
  session_start();
  readtests();
  session_unset();
} else {
  echo <<<NOTICE
<p>Usage:</p>
<p>WRITE ensures that \$_SESSION has variables in to be benchmarked with READ tests. READ unsets all \$_SESSION variables</p>
<p><a href="./?phpsess&write">PHP SESSIONS -> WRITE TESTS</a></p>
<p><a href="./?phpsess&read">PHP SESSIONS -> READ TESTS</a></p>
NOTICE;
}