<?php
/* footer.php
 * page footer
 *
 * $Id$
 */

define('FOOTER', 1);
TAG(__FILE__, __LINE__, '$Id$');
$time = $TAGS[count($TAGS)-1]['time']-$TAGS[0]['time'];
$caller = $TAGS[count($TAGS)-2];
$sparql_n = 0;
$sparql_t = 0;
if (isset($timings)) {
    $sparql_n = count($timings);
    foreach ($timings as $t) {
        $sparql_t += $t['time'];
    }
}
?>
<hr style="margin-bottom: 0;"/>
<address>
<span id="id"><?=$caller['id']?></span>
<span id="stat">generated in <?=substr($time, 0, 6)?>s
<?=$sparql_n<1?'':sprintf('with %d SPARQL quer%s in %ss', $sparql_n, $sparql_n>1?'ies':'y', substr($sparql_t, 0, 6))?></span>
</address>
</body>
</html>
