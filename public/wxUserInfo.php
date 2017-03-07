<?php
require('../Q/WXS.php');
//var_dump(_::$config["appId"]);

if(isset($_SESSION['user'])){ 
 	print_r($_SESSION['user']);
	exit;
}
$APPID=_::$config["appId"];
$REDIRECT_URI='https://itfree.dreamzk.cn/test.php';
$scope='snsapi_base';
$state = 'STATE';
//$scope='snsapi_userinfo';//需要授权
$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
header("Location:".$url);