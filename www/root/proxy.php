<?php

require_once('../wildcard/runtime.php');

if (isset($i_uri)) {
    $g = new \RDF\Graph('memory', '', '');
    $g->load($i_uri);
}

require_once('../wildcard/GET.php');
