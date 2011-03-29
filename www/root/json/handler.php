<?php
/* handler.php
 * JSON services handler
 
 * $Id$
 */

$_method = strtolower($_SERVER['REQUEST_METHOD']);
if (isset($_POST['_method'])) {
    $_method = strtolower($_POST['_method']);
}

$lst = explode('/', $_SERVER['REQUEST_URI']);
foreach ($lst as $elt) {
    array_shift($lst);
    if ($elt == 'json') break;
}
$i_name = array_shift($lst);

if ($_method == 'post') {
    $r = array('available'=>false);
    if (strlen($i_name)) {
        $r['id'] = $i_name;
        $r['available'] = sites\is_available($i_name);
    }
    header('Content-Type: application/json');
    echo json_encode($r);
} elseif ($_method == 'delete') {
    if ($i_name && substr($i_name, -1*strlen(BASE_DOMAIN))!=BASE_DOMAIN) {
        $i_name = $i_name . '.' . BASE_DOMAIN;
    }
    header('Content-Type: text/javascript');
    $r = $sites->remove_any("dns:$i_name");
    echo 'cloud.refresh();';
}
