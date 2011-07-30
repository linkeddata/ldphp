<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

// permissions
$acl_public = \sites\is_public_write($_domain);
if ($acl_public && !empty($_user)) {
} elseif (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!\sites\is_owner($_domain, $_user) && !wac('Write')) {
    httpStatusExit(403, 'Forbidden');
}

// action
$d = dirname($_filename);
if (!file_exists($d))
    mkdir($d, 0777, true);

$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    file_put_contents($_filename, $_data);
    exit;
}

$g = new \RDF\Graph('', $_filename, '', $_base);
$g->truncate();
if (!empty($_input) && $g->append($_input, $_data)) {
    $g->save();
} elseif ($_content_type == 'application/json') {
    $g->append_array(json_decode($_data, 1));
    $g->save();
} elseif ($g->append('turtle', $_data)) {
    $g->save();
}

@header('X-Triples: '.$g->size());
