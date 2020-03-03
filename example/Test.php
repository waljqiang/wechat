<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Waljqiang\Wechat\Wechat;
$wechat = new Wechat("wx5b18b274db7372d6","5897191e8562df5e83a2d7e7c519ff7b");
try{
	$accessToken = $wechat->getAccessToken();
	var_dump($accessToken);
}catch(\Exception $e){
	var_dump($e);
}