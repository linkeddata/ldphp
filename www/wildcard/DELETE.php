<?php
/* DELETE.php
 * service HTTP DELETE controller
 *
 * $Id$
 */

// permissions
// TODO: WACL
if (empty($_user))
    httpStatusExit(401, 'Unauthorized');

if (!count($_domain_data) || !\sites\is_owner($_domain, $_user))
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
