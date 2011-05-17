<?php
/* index.rdf.php
 * service RDF index page
 *
 * $Id$
 */

$g = new \RDF\Graph('memory', '', '', $_base);

$listing = array();
if (is_dir($_filename))
    $listing = scandir($_filename);
if (count(explode('/', $_base)) <= 4) {
    $listing = array_slice($listing, 2);
} else {
    $listing = array_slice($listing, 1);
}
foreach($listing as $item) {
    if ($item[0] == '.' && $item != '..' && $item != '.meta.sqlite') continue;
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

    $mtime = filemtime("$_filename/$item");
    $size = filesize("$_filename/$item");
    $g->append('turtle', "@prefix p: <http://www.w3.org/ns/posix#> . <$item_elt> a p:".($is_dir?'Directory':'File')." ; p:mtime $mtime ; p:size $size .");
}

header("Content-Type: $_output_type");
echo $g->to_string($_output);
