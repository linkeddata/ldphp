<?php
/* runtime.php
 * application main runtime
 *
 * $Id$
 */

// base dependencies
require_once('util.lib.php');

// base constants
define('BASE_DOMAIN', 'data.fm');
define('BASE_URI', 'http://'.BASE_DOMAIN);
define('BASE_HTTP', BASE_URI.'/');
define('X_AGENT', isset($_SERVER['X_AGENT']) ? $_SERVER['X_AGENT'] : 'Mozilla');
define('X_PAD', isset($_SERVER['X_PAD']) ? $_SERVER['X_PAD'] : '(null)');

define('REQUEST_TIME', $_SERVER['REQUEST_TIME']);
if (isHTTPS()) {
$BASE = 'https://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!='443'?':'.$_SERVER['SERVER_PORT']:'');
} else {
$BASE = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!='80'?':'.$_SERVER['SERVER_PORT']:'');
}
$URI = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
define('REQUEST_BASE', $BASE);
define('REQUEST_URL', $URI);
define('REQUEST_URI', $BASE.$URI);

// session startup
session_set_cookie_params(157680000, '/', '.'.BASE_DOMAIN);
session_start();

// application dependencies
require_once('rdf.lib.php');
require_once('app.lib.php');

import_request_variables('gp', 'i_');
if (is_array($_POST))
    extract($_POST, EXTR_PREFIX_ALL, 'p');

if (substr(REQUEST_URL, 0, 5) === '/json') {
    if (isset($g_callback)) {
        header('Content-type: text/javascript');
        echo "$g_callback(";
    } else {
        header('Content-type: application/json');
    }
}

date_default_timezone_set('America/New_York');

// negotiation: parse HTTP Accept
$_accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
$_accept_list = array();
$_accept_data = array();
foreach (explode(',', $_accept) as $elt) {
    $elt = explode(';', $elt);
    if (count($elt) == 1) {
        $_accept_list[] = trim($elt[0]);
    } elseif (count($elt) == 2) {
        $_accept_data[trim($elt[0])] = (float)substr($elt[1], 2);
    }
}
asort($_accept_data, SORT_NUMERIC);
$_accept_data = array_reverse($_accept_data);
//header('X-Accept-List: '.str_replace("\n"," ",print_r($_accept_list,1)));
//header('X-Accept-Data: '.str_replace("\n"," ",print_r($_accept_data,1)));

// negotiation: setup type maps
$_content_type_map = array(
    '/rdf+n3' => 'turtle',
    '/n3' => 'turtle',
    '/turtle' => 'turtle',
    '/rdf+nt' => 'ntriples',
    '/nt' => 'ntriples',
    '/rdf+xml' => 'rdfxml',
    '/rdf' => 'rdfxml',
    '/html' => 'rdfa',
    '/xhtml' => 'rdfa',
    '/rss+xml' => 'rss-tag-soup',
    '/rss' => 'rss-tag-soup',
);
$_accept_type_map = array(
    '/rdf+n3' => 'turtle',
    '/n3' => 'turtle',
    '/turtle' => 'turtle',
    '/rdf+nt' => 'ntriples',
    '/nt' => 'ntriples',
    '/rdf+xml' => 'rdfxml-abbrev',
    '/rdf' => 'rdfxml-abbrev',
    '/json' => 'json',
    '/atom+xml' => 'atom',
    '/rss+xml' => 'rss-1.0',
    '/rss' => 'rss-1.0',
    '/dot' => 'dot'
);

// negotiation: process HTTP Content-Type
$_content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
$_input = '';
foreach ($_content_type_map as $needle=>$input) {
    if (strstr($_content_type, $needle) !== FALSE) {
        $_input = $input;
        break;
    }
}

$_output = '';
$_output_type = null;
foreach ($_accept_list as $haystack) {
    foreach ($_accept_type_map as $needle=>$output) {
        if (strstr($haystack, $needle) !==FALSE) {
            $_output = $output;
            $_output_type = $haystack;
            break;
        }
    }
    if (!empty($_output)) break;
}
if (empty($output))
foreach (array_keys($_accept_data) as $haystack) {
    foreach ($_accept_type_map as $needle=>$output) {
        if (strstr($haystack, $needle) !==FALSE) {
            $_output = $output;
            $_output_type = $haystack;
            break;
        }
    }
    if (!empty($_output)) break;
}

TAG(__FILE__, __LINE__, '$Id$');
