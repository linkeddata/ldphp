<?php
/* verify.php
 * Verifies assertions returned by the IDP
 *
 * $Id$
 */

// Reference: http://code.google.com/apis/identitytoolkit/v1/reference.html#method_identitytoolkit_relyingparty_verifyAssertion
function verify($continueUrl, $response) {
    $q = array();
    $q['requestUri'] = $continueUrl;
    $q['postBody'] = $response;
    $q = http('POST', 'https://www.googleapis.com/identitytoolkit/v1/relyingparty/verifyAssertion?key='.GIT_KEY, json_encode($q));
    if ($q->status == 200)
        return json_decode($q->body, true);
    return array();
}

$result = verify(REQUEST_URI, @file_get_contents('php://input'));
$email = isset($result['verifiedEmail']) ? strtolower($result['verifiedEmail']) : '';
$name = isset($result['displayName']) ? $result['displayName'] : '';
$firstName = isset($result['firstName']) ? $result['firstName'] : '';
$lastName = isset($result['lastName']) ? $result['lastName'] : '';
if (strlen($email))
    sess('u:id', "mailto:$email");
?>
<script type="text/javascript">
if (opener) {
    opener.location = '/login';
    window.close();
} else {
    window.location = '/login';
}
</script>
