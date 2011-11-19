<?php
/* MKCOL.php
 * service HTTP MKCOL controller
 *
 * $Id$
 */

require_once('runtime.php');

// permissions
if (empty($_user)) {
    httpStatusExit(401, 'Unauthorized');
} elseif (!wac('Write')) {
    httpStatusExit(403, 'Forbidden');
}

// action
@mkdir($_filename, 0777, true);
