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
if (!isset($_ENV['CLOUD_BASE'])) $_ENV['CLOUD_BASE'] = '.data.fm';
if (!isset($_ENV['CLOUD_HOME'])) $_ENV['CLOUD_HOME'] = '/srv/cloud';
if (!isset($_ENV['CLOUD_DATA'])) $_ENV['CLOUD_DATA'] = '/srv/clouds';
define('BASE_DOMAIN', $_ENV['CLOUD_NAME']);
define('BASE_URI', (isHTTPS()?'https':'http').'://'.BASE_DOMAIN);
define('BASE_HTTP', 'http://'.BASE_DOMAIN.'/');
define('X_AGENT', isset($_SERVER['X_AGENT']) ? $_SERVER['X_AGENT'] : 'Mozilla');
define('X_PAD', isset($_SERVER['X_PAD']) ? $_SERVER['X_PAD'] : '(null)');

define('REQUEST_TIME', $_SERVER['REQUEST_TIME']);
if (isHTTPS()) {
    $BASE = 'https://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!='443'?':'.$_SERVER['SERVER_PORT']:'');
} else {
    $BASE = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!='80'?':'.$_SERVER['SERVER_PORT']:'');
}
//$URI = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
$URI = $_SERVER['REQUEST_URI'];
define('REQUEST_BASE', $BASE);
define('REQUEST_URL', $URI);
define('REQUEST_URI', $BASE.$URI);

// session startup
session_set_cookie_params(157680000, '/', $_ENV['CLOUD_BASE']);
session_start();

// application dependencies
require_once('rdf.lib.php');
require_once('app.lib.php');

date_default_timezone_set('America/New_York');
import_request_variables('gp', 'i_');

# init user ID
$_user = '';
foreach (array($_SERVER['REMOTE_USER'], sess('u:id'), sess('f:id')) as $_user) {
    if (!is_null($_user) && strlen($_user))
        break;
}

# proper Emails
if (substr($_user, 0, 4) != 'http')
    if (substr($_user, 0, 7) != 'mailto:' && stristr($_user,'@'))
        $_user = "mailto:$_user";

# fallback to DNS
if (empty($_user))
    $_user = 'dns:'.$_SERVER['REMOTE_ADDR'];

# init options
$_options = new stdClass();
$_options->clobber = false;
$_options->glob = false;
if (file_exists(dirname(__FILE__).'/config.inc.php')) {
    require_once(dirname(__FILE__).'/config.inc.php');
}
if (isset($_SERVER['HTTP_X_OPTIONS']))
foreach (explode(',',$_SERVER['HTTP_X_OPTIONS']) as $elt) {
    $k = trim($elt);
    $v = true;
    if ($k[0] == 'n' && $k[1] == 'o') {
        $k = substr($k, 2);
        $v = false;
    }
    if (isset($_options->$k))
        $_options->$k = $v;
}
if (!isset($_edit)) $_edit = true;
if (!isset($_showCode)) $_showCode = true;

# init user props
$_user_name = sess('f:name');
$_user_link = sess('f:link');
$_user_picture = sess('f:picture');

# ensure user props
if ($_user) {
    if (!$_user_name) {
        $_user_name = basename($_user);
        $c = strpos($_user_name, ':');
        if ($c > 0)
            $_user_name = substr($_user_name, $c+1);
    }
    if (!$_user_link)
        $_user_link = $_user;
}

TAG(__FILE__, __LINE__, '$Id$');
