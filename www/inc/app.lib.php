<?php
/* app.lib.php
 * application API
 *
 * $Id$
 */

namespace sites {
    $sites = new \RDF\Graph('sqlite', '/home/data.fm/sites.sqlite', '', 'http://data.fm/ns/schema');
    function is_valid($name) {
        $r = gettype($name) == 'string' && strlen($name) > 3;
        $r = $r && !in_array($name, array("1234","12345","123456","12345678","654321","admin","administrador","administrateur","administrator","asdf","asdfgh","audio","backlinks","beta","betas","blog","blogs","calendar","calendars","close","computer","computers","conn","contact","contacts","create","data","database","databases","default","delete","diary","edit","events","facebook","favorite","favorites","forum","forums","free","friend","friends","gallery","gates","google","guest","guests","guestbook","history","info","information","intro","invite","inviter","link","linked","linux","live","load","login","love","mail","manage","management","manager","mysql","open","oracle","owner","pass","passwd","password","passwords","photo","photos","post","posts","private","profile","profiles","public","qwer","read","remove","root","schedule","schema","secret","secrets","secure","server","servers","software","source","sources","standard","student","subscribe","sudo","support","sysop","teacher","temp","test","tests","update","updates","user","users","video","videos","view","views","webid","webids","weblog","webmaster","wiki","wikis","write","wwwadmin"));
        return $r;
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
namespace profile {
    function knows($uri, $force=false) {
        $r = sess('knows');
        if (!$force && !is_null($r))
            return $r;
        $user = new \RDF\Graph('uri', $uri);
        $d = $user->SELECT("SELECT ?knows WHERE { <{$uri}> <http://xmlns.com/foaf/0.1/knows> ?knows }");
        $d = $d['results']['bindings'];
        $r = array();
        foreach ($d as $row) {
            $k = $row['knows']['value'];
            $r[$k] = \sites\created_by($k);
        }
        sess('knows', $r);
        sess('knows_TS', REQUEST_TIME);
        return $r;
    }
}
