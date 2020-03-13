<?php
return [
	"wechat" => [
		"appid" => "wxfae2fb734a9ddf58",
		"secret" => "78fd8a05411fbfd3b1e899d8a64b026a",
		"token" => "pamtest",
		"encodingAesKey" => "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG",
		"encode" => FALSE,//消息是否加密
		"publish" => "gh_3c884a361561",//全网发布账号
		"cache" => TRUE,//开启缓存
		"accesstokenexpire" => 7200,//access_token缓存时间
		"commonexpire" => 2592000,//公众缓存时间，包括自定义菜单
		"pay" => [//微信支付配置信息
			"appid" => "wxdaa43d75b815f44e",
			"mch_id" => "1347427701",//商户号
			"key" => "otDEIs5YfaplAzvzXS5uBVFS7VD8rb12",//商户支付密钥
			"appsecret" => "3343b17da99cbd77d32d1d18f68f739a",
			"sslcert_path" => "",//绝对路径
			"sslkey_path" => "",//绝对路径
			"curl_proxy_host" => "0.0.0.0",
			"curl_proxy_port" => 0,
			"report_levenl" => "",
			"notify_url" => ""
		],
		"redis" => [
			"host" => "192.168.33.10",
			"port" => 6379,
			"database" => 0,
			"prefix" => "waljqiang:",
			"password" => "1f494c4e0df9b837dbcc82eebed35ca3f2ed3fc5f6428d75bb542583fda2170f"
		],
		"log" => [
			"enable" => FALSE,
			"channel" => "wechat",//log文件将以此为前缀命名
			"level" => "ERROR",
			"path" => "/vagrant/wechat/example/"
		]
	]
];