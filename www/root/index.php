<?php
/* index.php
 * application index
 */

require_once('runtime.php');

header('X-XRDS-Location: '.REQUEST_BASE.'/yadis');
defined('HEADER') || include_once('header.php');

?>
<div class="box welcome center width-1024">
 <strong> Welcome!</strong> This <a class="white link" target="_blank" href="http://www.w3.org/DesignIssues/ReadWriteLinkedData.html">Read/Write</a> <a class="white link" target="_blank" href="http://www.w3.org/DesignIssues/LinkedData.html">Linked Data</a> service is free (and open-source) for educational and personal use.
</div>

<script>
  f = document.createElement('iframe');
  f.src = "//ldphp.tk/server.html";
  f.style.display = 'none';
  ngLD = angular.module('LD', ['ui','ui.filters']);
  function Example($scope, $timeout) {
    document.body.appendChild(f);
    window.addEventListener("message", function(ev) {
      $scope.$apply(function() {
        $scope[ev.data.method+'Data'] = ev.data.response;
      });
    });
    $scope.storageStatus = function() {
      f.contentWindow.postMessage({method: 'storageStatus', storageName: $scope.storageName }, "*");
    }
    $scope.accountStatus = function() {
      f.contentWindow.postMessage({method: 'accountStatus', accountName: $scope.accountName }, "*");
    }
    $scope.storageCreate = function() {
        window.location='https://'+$scope.storageName+'.<?=ROOT_DOMAIN?>';
    }
  }
</script>

<div class="box center width-1024" ng-controller="Example">
  <div class="span-20 center">
    <div class="span-7">
      <h3>Create Account</h3>
      <form method="POST" action="/api/spkac.php" target="spkac">
        <keygen name="SPKAC" challenge="randomchars" keytype="rsa" style="display:none" />
          <input type="text" name="name" ng-model="name" placeholder="Your Name" class="clear" />
          <input type="email" name="email" ng-model="email" placeholder="you@somewhere.com" class="clear" />
        <em>https://id.<?=ROOT_DOMAIN?>/</em><input type="text" name="username" ng-model="accountName" ng-change="accountStatus()" placeholder="your-username" />
        <button type="submit" ng-disabled="!accountStatusData.available">OK</button>
      </form>
      <br />see also <a href="https://webid.mit.edu/">MIT WebID Service</a>
      <br />or login with <a href="login?provider=Gmail">Google</a>, <a href="login?provider=AOL">AOL</a>, <a href="login?provider=Yahoo">Yahoo</a>
    </div>
    <div class="span-8 push-2">
      <h3>Create Storage</h3>
      <form ng-submit="storageCreate()">
      <em>https://<input type="text" ng-model="storageName" ng-change="storageStatus()" placeholder="your-storage-name" />.<?=ROOT_DOMAIN?></em>
      <button type="submit" ng-disabled="!storageStatusData.available">OK</button>
      </form>
    </div>
    <iframe class="push-2" name="spkac" style="height: 5em"></iframe>
  </div>
  <div class="clearfix"></div>
</div>
<?php
require('help.php');

TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
