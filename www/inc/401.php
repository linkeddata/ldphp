<?php
/* 401.php
 * application HTTP 401 page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div style="padding: 1em; border: 1px solid black; float: left; width: 400px; margin: 0 0 2em">
<p><strong>You failed to login with a valid WebID:</strong></p>
<ul>
<li><a href="http://foaf.me/">Get a WebID</a> [foaf.me]</li>
<li><a href="http://esw.w3.org/WebID">Learn more about WebID</a> [w3.org]</li>
</ul>
<p><strong>If you have just installed a new WebID, you typically need to close all open browser windows and restart your browser before it will work.</strong></p>
<h4>Browser Compatibility</h4>
<p>We strongly recommend using the latest <a href="http://getfirefox.org/">Firefox</a> release with the <a href="http://tabulator.org/">Tabulator extension</a>. Safari is fine. Only the latest, developer builds of Chrome/Chromium are known to work.</p>
</div>
<div style="clear:both"></div>
<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
