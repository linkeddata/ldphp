<?php
require_once('../runtime.php');
require_once('webid.lib.php');

header('Content-type: text/plain');

$claim = webid_claim();
if (!isset($i_uri) && isset($claim['uri']))
    $i_uri = $claim['uri'];
if (!isset($i_uri) || substr($i_uri, 0, 4) != 'http')
    $i_uri = 'null:';

$g = new \RDF\Graph('uri', $i_uri, '', $i_uri);
$query = webid_query($i_uri, $g);

$r = array(
    'claim' => $claim,
    'lookup' => array(
        'uri' => $i_uri,
        'triples' => $g->size(),
        'results' => $query
    ),
    'verified' => webid_verify()
);
print_r($r);
