<?php
/* runtime.php
 * cloud common includes
 *
 * $Id$
 */

require_once('runtime.inc.php');

$_base = $_SERVER['SCRIPT_URI'];
$_domain = $_SERVER['SERVER_NAME'];
$_domain_data = $sites->SELECT_p_o("dns:$_domain");

#CRITICAL
$_filebase = $_ENV['CLOUD_DATA'].'/'.$_SERVER['SERVER_NAME'];
$_filename = $_SERVER['SCRIPT_URL'];
$_filename_ext = strrpos($_filename, '.');
$_filename_ext = $_filename_ext ? substr($_filename, 1+$_filename_ext) : '';
if (!strlen($_filename) || $_filename[0] != '/')
    $_filename = "/$_filename";
if ($_filebase != substr($_filename, 0, strlen($_filebase)))
    $_filename = "$_filebase$_filename";

$_request_url = substr($_filename, strlen($_filebase));

header("X-User: $_user");

// CORS
if (!isHTTPS()) {
    header('Access-Control-Allow-Origin: *');
} else {
    header('Access-Control-Allow-Credentials: true');
    $_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    $t = explode('/', $_origin);
    if (count($t) > 2) {
        $_origin = "{$t[0]}//{$t[2]}";
    } else {
        $_origin = '*';
    }
    header('Access-Control-Allow-Origin: '.$_origin);
}

if (in_array('OPTIONS', array($_SERVER['REQUEST_METHOD'], $_SERVER['REDIRECT_REQUEST_METHOD']))) {
    header('HTTP/1.1 200 OK');
    exit;
}

require_once('input.php');
require_once('output.php');
if (in_array($_filename_ext, array('css', 'html', 'js'))) {
    $_input = 'raw';
    $_output = 'raw';
    $_output_type = 'text/'.($_filename_ext=='js'?'javascript':$_filename_ext);
}
