<?php
/* index.php
 * service index page
 *
 * $Id$
 */

$TITLE = 'Index of '.$_request_url;
defined('HEADER') || include_once('header.php');
?>
<div id="editor" class="notice" style="position: absolute; top: 5%; left: 20%; display: none;">
    <img class="clear right" src="//<?=BASE_DOMAIN?>/common/images/cancel.gif" onclick="$(this).up().hide()" />
    <input class="cleft left" style="margin: 0;" type="text" id="editorpath" placeholder="loading..." />
    <textarea class="clear left" id="editorarea" style="width: 50em; bottom: 2em" disabled="disabled"></textarea>
    <input class="clear right" type="button" value="Save" onclick="cloud.save();" />
</div>
<table id="index" class="cleft left" style="width: auto; min-width: 50%;">
<thead>
    <tr>
        <th colspan=4>Name</th>
        <th>Last Modified</th>
        <th>Size</th>
        <th>Owner</th>
        <th>ACL (R/W)</th>
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
    if ($item[0] == '.' && $item != '..') continue;
    $is_dir = is_dir("$_filename/$item");
    $item_ext = strrpos($item, '.');
    $item_ext = $item_ext ? substr($item, 1+$item_ext) : '';
    $item_elt = $item;
    if (in_array($item_ext, array('sqlite')))
        $item_elt = substr($item_elt, 0, -strlen($item_ext)-1);
    if ($is_dir)
        $item_elt = "$item_elt/";
    echo '<tr><td>';
    if (!$is_dir) {
        echo '<a href="javascript:cloud.edit(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.'/common/images/pencil.gif" /></a> ';
    }
    echo '</td><td>';
    echo '<a href="', $item_elt, '">', $item_elt, '</a>';
    if ($item_ext == 'sqlite')
        echo ' (sqlite)';
    echo '</td><td>';
    if ($is_dir) {
        echo 'Directory';
    } elseif (in_array($item_ext, array('html', 'css', 'js'))) {
        echo 'text/', $item_ext=='js'?'javascript':$item_ext;
    } else {
        echo 'text/turtle';
        $i = 0;
        foreach (array(
            //'.json?callback=load'=>'JS',
            '.json'=>'JSON',
            '.rdf'=>'RDF/XML',
            '?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D'=>'SPARQL',
            //'?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D&callback=load'=>'SPARQL/JS'
        ) as $ext=>$label) {
            echo $i++ ? ', ' : ': ';
            printf('<a href="%s%s">%s</a>', $item_elt, $ext, $label);
        }
    }
    echo '</td><td>';
    echo '<a href="javascript:cloud.rm(\''.$item_elt.'\');"><img src="//'.BASE_DOMAIN.'/common/images/cancel.gif" /></a>';
    echo '</td><td>'.strftime('%c %Z', filemtime("$_filename/$item")).'</td>';
    echo '<td>'.(!$is_dir?filesize("$_filename/$item"):'').'</td>';
    echo '<td>'.$_domain_data['http://data.fm/ns/schema#owner'][0]['value'].'</td>';
    echo '<td>'.substr(strstr($_domain_data['http://data.fm/ns/schema#aclRead'][0]['value'],'#'), 1);
    echo '/'.substr(strstr($_domain_data['http://data.fm/ns/schema#aclWrite'][0]['value'],'#'), 1);
    echo '</td>';
    echo '</td></tr>';
}
?>
</tbody>
<tfoot>
    <tr>
        <td colspan=7>
            <input id="create-name" name="create[name]" type="text" value="" placeholder="Create new..." />
            <input id="create-type-file" name="create[type]" type="button" value="File" onclick="cloud.append($F($(this.parentNode).down()));" />
            <input id="create-type-directory" name="create[type]" type="button" value="Dir" onclick="cloud.mkdir($F($(this.parentNode).down()));" />
        </td>
    </tr>
</tfoot>
</table>
<script type="text/javascript">
$(document).observe('keydown', function(e) {
    if (e.keyCode == 27) { // ESC
        $('editor').hide();
    }
});
</script>
<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
