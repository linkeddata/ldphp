<?php
/* rdf.lib.php
 * RDF API
 *
 * $Id$
 */

namespace RDF {
    class Graph {
        private $_world, $_base_uri, $_storage, $_model;
        private $_f_writeBaseURI;
        function __construct($storage, $name, $options='', $base='http://null/') {
            // instance state
            $this->_world = librdf_php_get_world();
            $this->_base_uri = librdf_new_uri($this->_world, $base);
            $this->_storage = librdf_new_storage($this->_world, $storage, $name, $options);
            $this->_model = librdf_new_model($this->_world, $this->_storage, null);
            // common
            $this->_f_writeBaseURI = librdf_new_uri($this->_world, 'http://feature.librdf.org/raptor-writeBaseURI');
            $this->_n_0 = librdf_new_node_from_literal($this->_world, 0, null, 0);
        }
        function __destruct() {
            // instance state
            librdf_free_model($this->_model);
            librdf_free_storage($this->_storage);
            librdf_free_uri($this->_base_uri);
            // common
            librdf_free_uri($this->_f_writeBaseURI);
            librdf_free_node($this->_n_0);
        }
        function __toString() {
            return $this->to_string('turtle');
        }
        function to_string($name) {
            $s = librdf_new_serializer($this->_world, $name, null, null);
            librdf_serializer_set_feature($s, $this->_f_writeBaseURI, $this->_n_0);
            $r = librdf_serializer_serialize_model_to_string($s, $this->_base_uri, $this->_model);
            librdf_free_serializer($s);
            return $r;
        }
        function size() {
            return librdf_model_size($this->_model);
        }
        function append($content_type, $content) {
            //echo "parsing: $content_type ".strlen($content)."\n";
            $p = librdf_new_parser($this->_world, $content_type, null, null);
            $r = librdf_parser_parse_string_into_model($p, $content, $this->_base_uri, $this->_model);
            librdf_free_parser($p);
            return $r == 0;
        }
        function load($uri) {
            return librdf_model_load($this->_model, $uri, 'guess', null, null);
        }
        function SELECT($query, $base_uri=null) {
            if (is_null($base_uri)) $base_uri = $this->_base_uri;
            $q = librdf_new_query($this->_world, 'sparql', null, $query, $base_uri);
            $r = librdf_model_query_execute($this->_model, $q);
            $json_uri = librdf_new_uri($this->_world, 'http://www.w3.org/2001/sw/DataAccess/json-sparql/');
            $r = json_decode(librdf_query_results_to_string($r, $json_uri, $this->_base_uri), 1);
            librdf_free_query($q);
            librdf_free_uri($json_uri);
            return $r;
        }
        function SELECT_p_o($uri, $base_uri=null) {
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
        function CONSTRUCT($query, $base_uri=null) {
            if (is_null($base_uri)) $base_uri = $this->_base_uri;
            $q = librdf_new_query($this->_world, 'sparql', null, $query, $base_uri);
            $r = librdf_model_query_execute($this->_model, $q);
            $r_stream = librdf_query_results_as_stream($r);
            $r_store = librdf_new_storage($this->_world, 'memory', '', null);
            $r_model = librdf_new_model($this->_world, $r_store, null);
            librdf_model_add_statements($r_model, $r_stream);
            librdf_free_stream($r_stream);
            $serializer = librdf_new_serializer($this->_world, 'json', null, null);
            $r = librdf_serializer_serialize_model_to_string($serializer, null, $r_model);
            librdf_free_serializer($serializer);
            $r = json_decode($r, 1);
            if (is_null($r)) $r = array();
            librdf_free_model($r_model);
            librdf_free_storage($r_store);
            librdf_free_query($q);
            return $r;
        }
    }
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
*/
