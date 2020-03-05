<?php
namespace Waljqiang\Wechat;

use Illuminate\Container\Container;
use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Handles\Base;
use Waljqiang\Wechat\Redis\Redis;
use Waljqiang\Wechat\Redis\Script\MatchDelKeysBy;
use GuzzleHttp\Client;

//加载配置
require_once __DIR__ . "/defined.php";
if(file_exists(CONF)){
	$wechat = require_once CONF;
}else{
	$wechat = require_once __DIR__ . "/Configs/wechat.php";
}

Base::$cache = Wechat::$cache = $wechat["wechat"]["cache"];
Base::$wechatUrl = $wechat["wechat"]["wechaturl"];
Wechat::$config = $wechat["wechat"];
Wechat::$encode = $wechat["wechat"]["encode"];
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
		],
		'profile' => function ($options) {
	        $profile = $options->getDefault('profile');
	        $profile->defineCommand('matchDelCommand', MatchDelKeysBy::class);
	        return $profile;
	    },
	]);
});
//加载http客户端
Wechat::$container->singleton("HttpClient",function(){
	return new Client();
});

if(Wechat::$cache){
	Base::$commonExpire = $wechat["wechat"]["commonexpire"];
	Wechat::$accessTokenExpire = $wechat["wechat"]["accesstokenexpire"];
}