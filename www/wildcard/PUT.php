<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
$acl_public = \sites\is_public_write($_domain);
if ($acl_public && !empty($_user)) {
} elseif (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!\sites\is_owner($_domain, $_user)) {
    httpStatusExit(403, 'Forbidden');
}

// action
@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    file_put_contents($_filename, $_data);
    exit;
}

$g = new \RDF\Graph('', $_filename, '', $_base);
$g->truncate();
if (!empty($_input) && $g->append($_input, $_data)) {
    $g->save();
} elseif ($g->append('turtle', $_data)) {
    $g->save();
}

header('X-Triples: '.$g->size());
