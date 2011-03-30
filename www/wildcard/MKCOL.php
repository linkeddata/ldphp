<?php
/* MKCOL.php
 * service HTTP MKCOL controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
if (empty($_user)) {
    $TITLE = '401 Unauthorized';
    header("HTTP/1.1 $TITLE");
    echo "$TITLE\n";
    exit;
}
if (!count($_domain_data) || !\sites\is_owner($_domain, $_user)) {
    $TITLE = '403 Forbidden';
    header("HTTP/1.1 $TITLE");
    echo "$TITLE\n";
    exit;
}

// action
@mkdir($_filename);
