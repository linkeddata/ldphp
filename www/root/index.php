<?php
/* index.php
 * application index
 *
 * $Id$
 */

require_once('runtime.php');

header('X-XRDS-Location: '.REQUEST_BASE.'/yadis');
defined('HEADER') || include_once('header.php');
?>
<?php if ($_options->open) { ?>
<div id="welcome" class="box" style="margin-right: 2em;">
<strong>Welcome!</strong> This <a target="_blank" href="http://www.w3.org/DesignIssues/ReadWriteLinkedData.html">Read/Write</a> <a target="_blank" href="http://www.w3.org/DesignIssues/LinkedData.html">Linked Data</a> service is free (and open-source) for educational and personal use.
</div>
<?php } ?>
<?php if ($_options->editui) { ?>
<div id="login" class="notice" align="center">
    <div style="float: right;"><fb:login-button perms="email"></fb:login-button></div>
    <a href="https://<?=BASE_DOMAIN.$_options->base_url?>/login"><img style="float: left" src="//<?=BASE_DOMAIN.$_options->base_url?>/common/images/loginWebID.png" /></a>
    <br /><br />
    <?php if (defined('GAPIKEY')) { ?>
    <form action="rp_auth" style="float: left; clear: left;">
    <input type="submit" name="provider" value="Gmail" />
    <input type="submit" name="provider" value="AOL" />
    <input type="submit" name="provider" value="Yahoo" />
    </form>
    <?php } ?>
</div>
<div id="fb-root"></div>
<script type="text/javascript" src="//connect.facebook.net/en_US/all.js"></script>
<?php
}
include('help.php');
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
