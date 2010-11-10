<?php
/* wildcard.inc.php
 * site common includes
 *
 * $Id$
 */

$_domain = $_SERVER['SERVER_NAME'];
$_domain_data = $sites->SELECT_p_o("http://$_domain/");
$_user = $_SERVER['REMOTE_USER'];
$_filename = $_SERVER['REQUEST_FILENAME'];
$_base = $_SERVER['SCRIPT_URI'];

if (!strstr($_filename, '/')) {
    $_filename = '/home/'.BASE_DOMAIN."/data/{$_SERVER['SERVER_NAME']}/$_filename";
}

header("X-User: $_user");

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
if ($_SERVER['REDIRECT_REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}
