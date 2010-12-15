<?php
/* 401.php
 * application HTTP 401 page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div style="padding: 1em; border: 1px solid black; float: right; width: 400px; margin: 0 0 2em">
<p>Use of this service requires a valid WebID:</p>
<ul>
<li><a href="http://foaf.me/">Get a WebID</a> [foaf.me]</li>
<li><a href="http://esw.w3.org/WebID">Learn more about WebID</a> [w3.org]</li>
</ul>
<form action="login" method="post">
<input type="submit" value="Login with WebID" style="float: right" />
</form>
</div>
<?php
include('help.php');
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
