<?php
/* create.php
 * site creation controller
 *
 * $Id$
 */

require_once('runtime.php');

$i_name = strtolower($i_name);
$i_acl = strtolower($i_acl);

$base = $_ENV['CLOUD_BASE'];
if (substr($base, 0, 1) != '.') $base = ".$base";

$domain_uri = "dns:$i_name$base";
$turtle = "<$domain_uri> <#owner> <$_user>";
$turtle .= "; <#aclRead> <acl#$i_aclRead>";
$turtle .= "; <#aclWrite> <acl#$i_aclWrite>";
$turtle .= '.';

//TODO: locking
if (sites\is_available($i_name)) {
    @mkdir($_ENV['CLOUD_DATA'].'/'.substr($domain_uri, 4));
    $sites->append('turtle', $turtle);
}

header('Location: /manage');
