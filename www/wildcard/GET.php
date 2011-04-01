<?php
/* GET.php
 * service HTTP GET controller
 *
 * $Id$
 */

if ($_SERVER['REQUEST_METHOD'] != 'GET' && !isset($i_query))
    httpStatusExit(501, 'Not Implemented');

// permissions
// TODO: WACL
if (!count($_domain_data) || (!\sites\is_public($_domain) && (empty($_user) || !\sites\is_owner($_domain, $_user))))
    httpStatusExit(501, 'Forbidden', '403-404.php');

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

if (empty($_output)) {
    $_output = 'turtle';
    $_output_type = 'text/turtle';
}

if (!file_exists($_filename)) {
    if ($_output && $_output != 'raw')
        httpStatusExit(404, 'Not Found', 'empty.php');
    else
        httpStatusExit(404, 'Not Found', '403-404.php');
} elseif ($_output == 'raw') {
    if ($_output_type)
        header("Content-Type: $_output_type");
    readfile($_filename);
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
