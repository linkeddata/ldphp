<?php
/* index.php
 * application index
 *
 * $Id$
 */

header('X-XRDS-Location: '.REQUEST_BASE.'/yadis');
defined('HEADER') || include_once('header.php');
if ($_edit) {
?>
<div id="login" class="notice" align="center">
    Login with <a target="_blank" href="http://www.w3.org/wiki/WebID">WebID</a>, Facebook,<br />Gmail, AOL, or Yahoo:<br /><br />
    <div style="float: right;"><fb:login-button perms="email"></fb:login-button></div>
    <a href="https://<?=BASE_DOMAIN?>/login"><img style="float: left" src="/common/images/loginWebID.png" /></a>
    <br /><br />
    <form action="rely" style="float: left; clear: left;">
    <input type="submit" name="provider" value="Gmail" />
    <input type="submit" name="provider" value="AOL" />
    <input type="submit" name="provider" value="Yahoo" />
    </form>
</div>
<div id="fb-root"></div>
<script type="text/javascript" src="//connect.facebook.net/en_US/all.js"></script>
<?php
}
include('help.php');
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
