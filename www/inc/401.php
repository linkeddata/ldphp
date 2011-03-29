<?php
/* 401.php
 * application HTTP 401 page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div class="clear left error" style="width: 400px;">You must login to access this URL.</div>

<div class="clear left notice" style="width: 400px;">
If you have just installed a new WebID SSL certificate, you need to close all open browser windows and restart your browser before it will work.
<br /><br />
We strongly recommend using the latest <a target="_blank" href="http://getfirefox.org/">Firefox</a> release with the <a target="_blank" href="http://tabulator.org/">Tabulator extension</a>. Safari is fine. Only the latest, developer builds of Chrome/Chromium are known to work.
</div>

<div style="clear:both"></div>

<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
