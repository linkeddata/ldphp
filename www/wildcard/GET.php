<?php
/* GET.php
 * service HTTP GET controller
 *
 * $Id$
 */

if ($_SERVER['REQUEST_METHOD'] != 'GET' && !isset($i_query)) {
    $TITLE = '501 Not Implemented';
    header("HTTP/1.1 $TITLE");
    exit;
}

// permissions
// TODO: WACL
if (!count($_domain_data) || (!\sites\is_public($_domain) && (empty($_user) || !\sites\is_owner($_domain, $_user)))) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    include_once('403-404.php');
    exit;
}

if (is_dir($_filename)) {
    if (substr($_filename, -1) == '/') {
        include_once('index.php');
        exit;
    } else {
        header("Location: $_base/");
        exit;
    }
}

if (!file_exists($_filename) && in_array($_filename_ext, array('turtle','n3','json','rdf','nt'))) {
    $_filename = substr($_filename, 0, -strlen($_filename_ext)-1);
    $_base = substr($_base, 0, -strlen($_filename_ext)-1);
    if ($_filename_ext == 'turtle' || $ext == 'n3') {
        $_output = 'turtle';
        $_output_type = 'text/turtle';
    } elseif ($_filename_ext == 'json') {
        $_output = 'json';
        $_output_type = 'application/json';
    } elseif ($_filename_ext == 'rdf') {
        $_output = 'rdfxml-abbrev';
        $_output_type = 'application/rdf+xml';
    } elseif ($_filename_ext == 'nt') {
        $_output = 'ntriples';
        $_output_type = 'text/plain';
    }
}

if (!file_exists($_filename)) {
    $TITLE = '404 Not Found';
    header("HTTP/1.1 $TITLE");
    $_filename = null;
}

if (empty($_output)) {
    $_output = 'turtle';
    $_output_type = 'text/turtle';
} elseif (0 && empty($_output)) {
    $TITLE = '415 Unsupported Media Type';
    header("HTTP/1.1 $TITLE");
    exit;
}

if ($_output == 'raw') {
    if ($_filename) {
        header("Content-Type: $_output_type");
        readfile($_filename);
    } else {
        require_once('403-404.php');
    }
    exit;
}

$g = new \RDF\Graph('memory', '', '', $_base);
if (!empty($_filename)) {
    $g->append('turtle', file_get_contents($_filename));
}

header('X-Triples: '.$g->size());

if (isset($i_callback)) {
    header('Content-Type: text/javascript');
    echo "$i_callback(";
    register_shutdown_function(function() { echo ');'; });
} elseif (isset($i_query)) {
    header('Content-Type: application/json');
} else {
    header("Content-Type: $_output_type");
}

if (isset($i_query)) {
    echo $g->query($i_query);
} else {
    echo $g->to_string($_output);
}
