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
  f.src = "//data.fm/server.html";
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
    $scope.accountCreate = function() {
        console.log($scope.accountName);
    }
  }
</script>

<div class="box center width-1024" ng-controller="Example">
  <div class="span-20 center">
    <div class="span-8">
      <h3>Create Account</h3>
      <form ng-submit="accountCreate()">
        <div class="clear">
      email: <input type="email" ng-model="email" />
        </div>
      <em>https://id.<?=ROOT_DOMAIN?>/</em><input type="text" ng-model="accountName" ng-change="accountStatus()" placeholder="your-username" />
      <input type="button" ng-disabled="!accountStatusData.available" value="OK" />
      </form>
    </div>
    <div class="span-8">
      <h3>Create Storage</h3>
      <form ng-submit="storageCreate()">
      <em>https://<input type="text" ng-model="storageName" ng-change="storageStatus()" placeholder="your-storage-name" />.<?=ROOT_DOMAIN?></em>
      <input type="button" ng-disabled="!storageStatusData.available" value="OK" />
      </form>
    </div>
  </div>
  <div class="clearfix"></div>
</div>
<?php
require('help.php');

TAG(__FILE__, __LINE__, '$Id$');
defined('FOOTER') || include_once('footer.php');
