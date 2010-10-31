<?php
/* app.lib.php
 * application API
 *
 * $Id$
 */

namespace sites {
    $sites = new \RDF\Graph('sqlite', '/home/rdf.me/sites.sqlite', '', 'http://rdf.me/ns/schema');
    function is_valid($name) {
        return gettype($name) == 'string' and strlen($name) > 3;
    }
    function is_available($name) {
        if (!is_valid($name))
            return false;
        global $sites;
        $domain = "$name.".BASE_DOMAIN;
        $domain_uri = "http://$domain/";
        $q = "SELECT ?o WHERE { <$domain_uri> <#creator> ?o }";
        $q = $sites->SELECT($q);
        $q = $q['results']['bindings'];
        return count($q) < 1;
    }
    function is_public($domain) {
        global $sites;
        $q = "SELECT ?o WHERE { <http://$domain/> <#acl> <acl#public> }";
        $q = $sites->SELECT($q);
        $q = $q['results']['bindings'];
        return count($q) > 0;
    }
    function is_created_by($domain, $uri) {
        global $sites;
        $q = "SELECT ?o WHERE { <http://$domain/> <#creator> <$uri> }";
        $q = $sites->SELECT($q);
        $q = $q['results']['bindings'];
        return count($q) > 0;
    }
    function created_by($uri) {
        global $sites;
        $q = $sites->SELECT("SELECT ?site WHERE { ?site <#creator> <$uri> }");
        $r = array();
        foreach ($q['results']['bindings'] as $row) {
            $r[] = $row['site']['value'];
        }
        return $r;
    }
}
namespace knows {
    function get($force=false) {
        $r = sess('knows');
        if (!$force && !is_null($r))
            return $r;
        $user = new \RDF\Graph('uri', 'http://presbrey.mit.edu/foaf#presbrey');
        $d = $user->SELECT("SELECT ?knows WHERE { <{$_SERVER['REMOTE_USER']}> <http://xmlns.com/foaf/0.1/knows> ?knows }");
        $d = $d['results']['bindings'];
        $r = array();
        foreach ($d as $row) {
            $k = $row['knows']['value'];
            $r[$k] = \sites\created_by($k);
        }
        sess('knows', $r);
        return $r;
    }
}
