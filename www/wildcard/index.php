<?php
/* index.php
 * wildcard/catch-all request handler
 *
 * $Id$ */

require_once('runtime.php');

// CORS
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// WebDAV
header('MS-Author-Via: SPARQL');

// REQUEST_METHOD dispatch
if (in_array($_method, array('GET', 'HEAD', 'OPTIONS'))) {
    require_once('GET.php');
} elseif (in_array($_method, array('MKCOL', 'PATCH', 'POST', 'PUT', 'DELETE'))) {
    require_once("$_method.php");
}
