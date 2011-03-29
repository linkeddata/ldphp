<?php
/* index.php
 * service index page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<table>
    <thead>
    <tr>
        <th>Index for <?=$_SERVER['REQUEST_URI']?></th>
        <th>Formats</th>
        <th>Last Modified</th>
        <th>Creator</th>
        <th>ACL (R/W)</th>
    </tr>
</thead>
<?php
$listing = scandir($_filename);
if (count(explode('/', $_base)) <= 4) {
    $listing = array_slice($listing, 2);
} else {
    $listing = array_slice($listing, 1);
}
foreach($listing as $item) {
    $is_dir = is_dir("$_filename/$item");
    if ($is_dir) {
        $item = "$item/";
        printf('<tr><td><a href="%s">%s</a></td><td>Directory</td>', $item, $item);
    } else {
        printf('<tr><td><a href="%s">%s</a></td><td>Turtle (default)', $item, $item);
        foreach (array(
            '.json?callback=load'=>'JS',
            '.json'=>'JSON',
            '.rdf'=>'RDF/XML',
            '?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D'=>'SPARQL',
            '?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D&callback=load'=>'SPARQL/JS'
        ) as $ext=>$label) {
            printf(', <a href="%s%s">%s</a>', $item, $ext, $label);
        }
        echo '</td>';
    }
    echo '<td>'.strftime('%c %Z', filemtime("$_filename/$item")).'</td>';
    echo '<td>'.$_domain_data['http://data.fm/ns/schema#owner'][0]['value'].'</td>';
    echo '<td>'.substr(strstr($_domain_data['http://data.fm/ns/schema#aclRead'][0]['value'],'#'), 1);
    echo '/'.substr(strstr($_domain_data['http://data.fm/ns/schema#aclWrite'][0]['value'],'#'), 1);
    echo '</td>';
    echo '</td></tr>';
}
?>
</table>
<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
