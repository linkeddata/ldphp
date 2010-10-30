<?php
/* GET.php
 * service HTTP GET controller
 *
 * $Id$
 */

include_once('wildcard.inc.php');

// permissions
// TODO: WACL
if (!count($_domain_data) || (!\sites\is_public($_domain) && (empty($_user) || !\sites\is_created_by($_domain, $_user)))) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    include_once('403-404.php');
    exit;
}

if (substr($_filename, -1) == '/') {
    include_once('index.php');
    exit;
}

if (empty($_output) && !file_exists($_filename)) {
    $parts = explode('.', $_filename);
    $n = count($parts);
    if ($n > 0) {
        $ext = $parts[$n-1];
        $_filename = substr($_filename, 0, -strlen($ext)-1);
        $_base = substr($_base, 0, -strlen($ext)-1);
        if ($ext == 'turtle' || $ext == 'n3') {
            $_output = 'turtle';
            $_output_type = 'text/turtle';
        } elseif ($ext == 'json') {
            $_output = 'json';
            $_output_type = 'application/json';
        } elseif ($ext == 'rdf') {
            $_output = 'rdfxml-abbrev';
            $_output_type = 'application/rdf+xml';
        } elseif ($ext == 'nt') {
            $_output = 'ntriples';
            $_output_type = 'application/rdf+nt';
        }
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

header("Content-Type: $_output_type");
$g = new \RDF\Graph('memory', '', '', $_base);
$g->append('turtle', file_get_contents($_filename));
header('X-Triples: '.$g->size());
echo $g->to_string($_output);
exit;
