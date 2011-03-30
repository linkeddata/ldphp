<?php
/* 401.php
 * application HTTP 401 page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<div class="clear left error">
You must login to access this URL
</div>

<div class="clear left notice">
If you have just installed a new WebID SSL certificate,<br />
please restart your browser to trigger its certificate UI
</div>

<div class="clear left info">
We strongly recommend using recent <a target="_blank" href="http://getfirefox.org/">Firefox</a> (&gt;=3.5) with the <a target="_blank" href="http://tabulator.org/">Tabulator extension</a>
<br /><br />
Shipping Safari/IE and developer Chrome builds are also known to work
</div>

<div style="clear:both"></div>

<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
