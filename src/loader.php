<?php
namespace Waljqiang\Wechat;

use Illuminate\Container\Container;
use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Redis;
use GuzzleHttp\Client;

//加载配置
define("WECHATURL",__DIR__ . "/Configs/wechat.php");
$wechat = require WECHATURL;
Wechat::$cache = $wechat["wechat"]["cache"];
Wechat::$wechatUrl = $wechat["wechat"]["wechaturl"];
Wechat::$container = new Container;
//加载缓存
Wechat::$container->singleton("Redis",function() use ($wechat){
	return new Redis([
		"host" => $wechat["wechat"]["redis"]["host"],
		"port" => $wechat["wechat"]["redis"]["port"],
		"database" => $wechat["wechat"]["redis"]["database"]
	],[
		"prefix" => $wechat["wechat"]["redis"]["prefix"],
		"parameters" => [
			"password" => $wechat["wechat"]["redis"]["password"]
		]
	]);
});
//加载http客户端
Wechat::$container->singleton("HttpClient",function(){
	return new Client();
});

if(Wechat::$cache){
	Wechat::$accessTokenExpire = $wechat["wechat"]["accesstokenexpire"];
}