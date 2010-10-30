<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

include_once('wildcard.inc.php');

// permissions
// TODO: WACL
if (empty($_user)) {
    $TITLE = '401 Unauthorized';
    header("HTTP/1.1 $TITLE");
    // TODO: if HTTP Accept */x?html
    //include_once('401.php');
    echo "$TITLE\n";
    exit;
}
if (!count($_domain_data) || !\sites\is_created_by($_domain, $_user)) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    // TODO: if HTTP Accept */x?html
    //include_once('403-404.php');
    echo "$TITLE\n";
    exit;
}

@mkdir(dirname($_filename));
$_data = file_get_contents('php://input');

$g = new \RDF\Graph('memory', '', '', $_base);
if (!empty($_input) && $g->append($_input, $_data)) {
    file_put_contents($_filename, (string)$g);
    echo $g->size();
} elseif ($g->append('turtle', $_data)) {
    file_put_contents($_filename, (string)$g);
    echo $g->size();
}

