<?php
/* runtime.php
 * application main runtime
 *
 * $Id$
 */

// base dependencies
require_once('util.lib.php');

// base constants
if (!isset($_ENV['CLOUD_NAME'])) $_ENV['CLOUD_NAME'] = 'data.fm';
if (!isset($_ENV['CLOUD_HOME'])) $_ENV['CLOUD_HOME'] = '/srv/cloud';
if (!isset($_ENV['CLOUD_DATA'])) $_ENV['CLOUD_DATA'] = '/srv/clouds';
define('BASE_DOMAIN', $_ENV['CLOUD_NAME']);
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

date_default_timezone_set('America/New_York');

$_user = '';
foreach (array($_SERVER['REMOTE_USER'], sess('f:id')) as $_user) {
    if (!is_null($_user) && strlen($_user))
        break;
}

# email ID
if (substr($_user, 0, 4) != 'http' && stristr($_user,'@'))
    $_user = "mailto:$_user";

# facebook ID
$_user_name = sess('f:name');
if (!$_user_name && $_user) {
    $_user_name = basename($_user);
    $c = strpos($_user_name, ':');
    if ($c > 0)
        $_user_name = substr($_user_name, $c+1);
}
$_user_link = sess('f:link');
if (!$_user_link) $_user_link = $_user;
$_user_picture = sess('f:picture');

TAG(__FILE__, __LINE__, '$Id$');
