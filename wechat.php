<?php
return [
	"wechat" => [
		"appid" => "wx5b18b274db7372d6",
		"secret" => "5897191e8562df5e83a2d7e7c519ff7b",
		"cache" => true,//开启缓存
		"accesstokenexpire" => 7200,//access_token缓存时间
		//微信公众号请求链接
		"wechaturl" => [
			"getaccesstoken" => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",//获取access_token
		],
		"redis" => [
			"host" => "192.168.33.10",
			"port" => 6379,
			"database" => 0,
			"prefix" => "waljqiang:",
			"password" => "1f494c4e0df9b837dbcc82eebed35ca3f2ed3fc5f6428d75bb542583fda2170f"
		],
	]
];