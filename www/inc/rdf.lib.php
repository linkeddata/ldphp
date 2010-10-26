<?php

namespace RDF {
    class Graph {
        private $_world, $_base_uri, $_storage, $_model;
        function __construct($storage, $name, $options='', $base_uri='http://null/') {
            $this->_world = librdf_php_get_world();
            $this->_base_uri = librdf_new_uri($this->_world, $base_uri);
            $this->_storage = librdf_new_storage($this->_world, $storage, $name, $options);
            $this->_model = librdf_new_model($this->_world, $this->_storage, null);
        }
        function __destruct() {
            librdf_free_model($this->_model);
            librdf_free_storage($this->_storage);
            librdf_free_uri($this->_base_uri);
        }
        function append($content_type, $content) {
            $p = librdf_new_parser($this->_world, $content_type, null, null);
            return librdf_parser_parse_string_into_model($p, $content, $this->_base_uri, $this->_model);
        }
        function SELECT($query, $base_uri=null) {
            if (is_null($base_uri))
                $base_uri = $this->_base_uri;
            $q = librdf_new_query($this->_world, 'sparql', NULL, $query, $base_uri);
            $r = librdf_model_query_execute($this->_model, $q);
            $json_uri = librdf_new_uri($this->_world, 'http://www.w3.org/2001/sw/DataAccess/json-sparql/');
            $r = json_decode(librdf_query_results_to_string($r, $json_uri, $this->_base_uri), 1);
            librdf_free_query($q);
            librdf_free_uri($json_uri);
            return $r;
        }
        function SELECT_p_o($uri, $base_uri=NULL) {
            $q = "SELECT * WHERE { <$uri> ?p ?o }";
            $r = array();
            $d = $this->SELECT($q, $base_uri);
            foreach($d['results']['bindings'] as $elt) {
                $p = $elt['p']['value'];
                if (!isset($r[$p])) {
                    $r[$p] = array();
                }
                $r[$p][] = $elt['o'];
            }
            return $r;
        }
    }
    $sites = new Graph('sqlite', '/home/rdf.me/sites.sqlite', '', 'http://rdf.me/ns/schema');
}

/*
$_NS = array(
    'rdfs' => '<http://www.w3.org/2000/01/rdf-schema#>',
    'dc' => '<http://purl.org/dc/terms/>',
    'foaf' => '<http://xmlns.com/foaf/0.1/>',
    'en' => '<http://en.wikipedia.org/wiki/>',
    'rdf' => '<http://www.w3.org/1999/02/22-rdf-syntax-ns#>',
    'xsd' => '<http://www.w3.org/2001/XMLSchema#>',
);

function CONSTRUCT($model, $q, $base_uri=NULL) {
    global $_WORLD, $_MODEL;
    $_q = librdf_new_query($_WORLD, 'sparql', NULL, $q, $base_uri);
    $_r = librdf_model_query_execute($_MODEL[$model], $_q);
    $r_s = librdf_query_results_as_stream($_r);

    $r_store = librdf_new_storage($_WORLD, 'memory', '', NULL);
    $r_model = librdf_new_model($_WORLD, $r_store, NULL);
    librdf_model_add_statements($r_model, $r_s);
    $serializer = librdf_new_serializer($_WORLD, 'json', NULL, NULL);
    $r = librdf_serializer_serialize_model_to_string($serializer, NULL, $r_model);
    librdf_free_serializer($serializer);
    $r = json_decode($r, 1);
    if (is_null($r)) $r = array();
    librdf_free_model($r_model);
    librdf_free_storage($r_store);
    librdf_free_query($_q);
    return $r;
}
*/
