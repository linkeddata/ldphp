<?php
/* index.php
 * service index page
 *
 * $Id$
 */

defined('HEADER') || include_once('header.php');
?>
<hr />
<h2>Index for <?=$_SERVER['REQUEST_URI']?></h2>
<table>
<?php
$listing = scandir($_filename);
if (count(explode('/', $_base)) <= 4) {
    $listing = array_slice($listing, 2);
} else {
    $listing = array_slice($listing, 1);
}
foreach($listing as $item) {
    if (is_dir("$_filename/$item")) {
        $item = "$item/";
        printf('<tr><td><a href="%s">%s</a></td><td>Directory</td></tr>', $item, $item);
    } else {
        printf('<tr><td><a href="%s">%s</a></td><td>N3/Turtle', $item, $item);
        //foreach (array('.json'=>'JSON','.nt'=>'NTriples','.n3'=>'N3/Turtle','.rdf'=>'RDF/XML','?query='.urlencode('SELECT * WHERE {?s ?p ?o}')=>'SPARQL') as $ext=>$label) {
        foreach (array('.json'=>'JSON','.rdf'=>'RDF/XML','?query=SELECT+%2A+WHERE+%7B%3Fs+%3Fp+%3Fo%7D'=>'SPARQL') as $ext=>$label) {
            printf(', <a href="%s%s">%s</a>', $item, $ext, $label);
        }
        echo '</td></tr>';
    }
}
?>
</table>
<?php
TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
