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

ini_set('display_errors', 0);

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
  $pad = str_repeat(" ", 50-strlen($name)-strlen($num));

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
    return "Abstract Dummy Object";  
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
    return "Dummy Object";
  }
}

/**
 * Includes
 */
include_once 'sessions/Session.php';
include_once 'sessions/SessionWriter.php';
include_once 'sessions/session_writers/FileSessionWriter.php';
include_once 'sessions/session_writers/PDOSessionWriter.php';

/**
 * Benchmarks
 */

function sessionwrite($n) {
  $i = -1;
  while ($i++ < $n) {
    $_SESSION[$i] = array('some_value' => $i, 'a_fixed_value' => 'some_value');
    $_SESSION[$i << 1] = new DummyObject();
  }
}

function sessionread($n) {
  $i = -1;
  while ($i++ < $n) {
    $f = $_SESSION[$i];
    $g = $_SESSION[$i << 1];
  }
}

function sessionstart() {
  session_start();
}


function filesessionstart() {
  Session::start(new FileSessionWriter());
}

function filesessionwrite($n) {
  $i = -1;
  while ($i++ < $n) {
    Session::set($i, array('some_value' => $i, 'a_fixed_value' => 'some_value'));
    Session::set($i << 1, new DummyObject());
  }
}

function filesessionread($n) {
  $i = -1;
  while ($i++ < $n) {
    $f = Session::get($i);;
    $g = Session::get($i << 1);;
  }
}

function pdosessionstart() {
  Session::start(new PDOSessionWriter(new PDO(":dsn", ":uname", ":pword")));
}

function pdosessionwrite($n) {
  $i = -1;
  while ($i++ < $n) {
    Session::set($i, array('some_value' => $i, 'a_fixed_value' => 'some_value'));
    Session::set($i << 1, new DummyObject());
  }
}

function pdosessionread($n) {
  $i = -1;
  while ($i++ < $n) {
    $f = Session::get($i);;
    $g = Session::get($i << 1);;
  }
}

/**
 * Test functions
 */

function phpsessreadtests() {
  $t0 = $t = start_test();
  sessionstart();
  $t = end_test($t, "sessionstart()");
  sessionread(20000);
  $t = end_test($t, "sessionread(20000)");
  total($t0, "Total");
}

function phpsesswritetests() {
  $t0 = $t = start_test();
  sessionstart();
  $t = end_test($t, "sessionstart()");
  sessionwrite(20000);
  $t = end_test($t, "sessionwrite(20000)");
  total($t0, "Total");
}

function filesessionwritetests() {
  $t0 = $t = start_test();
  filesessionstart();
  $t = end_test($t, "filesessionstart()");
  filesessionwrite(20000);
  $t = end_test($t, "filesessionwrite(20000)");
  total($t0, "Total");
}

function filesessionreadtests() {
  $t0 = $t = start_test();
  filesessionstart();
  $t = end_test($t, "filesessionstart()");
  filesessionread(20000);
  $t = end_test($t, "filesessionread(20000)");
  total($t0, "Total");
}

function pdosessionwritetests() {
  $t0 = $t = start_test();
  pdosessionstart();
  $t = end_test($t, "pdosessionstart()");
  pdosessionwrite(20000);
  $t = end_test($t, "pdosessionwrite(20000)");
  total($t0, "Total");
}

function pdosessionreadtests() {
  $t0 = $t = start_test();
  pdosessionstart();
  $t = end_test($t, "pdosessionstart()");
  pdosessionread(20000);
  $t = end_test($t, "pdosessionread(20000)");
  total($t0, "Total");
}

/**
 * Run tests
 */
if (isset($_GET['phpsess']) && isset($_GET['write'])) {
  phpsesswritetests();
} else if (isset($_GET['phpsess']) && isset($_GET['read'])) {
  phpsessreadtests();
  session_unset();
} else if (isset($_GET['file']) && isset($_GET['write'])){
  filesessionwritetests();
} else if (isset($_GET['file']) && isset($_GET['read'])) {
  filesessionreadtests();
} else if (isset($_GET['pdo']) && isset($_GET['read'])) {
  pdosessionreadtests();
} else if (isset($_GET['pdo']) && isset($_GET['write'])) {
  pdosessionwritetests();
} else {


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?phpsess&write');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>PHP SESS WRITE <br>";
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?phpsess&read');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>PHP SESS READ<br>";
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?file&write');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>FILE SESS WRITE<br>";
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?file&read');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>FILE SESS READ<br>";
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?pdo&write');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>PDO SESS WRITE<br>";
curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST'] . '/?pdo&read');
curl_setopt($ch, CURLOPT_HEADER, 0);
echo "<br>PDO SESS READ<br>";
curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);
}