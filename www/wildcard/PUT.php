<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

require_once('runtime.php');

// permissions
if (empty($_user)) {
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
if (!$_options->clobber && $g->exists())
    httpStatusExit(409, 'Resource Exists', null, 'First DELETE the resource or set X-Options: clobber');
$g->truncate();
if (!empty($_input) && $g->append($_input, $_data)) {
    $g->save();
} elseif ($_content_type == 'application/json') {
    if ($g->append('json', $_data) || 1)
        $g->save();
} else {
    httpStatusExit(406, 'Content-Type Not Acceptable');
}

@header('X-Triples: '.$g->size());
