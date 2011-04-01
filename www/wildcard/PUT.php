<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

if (!count($_domain_data) || !\sites\is_owner($_domain, $_user))
    httpStatusExit(403, 'Forbidden');

@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    file_put_contents($_filename, $_data);
    exit;
}

$g = new \RDF\Graph('memory', '', '', $_base);
if (!empty($_input) && $g->append($_input, $_data)) {
    file_put_contents($_filename, (string)$g);
} elseif ($g->append('turtle', $_data)) {
    file_put_contents($_filename, (string)$g);
}

header('X-Triples: '.$g->size());
