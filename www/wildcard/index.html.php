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
    <textarea class="clear left" id="editorarea" style="width: 50em; bottom: 2em" disabled="disabled"></textarea>
    <input class="clear right" type="button" value="Save" onclick="cloud.save();" />
</div>
<?php } ?>
<table id="index" class="cleft left" style="width: auto; min-width: 50%;">
<thead>
    <tr>
        <th colspan=4>Name</th>
        <th>Last Modified</th>
        <th>Size</th>
    </tr>
</thead>
<tbody>
<?php
$listing = array();
if (is_dir($_filename))
    $listing = scandir($_filename);
if (count(explode('/', $_base)) <= 4) {
    $listing = array_slice($listing, 2);
} else {
    $listing = array_slice($listing, 1);
}
foreach($listing as $item) {
    if ($item[0] == '.' && $item != '..' && substr($item, 0, 5) != '.meta') continue;
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

    echo '<tr><td>';
    if ($_options->editui && !$is_dir) {
        echo '<a href="javascript:cloud.edit(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.$_options->base_url.'/common/images/pencil.gif" /></a> ';
    }
    echo '</td><td>';
    echo '<a href="', $item_elt, '">', $item_elt, '</a>';
    if ($item_ext == 'sqlite')
        echo ' (sqlite)';
    echo '</td><td>';
    if ($is_dir) {
        echo 'Directory';
    } elseif (in_array($item_ext, $_RAW_EXT)) {
        echo 'text/', $item_ext=='js'?'javascript':$item_ext;
    } elseif ($_options->editui) {
        echo 'text/turtle';
        $i = 0;
        foreach (array(
            //'.json?callback=load'=>'JS',
            '.json'=>'JSON',
            '?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D+LIMIT+10'=>'SPARQL',
        ) as $ext=>$label) {
            echo $i++ ? ', ' : ': ';
            printf('<a href="%s%s">%s</a>', $item_elt, $ext, $label);
        }
    }
    echo '</td><td>';
    if ($_options->editui)
        echo '<a href="javascript:cloud.rm(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.$_options->base_url.'/common/images/cancel.gif" /></a>';
    echo '</td><td>'.strftime('%F %X %Z', filemtime("$_filename/$item")).'</td>';
    echo '<td>'.(!$is_dir?filesize("$_filename/$item"):'').'</td>';
    echo '</td></tr>';
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
        </td>
    </tr>
</tfoot>
<?php } ?>
</table>
<?php if ($_options->editui) { ?>
<script type="text/javascript">
$(document).observe('keydown', function(e) {
    if (e.keyCode == 27) { // ESC
        $('editor').hide();
    }
});
</script>
<?php
}
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
