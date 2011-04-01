<?php
/* util.lib.php
 * PHP utilities
 *
 * $Id$
 */

function isPost() { return ($_SERVER['REQUEST_METHOD'] == 'POST'); }
function isPostData() {
  return isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['HTTP_CONTENT_LENGTH'])
    && $_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_CONTENT_LENGTH'] > 0;
} 
function isSess($id) { return isset($_SESSION[$id]); }
function sess($id,$val=NULL) {
  if (func_num_args()==1) {
    return (isSess($id)?$_SESSION[$id]:NULL);
  } elseif (is_null($val)) {
      unset($_SESSION[$id]);
  } else {
    $prev = sess($id);
    $_SESSION[$id] = $val;
    return $prev;
  }
}

function newQS($key, $val=null) { return newQSA(array($key=>$val)); }
function newQSA($array=array()) {
  parse_str($_SERVER['QUERY_STRING'], $arr);
  $s = count($arr);
  foreach($array as $key=>$val) {
    $arr[$key] = $val;
    if (is_null($val))
      unset($arr[$key]);
  }
  return (count($arr)||$s)?'?'.http_build_query($arr):'';
} 

function isHTTPS() { return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'); }

function timings($query=null) {
  global $timingc;
  global $timings;

  if(!isset($timings)) {
    $timings = array();
  }

  if (!isset($timingc) || empty($timingc)) {
    $timingc = 1;
  } elseif (!is_null($query)) {
    $timingc++;
  }
  $key = $timingc;

  if (is_null($query)) {
    $timings[$key]['time'] = microtime(true)-$timings[$key]['time'];
    if (mysql_error())
        $timings[$key]['error'] = mysql_error();
    return true;
  } else {
    $timings[$key] = array();
    $timings[$key]['time'] = microtime(true);
    $timings[$key]['query'] = $query;
    return false;
  }
}

function httpStatusExit($status, $message, $require=null) {
    $status = (string)$status;
    header("HTTP/1.1 $status $message");
    if ($require)
        require_once($require);
    else
        echo "<h1>$status $message</h1>";
    exit;
}

$TAGS = array(array(
    'file' => __FILE__,
    'line' => __LINE__,
    'id' => null,
    'time'=>microtime(true)
));
function TAG($file, $line, $id) {
    global $TAGS;
    $TAGS[] = array(
        'file' => $file,
        'line' => $line,
        'id' => $id,
        'time' => microtime(true)
    );
}
