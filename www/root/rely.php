<?php
/* rely.php
 * Creates the URL used by the IDP to authenticate the user
 *
 * $Id$
 */

// Reference: http://code.google.com/apis/identitytoolkit/v1/reference.html#method_identitytoolkit_relyingparty_createAuthUrl
$url = 'https://www.googleapis.com/identitytoolkit/v1/relyingparty/createAuthUrl?key='.GIT_KEY;
$data = array(
    'continueUrl' => REQUEST_BASE.'/verify',
    'identifier' => strtolower($i_provider).'.com',
);

$q = http('POST', $url, json_encode($data));
if ($q->status == 200) {
    $q = json_decode($q->body);
    if (isset($q->authUri))  {
        header('Location: '.$q->authUri);
        exit;
    }
} else {
    $q = json_decode($q->body);
    if (isset($q->error))
        echo $q->error->message;
}
