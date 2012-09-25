<?php
/* POST.php
 * service HTTP POST controller
 * (PATCH is a variant of POST)
 *
 * $Id$
 */

require_once('runtime.php');

if (isset($i_query)) {
    require_once('GET.php');
    exit;
}

// permissions
if (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!wac('Write')) {
    httpStatusExit(403, 'Forbidden');
}

// action
$d = dirname($_filename);
if (!file_exists($d))
    mkdir($d, 0777, true);

$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    file_put_contents($_filename, $_data, FILE_APPEND | LOCK_EX);
    exit;
}

$g = new \RDF\Graph('', $_filename, '', $_base);
if ($_method == 'PATCH') {
    if ($_input == 'json' && ($g->patch_json($_data) || 1)) {
        librdf_php_last_log_level() && httpStatusExit(400, 'Bad Request', null, librdf_php_last_log_message());
        $g->save();
    }
} elseif (!empty($_input) && ($g->append($_input, $_data) || 1)) {
    librdf_php_last_log_level() && httpStatusExit(400, 'Bad Request', null, librdf_php_last_log_message());
    $g->save();
} elseif ($_content_type == 'application/sparql-query') {
    require_once('SPARQL.php');
} else {
    httpStatusExit(406, 'Content-Type ('.$_content_type.') Not Acceptable');
}

@header('X-Triples: '.$g->size());
