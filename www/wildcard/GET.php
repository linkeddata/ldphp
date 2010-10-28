<?php
$_domain = $_SERVER['SERVER_NAME'];
$_user = $_SERVER['REMOTE_USER'];
$_filename = $_SERVER['REQUEST_FILENAME'];
$_base = $_SERVER['SCRIPT_URI'];
$d = $sites->SELECT_p_o("http://$_domain/");

// permissions
// TODO: WACL
if (!count($d) || (!\sites\is_public($_domain) && (empty($_user) || !\sites\is_created_by($_domain, $_user)))) {
    header('HTTP/1.1 403 Forbidden');
    $TITLE = '403/404';
    include_once('header.php');
    include_once('403-404.php');
    include_once('footer.php');
    exit;
}

if (substr($_filename, -1) == '/') {
    include_once('index.php');
    exit;
}

// TODO: negotiation
if (file_exists($_filename)) {
    //header('Content-type: application/json');
    header('Content-type: text/turtle');
    $w = librdf_php_get_world();
    $s = librdf_new_storage($w, 'memory', '', '');
    $p = librdf_new_parser($w, 'turtle', '', null);
    $m = librdf_new_model($w, $s, '');
    $_file_uri = librdf_new_uri($w, "file://$_filename");
    $_base_uri = librdf_new_uri($w, $_base);
    librdf_parser_parse_into_model($p, $_file_uri, $_base_uri, $m);
    //echo librdf_model_to_string($m, $_base_uri, 'json', '', null);
    echo librdf_model_to_string($m, $_base_uri, 'turtle', '', null);
    exit;
} else {
    include_once('empty.php');
    exit;
}
