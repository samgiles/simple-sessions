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
 * Benchmarks
 */

function sessionwrite($n) {
  $i = -1;
  while ($i++ < $n) {
    if (!isset($_SESSION[$i])) {
      $_SESSION[$i] = $i;
    }
  }
}

function sessionread($n) {
  $i = -1;
  while ($i++ < $n) {
    if (isset($_SESSION[$i])) {
      $f = $_SESSION[$i];
    }
  }
}

/**
 * Test functions  - ?phpsess  <-  QS php sessions
 */

function readtests() {
  $t0 = $t = start_test();
  sessionread(10000);
  $t = end_test($t, "sessionread(10000)");
  total($t0, "Total");
}

function writetests() {
  $t0 = $t = start_test();
  sessionwrite(10000);
  $t = end_test($t, "sessionwrite(10000)");
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