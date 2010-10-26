<?php
$_domain = $_SERVER['SERVER_NAME'];
$_user = $_SERVER['REMOTE_USER'];
$_filename = $_SERVER['REQUEST_FILENAME'];
$d = $sites->SELECT_p_o("http://$_domain/");

// permissions
if (!count($d) || (!\sites\is_public($_domain) && (empty($_user) || !\sites\is_created_by($_domain, $_user)))) {
    header('HTTP/1.1 403 Forbidden');
    $TITLE = '403/404';
    include_once('header.php');
    include_once('403-404.php');
    include_once('footer.php');
    exit;
}

// negotiation
if (substr($_filename, -1) == '/') {
    include_once('index.php');
    //print_r(glob("$_filename*"));
    exit;
}

if (file_exists($_filename)) {
    header('Content-type: text/turtle');
} else {
    include_once('empty.php');
}
