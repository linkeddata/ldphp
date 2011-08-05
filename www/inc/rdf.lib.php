<?php
/* rdf.lib.php
 * RDF API
 *
 * $Id$
 */

namespace RDF {

    function absolutize($base, $url) {
        if (!$url)
            return $base;
        $url_p = parse_url($url);
        if (array_key_exists('scheme', $url_p))
            return $url;
        $base_p = parse_url("$base ");
        if (!array_key_exists('path', $base_p))
            $base_p = parse_url("$base/ ");
        $path = ($url[0] == '/') ? $url : dirname($base_p['path']) . "/$url";
        $path = preg_replace('~/\./~', '/', $path);
        $parts = array();
        foreach (explode('/', preg_replace('~/+~', '/', $path)) as $part)
            if ($part === '..') {
                array_pop($parts);
            } elseif ($part != '') {
                $parts[] = $part;
            }
        return (array_key_exists('scheme', $base_p) ? $base_p['scheme'] . '://' . $base_p['host'] : '') . '/' . implode('/', $parts);
    }

    class Graph {
        private $_world, $_base_uri, $_store, $_model;
        private $_f_writeBaseURI;
        private $_name, $_exists, $_storage, $_base;
        function __construct($storage, $name, $options='', $base='null:/') {
            $ext = strrpos($name, '.');
            $ext = $ext ? substr($name, 1+$ext) : '';
            $this->_exists = false;

            // auto-detect empty storage from name
            if (empty($storage) && !empty($name)) {
                $storage = 'memory';
                if (file_exists($name)) {
                    $this->_exists = true;
                    if ($ext == 'sqlite')
                        $storage = 'sqlite';
                } elseif (file_exists("$name.sqlite")) {
                    $this->_exists = true;
                    $name = "$name.sqlite";
                    $ext = 'sqlite';
                    $storage = 'sqlite';
                } else {
                    /*
                    // auto-enable SQLite
                    if ($ext != 'sqlite') {
                        $name = "$name.sqlite";
                        $storage = 'sqlite';
                    }
                    */
                    if ($ext == 'sqlite') {
                        $storage = 'sqlite';
                        if (empty($options))
                            $options = "new='yes'";
                    }
                }
            }
            $this->_name = $name;
            if ($storage == 'memory')
                $name = '';
            $this->_storage = $storage;
            $this->_base = $base;
            /*
            if (DEVEL) {
                header('X-Filename: '.$this->_name);
                header('X-Storage: '.$this->_storage);
                header('X-Options: '.$options);
            }
            */

            // instance state
            $this->_world = librdf_php_get_world();
            $this->_base_uri = librdf_new_uri($this->_world, $base);
            $this->_store = librdf_new_storage($this->_world, $this->_storage, $this->_name, $options);
            $this->_model = librdf_new_model($this->_world, $this->_store, null);

            // const objs
            $this->_f_writeBaseURI = librdf_new_uri($this->_world, 'http://feature.librdf.org/raptor-writeBaseURI');
            $this->_n_0 = librdf_new_node_from_literal($this->_world, 0, null, 0);

            if ($storage == 'memory' && $this->exists())
                $this->append_file('turtle', "file://{$this->_name}", $this->_base);
        }
        function exists() { return $this->_exists; }
        function save() {
            if ($this->_storage == 'memory' && !empty($this->_name)) {
                file_put_contents($this->_name, $this->__toString());
            }
            return librdf_model_sync($this->_model);
        }
        function truncate() {
            librdf_free_model($this->_model);
            librdf_free_storage($this->_store);
            if ($this->_storage != 'memory')
                $this->delete();
            $this->_store = librdf_new_storage(
                $this->_world, $this->_storage,
                $this->_storage == 'memory' ? '' : $this->_name,
                $this->_storage == 'sqlite' ? "new='yes'" : ''
            );
            $this->_model = librdf_new_model($this->_world, $this->_store, null);
            $this->_exists = $this->_model ? true : false;
        }
        function delete() {
            if ($this->exists()) {
                unlink($this->_name);
                $this->_exists = false;
            }
        }
        function __destruct() {
            // instance state
            librdf_free_model($this->_model);
            librdf_free_storage($this->_store);
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
            $p = librdf_new_parser($this->_world, $content_type, null, null);
            $r = librdf_parser_parse_string_into_model($p, $content, $this->_base_uri, $this->_model);
            librdf_free_parser($p);
            return $r == 0;
        }
        function append_file($content_type, $file, $base=null) {
            $p = librdf_new_parser($this->_world, $content_type, null, null);
            $file_uri = librdf_new_uri($this->_world, $file);
            $base_uri = librdf_new_uri($this->_world, is_null($base)?$this->_base:$base);
            $r = librdf_parser_parse_into_model($p, $file_uri, $base_uri, $this->_model);
            librdf_free_parser($p);
            librdf_free_uri($base_uri);
            librdf_free_uri($file_uri);
            return $r == 0;
        }
        function load($uri) {
            return librdf_model_load($this->_model, $uri, 'guess', null, null);
        }
        function _node($node) {
            $r = array('value' => librdf_node_to_string($node));
            if (librdf_node_is_resource($node)) {
                $r['type'] = 'uri';
                $r['value'] = substr($r['value'], 1, -1);
            } elseif (librdf_node_is_literal($node)) {
                $r['type'] == 'literal';
            } elseif (librdf_node_is_blank($node)) {
            }
            return $r;
        }
        function _statement($statement) {
            return array(
                $this->_node(librdf_statement_get_subject($statement)),
                $this->_node(librdf_statement_get_predicate($statement)),
                $this->_node(librdf_statement_get_object($statement))
            );
        }
        function any($s=null, $p=null, $o=null) {
            $r = array();
            if (!is_null($s)) $s = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $s));
            if (!is_null($p)) $p = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $p));
            $pattern = librdf_new_statement_from_nodes($this->_world, $s, $p, $o);
            $stream = librdf_model_find_statements($this->_model, $pattern);
            while (!librdf_stream_end($stream)) {
                $r[] = $this->_statement(librdf_stream_get_object($stream));
                librdf_stream_next($stream);
            }
            librdf_free_stream($stream);
            librdf_free_statement($pattern);
            $s && librdf_free_node($s);
            $p && librdf_free_node($p);
            return $r;
        }
        function remove_any($s=null, $p=null, $o=null) {
            $r = 0;
            if (!is_null($s)) $s = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $s));
            if (!is_null($p)) $p = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $p));
            $pattern = librdf_new_statement_from_nodes($this->_world, $s, $p, $o);
            $stream = librdf_model_find_statements($this->_model, $pattern);
            while (!librdf_stream_end($stream)) {
                $elt = librdf_stream_get_object($stream);
                $r += librdf_model_remove_statement($this->_model, $elt) ? 0 : 1;
                librdf_stream_next($stream);
            }
            librdf_free_stream($stream);
            librdf_free_statement($pattern);
            $s && librdf_free_node($s);
            $p && librdf_free_node($p);
            return $r;
        }
        function query($query, $base_uri=null) {
            timings($query);
            if (is_null($base_uri)) $base_uri = $this->_base_uri;
            $q = librdf_new_query($this->_world, 'sparql', null, $query, $base_uri);
            $r = librdf_model_query_execute($this->_model, $q);
            $json_uri = librdf_new_uri($this->_world, 'http://www.w3.org/2001/sw/DataAccess/json-sparql/');
            $r = librdf_query_results_to_string($r, $json_uri, $base_uri);
            librdf_free_query($q);
            librdf_free_uri($json_uri);
            timings();
            return $r;
        }
        function SELECT($query, $base_uri=null) {
            return json_decode($this->query($query, $base_uri), 1);
        }
        function SELECT_p_o($uri, $base_uri=null) {
            $q = "SELECT * WHERE { <$uri> ?p ?o }";
            $r = array();
            $d = $this->SELECT($q, $base_uri);
            if (isset($d['results']) && isset($d['results']['bindings']))
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
            timings($query);
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
            timings();
            return $r;
        }
        function append_objects($s, $p, $lst) {
            if (!is_null($s)) { $s = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $s)); }
            if (!is_null($p)) { $p = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $p)); }
            $r = 0;
            foreach ($lst as $elt) {
                if (isset($elt['type']) && isset($elt['value'])) {
                    if ($elt['type'] == 'literal') {
                        $o = librdf_new_node_from_literal($this->_world, $elt['value'], NULL, 0);
                    } elseif ($elt['type'] == 'uri') {
                        $o = librdf_new_node_from_uri_string($this->_world, absolutize($this->_base, $elt['value']));
                    }
                    $r += librdf_model_add($this->_model, $s, $p, $o) ? 0 : 1;
                    //$o && librdf_free_node($o);
                }
            }
            //$p && librdf_free_node($p);
            //$s && librdf_free_node($s);
            return $r;
        }
        function append_array($data) {
            $r = 0;
            foreach ($data as $s=>$s_data) {
                foreach ($s_data as $p=>$p_data) {
                    $r += $this->append_objects($s, $p, $p_data);
                }
            }
            return $r;
        }
        function patch_array($data) {
            $r = 0;
            foreach ($data as $s=>$s_data) {
                foreach ($s_data as $p=>$p_data) {
                    $r += $this->remove_any($s, $p);
                    $r += $this->append_objects($s, $p, $p_data);
                }
            }
            return $r;
        }
    } // class Graph
} // namespace RDF

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
