<?php
/* footer.php
 * page footer
 *
 * $Id$
 */

TAG(__FILE__, __LINE__, '$Id$');
$time = $TAGS[count($TAGS)-1]['time']-$TAGS[0]['time'];
$caller = $TAGS[count($TAGS)-2];
?>
<hr style="margin-bottom: 0;"/>
<address>
<span id="id"><?=$caller['id']?></span>
<span id="stat">generated in <?=substr($time, 0, 6)?>s</span>
</address>
</body>
</html>
