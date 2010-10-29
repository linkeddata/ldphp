<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

include_once('wildcard.inc.php');

// permissions
// TODO: WACL
if (empty($_user)) {
    $TITLE = '401 Unauthorized';
    header("HTTP/1.1 $TITLE");
    include_once('401.php');
    exit;
}
if (!count($d) || !\sites\is_created_by($_domain, $_user)) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    include_once('403-404.php');
    exit;
}

@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

$w = librdf_php_get_world();
$s = librdf_new_storage($w, 'memory', '', '');
$p = librdf_new_parser($w, $_input, '', null);
$m = librdf_new_model($w, $s, '');

//file_put_contents($_filename, );
