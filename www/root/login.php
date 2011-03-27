<?php
/* login.php
 * application login redirector
 *
 * $Id$
 */

if (isset($i_display) && $i_display == 'popup') {
    $next = newQSA(array('display'=>NULL));
    echo "<script>opener.document.location = '$next';window.close();</script>";
} elseif (isset($i_id) && $i_id == 'facebook' && isset($i_session)) {
    $session = json_decode($i_session, true);
    if (isset($session['access_token'])) {
        $q = json_decode(file_get_contents('https://graph.facebook.com/me?fields=id,name,link,username,email&access_token='.$session['access_token']), true);
        if (isset($q['id'])) {
            foreach ($q as $k=>$v) {
                sess('f:'.$k, $v);
            }
            sess('f:id', 'https://graph.facebook.com/'.$q['id']);
            sess('f:access_expires', $session['expires']);
            sess('f:access_token', $session['access_token']);
        }
    }
    header('Location: https://'.BASE_DOMAIN.'/login');
} elseif (!$_user && !isHTTPS()) {
    header('Location: https://'.BASE_DOMAIN.'/login?'.newQSA());
} elseif (!$_user) {
    require_once('401.php');
} else {
    header('Location: https://'.BASE_DOMAIN.'/manage');
}
