<?php
/* runtime.php
 * cloud common includes
 *
 * $Id$
 */

define('METHODS_S', 'GET, PUT, POST, OPTIONS, HEAD, MKCOL, DELETE, PATCH');

require_once(dirname(__FILE__).'/../inc/runtime.inc.php');

$_RAW_EXT = array(
    'css'=>'text',
    'html'=>'text',
    'js'=>'text',
    'jpg'=>'image');

header("User: $_user");

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
    header('Filename: '.$_filename);
}

// WebDAV
header('MS-Author-Via: DAV, SPARQL');

// HTTP Access Control
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
    header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    header('Access-Control-Allow-Methods: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $t = explode('/', $_SERVER['HTTP_ORIGIN']);
    if (count($t) > 2) {
        $n = "{$t[0]}//{$t[2]}";
    } else {
        $n = '*';
    }
    header('Access-Control-Allow-Origin: '.$n);
    header('Access-Control-Allow-Credentials: true');
}

// Web Access Control
$_aclbase = $_filebase.$_options->base_url;
$_acl = new Graph('', "$_aclbase/.meta", '', REQUEST_BASE.'/.meta');
if ($_options->linkmeta || $_acl->exists())
    header('Link: <'.$_options->base_url.'/.meta>; rel=meta');
function wac($method,$uri=null) {
    // method: Read/Write/Control
    global $_acl, $_user, $_base, $_options;
    if ($_options->open && !$_acl->size())
        return true;
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
    return false;
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

    if (!isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header('Access-Control-Allow-Methods: '.METHODS_S);
    if (!isset($_SERVER['HTTP_ORIGIN']))
        header('Access-Control-Allow-Origin: *');

    header('Allow: '.METHODS_S);
    header('Accept-Patch: application/json');
    exit;
}

// HTTP Content Negotiation
require_once('input.php');
require_once('output.php');
if (isset($_RAW_EXT[$_filename_ext])) {
    $_input = 'raw';
    $_output = 'raw';
    $_output_type = $_RAW_EXT[$_filename_ext].'/'.($_filename_ext=='js'?'javascript':$_filename_ext);
}
