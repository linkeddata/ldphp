<?php
/* header.php
 * page header
 */

define('HEADER', 1);
if (!isset($TITLE)) {
    $TITLE = 'data cloud';
}

$user_link = sess('u:link');
$user_pic = '/common/images/nouser.png';

if (substr($_user, 0, 4) == 'dns:') {
    $user_name = $_user;
} else if (is_null(sess('u:name'))) {
    if (is_null($user_name) || !strlen($user_name))
        $user_name = $_user;
} else {
    $user_name = sess('u:name');
    $user_pic = sess('u:pic');
}

?><!DOCTYPE html>
<html class="ng-app">
<head>
    <title><?=$_SERVER['SERVER_NAME']?>: <?=$TITLE?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="/common/css/blueprint.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/common/css/common.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/common/css/font-awesome.min.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/common/css/buttons.css" type="text/css" media="screen, projection" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script>jQuery.noConflict();</script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.1.5/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui/0.4.0/angular-ui.min.js"></script>
    <script src="//w3.scripts.mit.edu/rdflib.js/dist/rdflib.js" type="text/javascript"></script>
    <script src="/common/js/prototype.js" type="text/javascript"></script>
    <script src="/common/js/common.js" type="text/javascript"></script>
    <script type="text/javascript">
    cloud.init({request_base:'<?=REQUEST_BASE?>',request_url:'<?=REQUEST_URL?>',user:'<?=$_user?>'});
    </script>
</head>
<body class="ng-cloak">
    <div id="alert" style="display: none;">
        <div id="alertbody" style="display: inline;"></div>
    </div>
    <div id="title" style="display: none;"><?=$TITLE?></div>
    
    <div id="topnav" class="topnav center">
    <a href="//<?=ROOT_DOMAIN?>"?><img src="/common/images/logo.svg" class="logo-icon left" /></a>
    <span class="title" title="Home"><a href="<?=REQUEST_BASE?>"><?=BASE_DOMAIN?></a>
<?php $paths = explode('/', REQUEST_URL); array_pop($paths);
foreach ($paths as $k=>$v) {
    if ($k > 0)
        echo '<a href="', REQUEST_BASE, implode('/', array_slice($paths, 0, $k+1)), '/">', $v, '</a>';
    echo ' / ';
} ?>
    </span>
    <?php
    if (True || $_SERVER['SERVER_NAME'] != ROOT_DOMAIN) {
        if ($user_link) { ?>
            <div class="login">
                <span class="login-links">
                    <a class="white" href="<?=$user_link?>" target="_blank"><?=$user_name?></a><br />
                    <a class="white" href="?logout">Logout</a>
                </span>
                <a class="white" href="<?=$user_link?>" target="_blank">
                    <img class="login-photo img-border r3" src="<?=$user_pic?>" title="View profile" /></a>
            </div>
        <?php } else { ?> 
            <div class="login"> 
                <span class="login-links"><a class="white" href="https://<?=BASE_DOMAIN?>">WebID Login</a>
                <br/><a class="white" href="#" onclick="showWebID(event)">Get a WebID</a></span>
                <img class="login-photo" src="/common/images/nouser.png" />
            </div>
    <?php
        }
    }
    ?>
    </div>
<?php
TAG(__FILE__, __LINE__, '$Id$');

