<?php
/* header.php
 * page header
 *
 * $Id$
 */

define('HEADER', 1);
if (!isset($TITLE)) {
    $TITLE = 'personal RDF cloud';
}
?><!DOCTYPE html>
<html>
<head>
    <title><?=$_SERVER['SERVER_NAME']?> | <?=$TITLE?></title>
    <link rel="stylesheet" href="//<?=BASE_DOMAIN?>/assets/css/blueprint.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="//<?=BASE_DOMAIN?>/assets/css/common.css" type="text/css" media="screen, projection" />
    <script src="//<?=BASE_DOMAIN?>/assets/js/prototype.js" type="text/javascript"></script>
    <script src="//<?=BASE_DOMAIN?>/assets/js/common.js" type="text/javascript"></script>
    <script type="text/javascript">
    cloud.init({request_base:'<?=REQUEST_BASE?>',user:'<?=$_user?>'});
    </script>
</head>
<body style="padding: 2em">
    <div id="identity">
        <h2><?php if ($_user_picture) { ?>
        <img src="<?=$_user_picture?>" height="25" />
        <?php } if ($_user_name) { ?>
        <a target="_blank" href="<?=$_user_link?>"><?=$_user_name?></a>
        <?php } ?>
        </h2>
    </div>
    <div id="status" style="padding-right: 5px">
        <a href="//<?=BASE_DOMAIN?>"><img src="//<?=BASE_DOMAIN?>/assets/images/rdf_flyer.24.gif" id="statusComplete" /></a>
        <img src="//<?=BASE_DOMAIN?>/assets/images/load_bigroller.gif" style="display: none" id="statusLoading" />
    </div>
    <div id="title">
        <h2><strong><?=$_SERVER['SERVER_NAME']?></strong> | <?=$TITLE?></h2>
    </div>
<?php
TAG(__FILE__, __LINE__, '$Id$');
