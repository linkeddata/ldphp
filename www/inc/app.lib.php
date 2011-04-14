<?php
/* app.lib.php
 * application API
 *
 * $Id$
 */

namespace sites {
    $sites = new \RDF\Graph('sqlite', $_ENV['CLOUD_DATA'].'/clouds.sqlite', '', 'http://data.fm/ns/schema');
    function is_valid($name) {
        $r = gettype($name) == 'string' && strlen($name) > 3;
        $r = $r && !in_array($name, array("1234","12345","123456","12345678","654321","admin","administrador","administrateur","administrator","asdf","asdfgh","audio","backlinks","beta","betas","blog","blogs","cache","calendar","calendars","close","cloud","computer","computers","conn","contact","contacts","create","data","database","databases","default","delete","diary","domain","domains","edit","events","facebook","favorite","favorites","forum","forums","free","friend","friends","gallery","gates","google","guest","guests","guestbook","history","info","information","intro","invite","inviter","json","link","linked","linux","live","load","login","love","mail","manage","management","manager","mysql","open","oracle","owner","pass","passwd","password","passwords","photo","photos","post","posts","private","profile","profiles","proxy","public","qwer","read","register","remove","root","schedule","schema","secret","secrets","secure","server","servers","software","source","sources","sparql","standard","student","subscribe","sudo","support","sysop","teacher","temp","test","tests","update","updates","user","users","video","videos","view","views","webid","webids","weblog","webmaster","wiki","wikis","write","wwwadmin"));
        return $r;
    }
    function is_available($name) {
        if (!is_valid($name))
            return false;
        global $sites;
        $domain = "$name.".BASE_DOMAIN;
        $r = false;
        $q = "SELECT ?o WHERE { <dns:$domain> <#owner> ?o }";
        $q = $sites->SELECT($q);
        if (isset($q['results']['bindings']))
            $r = count($q['results']['bindings']) < 1;
        return $r;
    }
    function is_public($domain) {
        global $sites;
        $r = false;
        $q = "SELECT ?o WHERE { <dns:$domain> <#aclRead> <acl#public> }";
        $q = $sites->SELECT($q);
        if (isset($q['results']['bindings']))
            $r = count($q['results']['bindings']) > 0;
        return $r;
    }
    function is_public_write($domain) {
        global $sites;
        $r = false;
        $q = "SELECT ?o WHERE { <dns:$domain> <#aclWrite> <acl#public> }";
        $q = $sites->SELECT($q);
        if (isset($q['results']['bindings']))
            $r = count($q['results']['bindings']) > 0;
        return $r;
    }
    function is_owner($domain, $uri) {
        global $sites;
        $r = false;
        $q = "SELECT ?o WHERE { <dns:$domain> <#owner> <$uri> }";
        $q = $sites->SELECT($q);
        if (isset($q['results']['bindings']))
            $r = count($q['results']['bindings']) > 0;
        return $r;
    }
    function created_by($uri) {
        global $sites;
        $r = array();
        $q = $sites->SELECT("SELECT ?site WHERE { ?site <#owner> <$uri> }");
        if (isset($q['results']['bindings'])) {
            foreach ($q['results']['bindings'] as $row) {
                $r[] = $row['site']['value'];
            }
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
        $d = $user->SELECT("SELECT ?knows WHERE { <$uri> <http://xmlns.com/foaf/0.1/knows> ?knows }");
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
