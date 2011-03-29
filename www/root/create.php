<?php
/* create.php
 * site creation controller
 *
 * $Id$
 */
$i_name = strtolower($i_name);
$i_acl = strtolower($i_acl);

$domain_uri = "dns:$i_name.".BASE_DOMAIN;
$turtle = "<$domain_uri> <#owner> <$_user>; <#acl> <acl#$i_acl> .";

//TODO: locking
if (sites\is_available($i_name)) {
    @mkdir('/home/'.BASE_DOMAIN."/data/$i_name.".BASE_DOMAIN);
    $sites->append('turtle', $turtle);
}

header('Location: /manage');
