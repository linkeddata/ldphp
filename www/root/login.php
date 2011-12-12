<?php
/* login.php
 * application login redirector
 *
 * $Id$
 */

require_once('runtime.php');

if (isset($i_display) && $i_display == 'popup') {
    $next = newQSA(array('display'=>NULL));
    echo "<script>opener.document.location = '$next';window.close();</script>";
} elseif (isset($i_id) && $i_id == 'facebook' && isset($i_session)) {
    $i_session = str_replace('\\', '', $i_session);
    $session = json_decode($i_session, true);
    if (isset($session['access_token'])) {
        $q = json_decode(file_get_contents('https://graph.facebook.com/me?fields=id,name,picture,link,username,email&access_token='.$session['access_token']), true);
        if (isset($q['id'])) {
            sess('f:id', $q['id']);
            sess('f:access_expires', $session['expires']);
            sess('f:access_token', $session['access_token']);
            sess('u:name', $q['name']);
            sess('u:link', $q['link']);
            $q['id'] = 'https://graph.facebook.com/'.$q['id'];
            sess('u:id', $q['id']);
            $sites->append('turtle', "<{$q['id']}> <http://xmlns.com/foaf/0.1/mbox> <mailto:{$q['email']}>.");
        }
    }
    header('Location: '.REQUEST_BASE.'/login');
} elseif (!$_user && !isHTTPS()) {
    header('Location: https://'.BASE_DOMAIN.$_options->base_url.'/login?'.newQSA());
} elseif (!$_user) {
    require_once('401.php');
} else {
    header('Location: '.REQUEST_BASE.'/manage');
}
