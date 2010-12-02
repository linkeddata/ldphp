<?php
/* index.php
 * service index page
 *
 * $Id$
 */

include_once('header.php');
?>
<hr />
<h2>Index for <?=$_SERVER['REQUEST_URI']?></h2>
<ul>
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
    }
    printf('<li><a href="%s">%s</a></li>', $item, $item);
}
?>
</ul>
<?php
TAG(__FILE__, __LINE__, '$Id$');
include_once('footer.php');
