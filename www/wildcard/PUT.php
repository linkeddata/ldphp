<?php
/* PUT.php
 * service HTTP PUT controller
 *
 * $Id$
 */

$_filename = $_SERVER['REQUEST_FILENAME'];
if (!strstr($_filename, '/')) {
    $_filename = '/home/'.BASE_DOMAIN."/data/{$_SERVER['SERVER_NAME']}/$_filename";
}

//TODO: negotiation
//TODO: permissions
//TODO: base_uri

@mkdir(dirname($_filename));
file_put_contents($_filename, file_get_contents('php://input'));
