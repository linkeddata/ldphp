<?php
/* MKCOL.php
 * service HTTP MKCOL controller
 *
 * $Id$
 */

require_once('runtime.php');

// permissions
$acl_public = \sites\is_public_write($_domain);
if ($acl_public) {
} elseif (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!\sites\is_owner($_domain, $_user) && !wac('Write')) {
    httpStatusExit(403, 'Forbidden');
}

// action
@mkdir($_filename, 0777, true);
