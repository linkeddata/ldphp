<?php

function queryError($msg) {
    header('HTTP/1.1 400 Bad Request');
    echo "$msg\n";
    exit;
}

require_once('arc2/ARC2.php');

$query = $_data;
$parser = ARC2::getMITSPARQLParser();
$parser->parse($query);
if (isset($parser->errors) && count($parser->errors))
    queryError(implode("\n",$parser->errors));

$info = $parser->getQueryInfos();
$query = $info['query'];

$assure_strings = array('type', 'target_graph');
$assure_arrays = array('dataset', 'target_graphs', 'construct_triples');

foreach ($assure_strings as $k=>$v)
    if (!isset($query[$v]))
        $query[$v] = '';
foreach ($assure_arrays as $k=>$v)
    if (!isset($query[$v]))
        $query[$v] = array();
foreach ($query as $k=>$v)
    if (!in_array($k, $assure_arrays) && !in_array($k, $assure_strings))
        queryError('unsupported query feature: '.$k);

if (!in_array($query['type'], array('insert')))
    queryError('valid query types: insert');

if (strlen($query['target_graph'])) {
    if ($query['target_graph'] != $_base)
        queryError('query must target request URI graph (only)');
    if (count($query['target_graphs']) && $query['target_graphs'][0] != $_base)
        queryError('query must target request URI graph (only)');
}

foreach ($query['construct_triples'] as $elt)
    foreach (array('s', 'p', 'o') as $k)
        if (!in_array($elt["{$k}_type"], array('uri', 'literal')))
            queryError('unsupported node type: '.$elt[$k].' ('.$elt["{$k}_type"].')');

$n = 0;
foreach ($query['construct_triples'] as $elt) {
    $g->append_objects($elt['s'], $elt['p'], array(array('type'=>$elt['o_type'], 'value'=>$elt['o'])));
    $n += 1;
}
if ($n)
    $g->save();
