<?php
/* MKCOL.php
 * service HTTP MKCOL controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

if (!count($_domain_data) || !\sites\is_owner($_domain, $_user))
    httpStatusExit(403, 'Forbidden');

// action
@mkdir($_filename);
