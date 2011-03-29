<?php
/* DELETE.php
 * service HTTP DELETE controller
 *
 * $Id$
 */

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
if (!count($_domain_data) || !\sites\is_owner($_domain, $_user)) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    // TODO: if HTTP Accept */x?html
    //include_once('403-404.php');
    echo "$TITLE\n";
    exit;
}

if (file_exists($_filename)) {
    unlink($_filename);
} else {
    $TITLE = '404 Not Found';
    header("HTTP/1.1 $TITLE");
}
exit;
