<?php
/* runtime.php
 * cloud common includes
 *
 * $Id$
 */

require_once(dirname(__FILE__).'/../inc/runtime.inc.php');

$_RAW_EXT = array('css', 'html', 'js');
header("X-User: $_user");

// Cloud
if (!isset($_SERVER['SCRIPT_URL']))
    $_SERVER['SCRIPT_URL'] = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
if (strpos($_SERVER['SCRIPT_URL'], '?'))
    $_SERVER['SCRIPT_URL'] = strstr($_SERVER['SCRIPT_URL'], '?', true);
if (!isset($_SERVER['SCRIPT_URI']))
    $_SERVER['SCRIPT_URI'] = REQUEST_BASE.$_SERVER['SCRIPT_URL'];
$_base = $_SERVER['SCRIPT_URI'];
$_domain = $_SERVER['SERVER_NAME'];

// Graph
$_filebase = $_ENV['CLOUD_DATA'].'/'.$_SERVER['SERVER_NAME'];
$_filename = $_SERVER['SCRIPT_URL'];
$_filename_ext = strrpos($_filename, '.');
$_filename_ext = $_filename_ext ? substr($_filename, 1+$_filename_ext) : '';
if (!strlen($_filename) || $_filename[0] != '/')
    $_filename = "/$_filename";
if (substr($_filename, 0, strlen($_filebase)) != $_filebase)
    $_filename = "$_filebase$_filename";
$_request_path = substr($_filename, strlen($_filebase));

if ($_options->debug) {
    header('X-Filename: '.$_filename);
}

// HTTP Access Control
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
    header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
} else {
    header('Access-Control-Allow-Headers: Content-Type, X-Prototype-Version, X-Requested-With');
}
if (!isHTTPS()) {
    header('Access-Control-Allow-Origin: *');
} else {
    $_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    $t = explode('/', $_origin);
    if (count($t) > 2) {
        $_origin = "{$t[0]}//{$t[2]}";
    } else {
        $_origin = '*';
    }
    header('Access-Control-Allow-Origin: '.$_origin);
}

// Web Access Control
header('Link: <'.$_options->base_url.'/.meta>; rel=meta');
$_metabase = $_filebase.$_options->base_url;
$_acl = new \RDF\Graph('', file_exists("$_metabase/.meta.sqlite")?"$_metabase/.meta":"$_metabase/.meta", '', REQUEST_BASE.'/.meta');
function wac($method,$uri=null) {
    // method: Read/Write/Control
    global $_acl, $_user, $_base, $_options;
    $uri = is_null($uri) ? $_base : $uri;
    // strip trailing slash
    if (substr($uri, -1, 1) == '/')
        $uri = substr($uri, 0, -1);
    $p = $uri;
    // walk path
    while (true) {
        if (!strpos($p, '/')) break;
        $verb = $p == $uri ? 'accessTo' : 'defaultForNew';
        // specific authorization
        $q = "PREFIX acl: <http://www.w3.org/ns/auth/acl#> SELECT * WHERE { ?z acl:agent <$_user>; acl:mode acl:$method; acl:$verb <$p> . }";
        $r = $_acl->SELECT($q);
        if (isset($r['results']['bindings']) && count($r['results']['bindings']) > 0)
            return true;
        // public authorization
        $q = "PREFIX acl: <http://www.w3.org/ns/auth/acl#> SELECT * WHERE { ?z acl:agentClass <http://xmlns.com/foaf/0.1/Agent>; acl:mode acl:$method; acl:$verb <$p> . }";
        $r = $_acl->SELECT($q);
        if (isset($r['results']['bindings']) && count($r['results']['bindings']) > 0)
            return true;
        $p = dirname($p);
    }
    return $_options->open || false;
}

// HTTP Methods
$_method = '';
foreach (array('REQUEST_METHOD', 'REDIRECT_REQUEST_METHOD') as $k) {
    if (isset($_SERVER[$k])) {
        $_method = strtoupper($_SERVER[$k]);
        break;
    }
}
if ($_method == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    header('Allow: GET, PUT, POST, OPTIONS, HEAD, MKCOL, DELETE, PATCH');
    header('Accept-Patch: application/json');
    exit;
}

// HTTP Content Negotiation
require_once('input.php');
require_once('output.php');
if (in_array($_filename_ext, $_RAW_EXT)) {
    $_input = 'raw';
    $_output = 'raw';
    $_output_type = 'text/'.($_filename_ext=='js'?'javascript':$_filename_ext);
}
