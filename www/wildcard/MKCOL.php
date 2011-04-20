<?php
/* MKCOL.php
 * service HTTP MKCOL controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
$acl_public = \sites\is_public_write($_domain);
if ($acl_public) {
} elseif (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!\sites\is_owner($_domain, $_user)) {
    httpStatusExit(403, 'Forbidden');
}

// action
@mkdir($_filename, 0777, true);
