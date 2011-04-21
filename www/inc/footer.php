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
if ($_showCode) {
?>
<hr style="margin-top: 1em; margin-bottom: 0;" />
<address>
<?php
$src = explode('/', $_SERVER['SCRIPT_FILENAME']);
$src = array_slice($src, array_search('www', $src));
$src = implode('/', $src);
$src = "http://dig.xvm.mit.edu/redmine/projects/data-fm/repository/entry/trunk/$src";
?>
<span id="codeID" style="display: none;"><a href="<?=$src?>"><?=$caller['id']?></a></span>
<span id="codeTime" onclick="$('codeID').toggle();">generated in <?=substr($time, 0, 6)?>s
<?=$sparql_n<1?'':sprintf('with %d quer%s in %ss', $sparql_n, $sparql_n>1?'ies':'y', substr($sparql_t, 0, 6))?></span>
</address>
<?php } ?>
</body>
</html>
