<?php
/* index.php
 * application index
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div id="login" class="notice" align="center">
    This service requires a<br /><a target="_blank" href="http://www.w3.org/wiki/WebID">WebID</a> or Facebook Login:<br /><br />
    <a href="https://<?=BASE_DOMAIN?>/login"><img style="float: left" src="/common/images/loginWebID.png" /></a>
    <div style="float: right;"><fb:login-button perms="email"></fb:login-button></div>
</div>
<div id="fb-root"></div>
<script type="text/javascript" src="//connect.facebook.net/en_US/all.js"></script>
<?php
include('help.php');
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
