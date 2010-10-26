<?php include_once('header.php'); ?>

<p style="float: right"><em><strong>Note:</strong>
Some features of this service are incomplete.<br />
Thanks for your patience as we complete development!</em></p>

<p>Hello, &lt;<?=$_SERVER['REMOTE_USER']?>&gt;. Welcome to your personal RDF cloud manager!</em></p>

<div class="area-dashed">
<h3>new store</h3>

<form action="create" method="get" id="create">
    <p>1. choose a name:<br />
    <sub>case-insensitive, &gt;3 characters</sub>
    </p>

    <div class="span-icon" style="float: left">
    <img src="/assets/images/check.gif" style="display: none" id="check_true" />
    <img src="/assets/images/cancel.gif" style="display: none" id="check_false" />
    </div>

    <input name="name" type="text" id="create_name" class="span-3" style="text-align: right; float:left; margin: 0" />
    <p><label for="create_name">.<?=BASE_DOMAIN?></label></p>

    <p style="text-align: right"><input id="create_check" type="button" value="check" /></p>

    <p>2. select who can see this:<br />
    <sub>you can customize this more later</sub></p>

    <p>
        <input type="radio" name="acl" value="public" class="create_acl" id="acl_public" checked /><label for="acl_public">public</label>
        <input type="radio" name="acl" value="knows" class="create_acl" id="acl_knows" /><label for="acl_knows">foaf:knows</label>
        <input type="radio" name="acl" value="private" class="create_acl" id="acl_private" /><label for="acl_private">private</label>
    </p>

    <p style="text-align: right"><input id="create_submit" type="submit" value="create" disabled /></p>
</form>
</div>

<script type="text/javascript">
var create_name = '';
function check() {
    create_name = $F('create_name');
    var parameters = $('create').serialize(true);
    $('create').disable();
    $('check_true').hide();
    $('check_false').hide();
    new Ajax.Request('json/sites/check', {
        parameters: parameters,
        onComplete: function(r) {
            $('check_'+r.responseJSON.available).show();
            $('create_name').enable();
            $('create_check').enable();
            if (r.responseJSON.available) {
                $('create').enable();
            }
            $('create_name').focus();
        }
    });
}
$('create_name').observe('keyup', function(e) {
    if (create_name != $F('create_name')) {
        $('check_true').hide();
        $('check_false').hide();
    }
});
$('create_name').observe('change', check);
$('create_check').observe('click', check);
$('create').reset();
$('create').disable();
$('create_name').enable();
$('create_check').enable();
$('create_name').focus();
</script>

<div class="area-dashed">
<h3>your stores</h3>
<?php
$d = sites\created_by($_SERVER['REMOTE_USER']);
if (!count($d)) {
    ?><p>None found.</p><?php
} else {
    foreach ($d as $site) {
        echo "<p><a href=\"$site\">$site</a></p>";
    }
}
?>
</div>

<div class="area-dashed">
<h3>your foaf:knows' stores</h3>
<?php
$d = knows\get();
if (!count($d)) {
    ?><p>No foaf:knows were found in your WebID profile.<br /><sub>we're not following sameAs/seeAlso yet</sub></p><?php
} else {
    ?><p>You know <?=count($d)?> others.</p><?php
    $n_visible = 0;
    foreach ($d as $known=>$stores) {
        if (count($stores)) {
            $n_visible += 1;
            echo "<div><a href='$known'>$known</a>";
            echo '<ul>';
            foreach ($stores as $store) {
                echo "<li><a href='$store'>$store</a></li>";
            }
            echo '</ul></div>';
        }
    }
    if ($n_visible < 1) {
        echo "<p>None of their stores are visible to you.</p>";
    }
}
$t = strftime('%c %Z', sess('knows_TS'));
echo "<p>updated: $t <a href='/s?reset=knows'><img src='/assets/images/redo.gif' /></a></p>";
?>
</div>

<?php include_once('footer.php'); ?>
