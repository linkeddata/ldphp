<?php
/* 403-404.php
 * application HTTP 403/404 page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div class="clear left error">The requested URI is inaccessible or does not exist.</div>
<div class="clear left">You might try using your WebID to:
<ul>
<?php
if (!isHTTPS()) {
    $ssl_uri = "https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
?>
    <li><a href="<?=$ssl_uri?>">access this cloud over SSL</a> if its inaccessible</li>
<?php } ?>
    <li><a href="//<?=BASE_DOMAIN?>">create this cloud</a> if it does not exist</li>
</ul></div>
<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
