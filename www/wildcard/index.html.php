<?php
/* index.html.php
 * service HTML index page
 *
 * $Id$
 */

require_once('runtime.php');

$TITLE = 'Index of '.$_request_path;
defined('HEADER') || include_once('header.php');
if (!isset($_options->editui)) $_options->editui = true;
if ($_options->editui) {
?>
<div id="editor" class="notice" style="position: fixed; top: 5%; left: 20%; display: none;">
    <img class="clear right" src="//<?=BASE_DOMAIN.$_options->base_url?>/common/images/cancel.gif" onclick="$(this).up().hide()" />
    <input class="cleft left" style="margin: 0;" type="text" id="editorpath" placeholder="loading..." />
    <select id="editorType" class="left" style="margin: 0;" onchange="cloud.edit($F('editorpath'))">
        <option disabled="disabled"></option>
        <option>text/turtle</option>
        <option>text/rdf+xml</option>
        <option>text/rdf+nt</option>
        <option>application/json</option>
        <option>application/json-ld</option>
        <option disabled="disabled">----</option>
        <option>text/css</option>
        <option>text/html</option>
        <option>text/javascript</option>
    </select>
    <textarea class="clear left" id="editorarea" style="width: 50em; bottom: 2em" disabled="disabled"></textarea>
    <input class="clear right" type="button" value="Save" onclick="cloud.save();" />
</div>

<div id="wac-editor" class="notice" style="position: fixed; top: 10%; left: 10%; display: none;">
    <h3>Permissions for <b><span id="wac-path" name="wac-path"></span></b><br/><small><span id="wac-reqpath" name="wac-reqpath"></span></small></h3>
    <input type="hidden" id="wac-exists" value="0" />
    <input type="hidden" id="wac-owner" value="<?=$_user?>" />
    <p>
        <input type="checkbox" id="wac-read" name="Read"> Read
        <input type="checkbox" id="wac-write" name="Write"> Write
        <input type="checkbox" id="wac-recursive" name="Recursively"> Recursively
    </p>
    Allowed identities:
    <br/>
    <small>(comma separated mailto: or http:// addresses OR leave blank for everyone)</small>
    <br/>
    <textarea id="wac-users" name="users" cols="5" rows="5"></textarea>
    <br/>
    <input type="submit" name="wac-save" value="Save" onclick="wac.save()">    
    <input type="button" value="Cancel" onclick="wac.hide()">
</div>
<?php } ?>
<table id="index" class="cleft left" style="width: auto; min-width: 50%;">
<thead>
    <tr>        
        <th>Name</th>
        <th>Type</th>
        <th>Last Modified</th>
        <th>Size</th>
        <th colspan="3">Actions</th>
    </tr>
</thead>
<tbody>
<?php
$listing = array();
if (is_dir($_filename))
    $listing = scandir($_filename);
foreach($listing as $item) {
    $len = strlen($item);
    if (!$len) continue;
    if (($_request_path == '/' && $item == '..') ||
        ($item[0] == '.' && $item != '..' && substr($item, 0, 5) != '.meta'))
        continue;
    $is_dir = is_dir("$_filename/$item");
    $item_ext = strrpos($item, '.');
    $item_ext = $item_ext ? substr($item, 1+$item_ext) : '';
    $item_elt = $item;
    if (in_array($item_ext, array('sqlite')))
        $item_elt = substr($item_elt, 0, -strlen($item_ext)-1);
    if ($is_dir)
        $item_elt = "$item_elt/";
    elseif (isset($_ext) && (!$item_ext || $item_ext == 'sqlite'))
        $item_elt = "$item_elt$_ext";

    echo '<tr>';
    echo '<td><a href="', $item_elt, '">', $item_elt, '</a>';
    if ($item_ext == 'sqlite')
        echo ' (sqlite)';
    echo '</td><td>';
    if ($is_dir) {
        echo 'Directory';
    } elseif (in_array($item_ext, $_RAW_EXT)) {
        echo 'text/', $item_ext=='js'?'javascript':$item_ext;
    } elseif ($_options->editui) {
        echo 'text/turtle';
        
    }
    echo '</td><td>'.strftime('%F %X %Z', filemtime("$_filename/$item")).'</td>';
    echo '<td>'.(!$is_dir?filesize("$_filename/$item"):'-').'</td>';
    echo '</td>';
    echo '<td class="options">';
    if ($_options->editui && !$is_dir) {
        echo '<a href="javascript:cloud.edit(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.$_options->base_url.'/common/images/pencil.gif" title="Edit contents" /></a>';
    }
    echo '</td>';
    echo '<td class="options">';
    echo '<a href="javascript:wac.edit(\''.$_request_path.'\', \''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.$_options->base_url.'/common/images/wac.png" title="Edit access control rules" /></a> ';
    echo '</td>';
    echo '<td class="options">';
    if ($_options->editui)
        echo '<a href="javascript:cloud.rm(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.$_options->base_url.'/common/images/cancel.gif" title="Delete" /></a>';
    echo '</td>';
    echo '</tr>';
}
?>
</tbody>
<?php if ($_options->editui) { ?>
<tfoot>
    <tr>
        <td colspan=7>
            <input id="create-name" name="create[name]" type="text" value="" placeholder="Create new..." />
            <input id="create-type-file" name="create[type]" type="button" value="File" onclick="cloud.append($F($(this.parentNode).down()));" />
            <input id="create-type-directory" name="create[type]" type="button" value="Dir" onclick="cloud.mkdir($F($(this.parentNode).down()));" />
            <input id="create-webid" name="create[webid]" type="button" value="Issue a WebID" />
        </td>
    </tr>
</tfoot>
<?php } ?>
</table>
<table id="webid-gen" style="display:none;">
    <form method="POST" action="">
        <tr><td>Your name: </td><td><input type="text" name="name" size="40" class="required"></td></tr>
        <tr><td>Preferred identifier: </td><td><input type="text" name="path" size="40" value="card#me" class="required"></td></tr>
        <tr><td>Email (recovery): </td><td><input type="text" name="email" size="40"></td></tr>
        <tr><td colspan="2"><keygen name="SPKAC" challenge="randomchars" keytype="rsa" hidden></td></tr>
        <tr><td colspan="2"><input type="submit" value="Generate" onclick="hideWebID()"> <input type="button" value="Cancel" onclick="hideWebID()"></td></tr>
    </form>
</table>
<?php if ($_options->editui) { ?>
<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> -->
<script>
// Open WebID dialog
$('create-webid').observe('click', function(e) {
  $('webid-gen').setStyle({
    top: e.pageY,
    left: e.pageX
  });
  $('webid-gen').show();
});
// Hide WebID dialog
function hideWebID() {
    $('webid-gen').hide();
}
</script>

<script type="text/javascript">
$(document).observe('keydown', function(e) {
    if (e.keyCode == 27) { // ESC
        $('editor').hide();
        $('webid-gen').hide();
        $('wac-editor').hide();
    }
});

</script>
<?php
}
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
