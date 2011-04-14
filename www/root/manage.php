<?php
/* manage.php
 * cloud manage page
 *
 * $Id$
 */

if (!$_user) {
    require_once('401.php');
    exit;
}

include_once('header.php');
?>

<div class="left cleft box colborder">
<h3>new cloud</h3>
<?php $acls = array('public', 'known', 'private'); ?>
<form action="create" method="get" id="create">
    <div class="clear left append-bottom">1. pick a name: (at least 4 chars)</div>
    <div class="left" style="clear: left; width: 2em">
        <img class="left" src="/common/images/check.gif" style="display: none" id="check_true" />
        <img class="left" src="/common/images/cancel.gif" style="display: none" id="check_false" />
        &nbsp;
    </div>
    <div class="left">
        <label class="right" for="create_name"><?=$_ENV['CLOUD_BASE']?></label>
        <input class="right span-3" name="name" type="text" id="create_name" style="text-align: right; margin: 0" />
    </div>
    <div class="clear left prepend-top">2. default read permissions:<br />
    <?php $i = 0; foreach($acls as $acl) {?>
        <input type="radio" name="aclRead" value="<?=$acl?>" class="create_acl" id="aclRead_<?=$acl?>" <?=$i==0?'checked ':''?>/><label for="aclRead_<?=$acl?>"><?=$acl?></label>
    <?php $i++; } ?>
    </div>
    <div class="clear left prepend-top">3. default write permissions:<br />
    <?php $i = 0; foreach(array_reverse($acls) as $acl) { ?>
        <input type="radio" name="aclWrite" value="<?=$acl?>" class="create_acl" id="aclWrite_<?=$acl?>" <?=$i==0?'checked ':''?>/><label for="aclWrite_<?=$acl?>"><?=$acl?></label>
    <?php $i++; } ?>
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
        echo "<a href=\"$link\">$site</a><a href=\"\"></a>";
        echo '<img class="right" src="/common/images/cancel.gif" onclick="cloud.remove(\'', $site,'\')">';
        echo "<br />";
        foreach($sites->any("dns:$site") as $elt) {
            $p = basename($elt[1]['value']);
            if (substr($p, 0, 10) == 'schema#acl') {
                $p = substr($p, 7);
                $o = basename($elt[2]['value']);
                echo '<dd>', $p, ': ', $o, '</dd>';
            }
        }
        echo '<br />';
    }
}
?>
</div>

<div class="left box colborder">
<h3>your knowns' clouds</h3>
<?php
$d = profile\knows($_user);
if (!count($d)) {
    ?><p>No knowns (friends) were found in your profile.<br /><sub>we're not following sameAs/seeAlso yet</sub></p><?php
} else {
    ?><p>You know <?=count($d)?> others.</p><?php
    $n_visible = 0;
    foreach ($d as $known=>$stores) {
        if (count($stores)) {
            $n_visible += 1;
            echo "<div><a href='$known'>$known</a>";
            echo '<ul>';
            foreach ($stores as $store) {
                $store = substr($store, 4);
                $link = strtok(REQUEST_BASE, ':').'://'.$store;
                echo "<li><a href=\"", $link, "\">$store</a></li>";
            }
            echo '</ul></div>';
        }
    }
    if ($n_visible < 1) {
        echo "<p>None of their clouds are visible to you.</p>";
    }
}
$t = strftime('%c %Z', sess('knows_TS'));
echo "<p>updated: $t <a href='/s?reset=knows'><img src='/common/images/redo.gif' /></a></p>";
?>
</div>

<script type="text/javascript" src="/common/js/manage.js"></script>

<?php
TAG(__FILE__, __LINE__, '$Id$');
include_once('footer.php');
