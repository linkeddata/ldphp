<?php
/* handler.php
 * JSON services handler
 
 * $Id$
 */

require_once('../runtime.php');

$_method = strtoupper($_SERVER['REQUEST_METHOD']);
if (isset($_POST['_method'])) {
    $_method = strtoupper($_POST['_method']);
}

$lst = explode('/', $_SERVER['REQUEST_URI']);
foreach ($lst as $elt) {
    array_shift($lst);
    if ($elt == 'json') break;
}
$i_name = array_shift($lst);

if ($_method == 'POST') {
    header('Content-Type: application/json');
    $r = array('available'=>false);
    if (strlen($i_name)) {
        $r['id'] = $i_name;
        $r['available'] = \sites\is_available($i_name);
    }
    echo json_encode($r);
} elseif ($_method == 'DELETE') {
    header('Content-Type: text/javascript');
    if ($i_name && substr($i_name, -1*strlen($_ENV['CLOUD_BASE']))!=$_ENV['CLOUD_BASE']) {
        $i_name = $i_name . $_ENV['CLOUD_BASE'];
    }
    $r = $sites->remove_any("dns:$i_name");
    echo 'cloud.refresh();';
}
