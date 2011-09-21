<?php
/* manage.php
 * cloud manage page
 *
 * $Id$
 */

if (!$_user || substr($_user, 0, 4) == 'dns:') {
    require_once('401.php');
    exit;
}

$TITLE = 'manage';
include_once('header.php');
?>

<div class="left cleft box colborder">
<h3>new cloud</h3>
<form action="create" method="get" id="create">
    <div class="clear left append-bottom">choose a name: (at least 4 chars)</div>
    <div class="left" style="clear: left; width: 2em">
        <img class="left" src="/common/images/check.gif" style="display: none" id="check_true" />
        <img class="left" src="/common/images/cancel.gif" style="display: none" id="check_false" />
        &nbsp;
    </div>
    <div class="left">
        <label class="right" for="create_name"><?=$_ENV['CLOUD_BASE']?></label>
        <input class="right span-3" name="name" type="text" id="create_name" style="text-align: right; margin: 0" />
    </div>
    <div class="clear right prepend-top append-bottom">
        <input id="create_submit" type="submit" value="create" disabled />
    </div>
</form>
</div>

<div class="left box colborder" style="min-width: 150px;">
<h3>your clouds</h3>
<?php
$d = sites\created_by($_user);
if (!count($d)) {
    ?><p>None found.</p><?php
} else {
    foreach ($d as $site) {
        $site = substr($site, 4);
        $link = strtok(REQUEST_BASE, ':').'://'.$site;
        echo '<div>';
        echo '<a class="left" href="'.$link.'">'.$site.'</a>';
        echo '<img class="right" src="/common/images/cancel.gif" onclick="cloud.remove(\'', $site,'\')">';
        echo '<div class="clear"></div>';
        echo '</div>';
    }
}
?>
</div>

<script type="text/javascript" src="/common/js/manage.js"></script>

<?php
TAG(__FILE__, __LINE__, '$Id$');
include_once('footer.php');
