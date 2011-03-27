<?php
/* index.php
 * application index
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div style="padding: 1em; border: 1px solid black; float: right; width: 400px; margin: 0 0 2em">
<p>Use of this service requires a WebID or Facebook:</p>
<ul>
<li><a href="http://foaf.me/">Get a WebID</a> [foaf.me]</li>
<li><a href="http://esw.w3.org/WebID">Learn more about WebID</a> [w3.org]</li>
</ul>
<form action="login" method="post">
<div style="float: right"><fb:login-button perms="email"></fb:login-button></div>
<input type="submit" value="Login with WebID" />
</form>
</div>
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
    FB.init({appId: '119467988130777', status: true, cookie: true, xfbml: true});
    FB._login = FB.login;
    FB.login = function(cb, opts) {
        opts['next'] = '<?=REQUEST_BASE?>/login?id=facebook&display=popup';
        return FB._login(cb, opts);
    }
};
(function() {
    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
}());
</script>
<?php
include('help.php');
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
