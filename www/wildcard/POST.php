<?php
/* POST.php
 * service HTTP POST controller
 *
 * $Id$
 */

if (isset($i_query)) {
    include_once('method/GET.php');
    exit;
}

// permissions
// TODO: WACL
$acl_public = \sites\is_public_write($_domain);
if ($acl_public) {
} elseif (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!\sites\is_owner($_domain, $_user)) {
    httpStatusExit(403, 'Forbidden');
}

// action
@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    file_put_contents($_filename, $_data, FILE_APPEND | LOCK_EX);
    exit;
}

$g = new \RDF\Graph('', $_filename, '', $_base);
if (!empty($_input) && $g->append($_input, $_data)) {
    $g->save();
} elseif ($g->append('turtle', $_data)) {
    $g->save();
}

header('X-Triples: '.$g->size());
