<?php
/* header.php
 * page header
 *
 * $Id$
 */

define('HEADER', 1);
if (!isset($TITLE)) {
    $TITLE = 'data cloud';
}
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?=$_SERVER['SERVER_NAME']?>: <?=$TITLE?></title>
    <link rel="stylesheet" href="//<?=BASE_DOMAIN.$_options->base_url?>/common/css/blueprint.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="//<?=BASE_DOMAIN.$_options->base_url?>/common/css/common.css" type="text/css" media="screen, projection" />
    <script src="//<?=BASE_DOMAIN.$_options->base_url?>/common/js/prototype.js" type="text/javascript"></script>
    <script src="//<?=BASE_DOMAIN.$_options->base_url?>/common/js/common.js" type="text/javascript"></script>
    <script src="//<?=BASE_DOMAIN.$_options->base_url?>/common/js/rdflib.js" type="text/javascript"></script>
    <script type="text/javascript">
    cloud.init({request_base:'<?=REQUEST_BASE?>',request_url:'<?=REQUEST_URL?>',user:'<?=$_user?>'});
    </script>
</head>
<body style="padding: 2em">
    <div id="alert" style="position: absolute; top: 0; left: 0; width: 100%; padding-top: 5px; text-align: center; z-index: 1000; display: none;">
        <div id="alertbody" style="display: inline;"></div>
    </div>
    <div id="status"><a target="_blank" href="//<?=BASE_DOMAIN.$_options->base_url?>">
        <img src="//<?=BASE_DOMAIN.$_options->base_url?>/common/images/load_bigroller.gif" style="display: none" id="statusLoading" alt="status: loading" />
        <img src="//<?=BASE_DOMAIN.$_options->base_url?>/common/images/rdf_flyer.24.gif" id="statusComplete" alt="status: complete" />
    </a></div>
    <div id="title"><h2><strong><?=$_SERVER['SERVER_NAME']?></strong>: <?=$TITLE?></h2></div>
    <div id="identity"><?php
    $user_link = sess('u:link');
    if ($user_link) {
        if (stristr(sess('u:id'), 's://graph.facebook.com/'))
            echo '<div class="right"><img src="//', BASE_DOMAIN.$_options->base_url, '/common/images/facebiblio.png" /><a href="//', BASE_DOMAIN.$_options->base_url, '/logout">logout</a></div>';
        echo '<a target="_blank" href="', $user_link, '">';
        echo '<h2 class="right">', sess('u:name'), '</h2>';
        echo '</a>';
    }
    ?></div>
<?php
TAG(__FILE__, __LINE__, '$Id$');
