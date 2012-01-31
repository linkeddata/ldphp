<?php

require_once('../inc/runtime.php');

$_options->linkmeta = false;

require_once('../wildcard/runtime.php');

if (isset($i_uri)) {
    $g = new \RDF\Graph('memory', '', '');
    $g->load($i_uri);
}

require_once('../wildcard/GET.php');
