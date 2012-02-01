<?php
/* index.php
 * wildcard/catch-all request handler
 *
 * $Id$ */

require_once('runtime.php');

// REQUEST_METHOD dispatch
if (in_array($_method, array('GET', 'HEAD', 'OPTIONS'))) {
    require_once('GET.php');
} elseif (in_array($_method, array('MKCOL', 'PATCH', 'POST', 'PUT', 'DELETE'))) {
    require_once("$_method.php");
}
