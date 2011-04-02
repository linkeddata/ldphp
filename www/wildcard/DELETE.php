<?php
/* DELETE.php
 * service HTTP DELETE controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

if (!count($_domain_data) || !\sites\is_owner($_domain, $_user))
    httpStatusExit(403, 'Forbidden');

if (!file_exists($_filename))
    httpStatusExit(404, 'Not Found');

if (is_dir($_filename)) {
    rmdir($_filename);
} else {
    unlink($_filename);
}

if (file_exists($_filename))
    httpStatusExit(409, 'Conflict');
