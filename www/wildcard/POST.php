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
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

// TODO: WACL
if (!count($_domain_data) || !\sites\is_owner($_domain, $_user))
    httpStatusExit(403, 'Forbidden');

@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    $f = fopen($_filename, 'a');
    fwrite($f, $_data);
    fclose($f);
    exit;
}

$g = new \RDF\Graph('memory', '', '', $_base);
if (file_exists($_filename)) {
    $g->append('turtle', file_get_contents($_filename));
}
if (!empty($_input) && $g->append($_input, $_data)) {
    file_put_contents($_filename, (string)$g);
} elseif ($g->append('turtle', $_data)) {
    file_put_contents($_filename, (string)$g);
}

header('X-Triples: '.$g->size());
