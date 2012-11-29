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


// Generate a WebID if we have a KEYGEN key in the request
if (($_POST['SPKAC']) && (strlen($_POST['SPKAC']) > 0)) {
    require_once 'webidgen.php'; // will exit at the end
} 

// action
$d = dirname($_filename);
if (!file_exists($d))
    mkdir($d, 0777, true);

$_data = file_get_contents('php://input');

if ($_input == 'raw') {
    require_once('if-match.php');
    file_put_contents($_filename, $_data, FILE_APPEND | LOCK_EX);
    exit;
}

$g = new Graph('', $_filename, '', $_base);
require_once('if-match.php');

if ($_method == 'PATCH') {
    if ($_input == 'json' && ($g->patch_json($_data) || 1)) {
        librdf_php_last_log_level() && httpStatusExit(400, 'Bad Request', null, librdf_php_last_log_message());
        $g->save();
    }
} elseif (!empty($_input) && ($g->append($_input, $_data) || 1)) {
    librdf_php_last_log_level() && httpStatusExit(400, 'Bad Request', null, librdf_php_last_log_message());

    // Check to see if we have LDP data in the request
    require_once('LDP.php');
    $ldp = new LDP($_filename, $_base, $_input, $_data);
    
    // finally save
    $g->save();

    // and exit
    httpStatusExit(201, 'Created');  
} elseif ($_content_type == 'application/sparql-query') {
    require_once('SPARQL.php');
// catch requests for new WebID requests
} else {
    httpStatusExit(406, 'Content-Type ('.$_content_type.') Not Acceptable');
}

@header('Triples: '.$g->size());
