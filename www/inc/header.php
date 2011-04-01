<?php
/* header.php
 * page header
 *
 * $Id$
 */

define('HEADER', 1);
if (!isset($TITLE)) {
    $TITLE = 'RDF cloud';
}
?><!DOCTYPE html>
<html>
<head>
    <title><?=$_SERVER['SERVER_NAME']?> | <?=$TITLE?></title>
    <link rel="stylesheet" href="//<?=BASE_DOMAIN?>/common/css/blueprint.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="//<?=BASE_DOMAIN?>/common/css/common.css" type="text/css" media="screen, projection" />
    <script src="//<?=BASE_DOMAIN?>/common/js/prototype.js" type="text/javascript"></script>
    <script src="//<?=BASE_DOMAIN?>/common/js/common.js" type="text/javascript"></script>
    <script type="text/javascript">
    cloud.init({request_base:'<?=REQUEST_BASE?>',request_url:'<?=$_SERVER['SCRIPT_URL']?>',user:'<?=$_user?>'});
    </script>
</head>
<body style="padding: 2em">
    <div id="alert" style="position: absolute; top: 0; left: 0; width: 100%; padding-top: 5px; text-align: center; display: none;">
        <div id="alertbody" class="alert" style="display: inline;"></div>
    </div>
    <div id="identity"><?php
    if ($_user_link) {
        if (stristr($_user, 's://graph.facebook.com/'))
            echo '<div class="right"><img src="//', BASE_DOMAIN, '/common/images/facebiblio.png" /><a href="//', BASE_DOMAIN, '/logout">logout</a></div>';
        echo '<a target="_blank" href="', $_user_link, '">';
        echo '<h2 class="right">', $_user_name, '</h2>';
        echo '</a>';
    }
    ?></div>
    <div id="status"><a href="//<?=BASE_DOMAIN?>">
        <img src="//<?=BASE_DOMAIN?>/common/images/load_bigroller.gif" style="display: none" id="statusLoading" />
        <img src="//<?=BASE_DOMAIN?>/common/images/rdf_flyer.24.gif" id="statusComplete" />
    </a></div>
    <div id="title"><h2><strong><?=$_SERVER['SERVER_NAME']?></strong> | <?=$TITLE?></h2></div>
<?php
TAG(__FILE__, __LINE__, '$Id$');
