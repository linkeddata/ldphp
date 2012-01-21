<?php

function webid_claim() {
    $r = array();
    if (isset($_SERVER['SSL_CLIENT_CERT'])) {
        $pem = $_SERVER['SSL_CLIENT_CERT'];
        $x509 = openssl_x509_read($pem);
        $pubKey = openssl_pkey_get_public($x509);
        $keyData = openssl_pkey_get_details($pubKey);
        if (isset($keyData['rsa'])) {
            if (isset($keyData['rsa']['n']))
                $r['m'] = strtolower(array_pop(unpack("H*", $keyData['rsa']['n'])));
            if (isset($keyData['rsa']['e']))
                $r['e'] = hexdec(array_shift(unpack("H*", $keyData['rsa']['e'])));
        }

        $d = openssl_x509_parse($x509);
        if (isset($d['extensions']) && isset($d['extensions']['subjectAltName'])) {
            $subjectAltName = $d['extensions']['subjectAltName'];
            if (substr($subjectAltName, 0, 4) == 'URI:')
                $r['uri'] = substr($subjectAltName, 4);
        }
    }
    return $r;
}

function webid_verify() {
    $q = webid_claim();
    if (isset($q['uri'])) {
        $g = new \RDF\Graph('uri', $q['uri'], '', $q['uri']);
        $d = $g->SELECT(sprintf("PREFIX : <http://www.w3.org/ns/auth/cert#> SELECT ?m ?e WHERE { <%s> :key [ :modulus ?m; :exponent ?e; ] . }", $q['uri']));
        if (isset($d['results']) && isset($d['results']['bindings'])) {
            foreach ($d['results']['bindings'] as $elt) {
                if ($q['e'] == $elt['e']['value'] && $q['m'] == strtolower(preg_replace('/[^0-9a-fA-F]/', '', $elt['m']['value']))) {
                    return $q['uri'];
                }
            }
        }
    }
    return '';
}
