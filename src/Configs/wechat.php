<?php
return [
	"wechat" => [
		"appid" => "wxfae2fb734a9ddf58",
		"secret" => "78fd8a05411fbfd3b1e899d8a64b026a",
		"encode" => FALSE,//消息是否加密
		"publish" => "gh_3c884a361561",//全网发布账号
		"cache" => TRUE,//开启缓存
		"accesstokenexpire" => 7200,//access_token缓存时间
		"commonexpire" => 2592000,//公众缓存时间，包括自定义菜单
		//微信公众号请求链接
		"wechaturl" => [
			"accesstoken" => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",//获取access_token
			"menuset" => "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s",//创建自定义菜单
			"menuget" => "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=%s",//查询自定义菜单
			"menudel" => "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s",//删除菜单
			"tagset" => "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=%s",//创建公众号标签
			"tagget" => "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=%s",//获取公众号标签
			"tagdel" => "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=%s",//删除公众号标签
			"tagfans" => "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=%s",//获取标签下的粉丝列表
			"tagtousers" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=%s",//为用户打标签
			"tagdelusers" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=%s",//为用户取消标签
			"usertags" => "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=%s",//获取用户身上的标签列表
			"userremarkset" => "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=%s",//设置用户备注
			"userinfo" => "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s",//获取用户基本信息
			"userlist" => "https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=%s",//获取用户列表
			"qrcodeticket" => "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s",//二维码ticket
			"qrcode" => "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s",//获取二维码
			"kfcreate" => "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%s",//添加客服账号
			"kfmodify" => "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=%s",//修改客服账号
			"kfdel" => "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=%s",//删除客服账号
			"kfavatar" => "http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=%s&kf_account=%s",//上传客服头像
			"kfget" => "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=%s",//获取所有客服账号
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