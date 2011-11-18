<?php
/* DELETE.php
 * service HTTP DELETE controller
 *
 * $Id$
 */

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir")
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

require_once('runtime.php');

// permissions
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

if (!\sites\is_owner($_domain, $_user) && !wac('Write'))
    httpStatusExit(403, 'Forbidden');

$frag = strrchr($_SERVER['REQUEST_URI'], '#');
if ($frag) {
    $g = new \RDF\Graph('', $_filename, '', $_SERVER['SCRIPT_URI']);
    $r = $g->remove_any($_SERVER['REQUEST_URI']);
    header('X-Triples: '.$r);
    if ($r)
        $g->save();
    exit;
}

if (is_dir($_filename)) {
    if ($_options->recursive)
        rrmdir($_filename);
    else
        rmdir($_filename);
} elseif (file_exists($_filename)) {
    unlink($_filename);
} else {
    $g = new \RDF\Graph('', $_filename, '', '');
    if ($g->exists()) {
        $g->delete();
    } else {
        httpStatusExit(404, 'Not Found');
    }
}

if (file_exists($_filename))
    httpStatusExit(409, 'Conflict');
