<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Waljqiang\Wechat\Wechat;
try{
	//如果使用配置文件中的公众号则不需要使用init函数，如果要改变微信公众号，则需要使用init方法重新进行初始化
	//Wechat::getInstance()->init("wx5b18b274db7372d6","3cfcde5dfaf9eb01c2762a5aeadf68b2");
	$accessToken = Wechat::getInstance()->getAccessToken();
	var_dump($accessToken);
	//创建菜单
/*	$options = [
		"button" => [
			[
				"type" => "view",
				"name" => "我要下单",
				"url" => "http://www.baidu.com"
			],
			[
				"type" => "click",
				"name" => "个人中心",
				"key" => "V0001_PERSONAL"
			],
			[
				"type" => "click",
				"name" => "关于我们",
				"key" => "V0002_ABOUT"
			]
		]
	];
	$rs = Wechat::getInstance()->setMenu($options);
	var_dump($rs);*/
	//查询菜单
/*	$menu = Wechat::getInstance()->getMenu();
	var_dump($menu);*/
	//删除菜单
/*	$rs = Wechat::getInstance()->deleteMenu();
	var_dump($rs);*/
	//创建公众号标签
/*	$res = Wechat::getInstance()->setTag("店小二1");
	var_dump($res);*/
	//获取公众号标签
	/*$res = Wechat::getInstance()->getTag();
	var_dump($res);*/
	//删除公众号标签
/*	$res = Wechat::getInstance()->deleteTag(105);
	var_dump($res);*/
	//获取公众号标签下的粉丝列表
/*	$res = Wechat::getInstance()->getTagFans(103,1,10);
	var_dump($res);*/
	//为用户打标签，一次最多支持20个
	/*$rs = Wechat::getInstance()->tagToUsers(103,["o9lXF0oPTBOMS44dILU1kfZMlra0"]);
	var_dump($rs);*/
	//为用户取消标签,一次最多支持50个
/*	$rs = Wechat::getInstance()->tagDelUsers(102,["o9lXF0oPTBOMS44dILU1kfZMlra0"]);
	var_dump($rs);*/
	//获取用户标签列表
/*	$res = Wechat::getInstance()->getUserTags("o9lXF0oPTBOMS44dILU1kfZMlra0");
	var_dump($res);*/
	//为用户打备注
/*	$res = Wechat::getInstance()->setUserRemark("o9lXF0oPTBOMS44dILU1kfZMlra0",'123');
	var_dump($res);*/
	//获取用户基本信息
/*	$res = Wechat::getInstance()->getUserInfo("o9lXF0oPTBOMS44dILU1kfZMlra0");
	var_dump($res);*/
	//获取用户列表
/*	$res = Wechat::getInstance()->getUserList(1,10);
	var_dump($res);*/
	//生成带参数的二维码
/*	$res = Wechat::getInstance()->getQrcode(12,"QR_SCENE",120);
	var_dump($res);*/
	//公众号推送的消息处理
/*	$message = "<xml>
  <ToUserName><![CDATA[toUser]]></ToUserName>
  <FromUserName><![CDATA[FromUser]]></FromUserName>
  <CreateTime>123456789</CreateTime>
  <MsgType><![CDATA[event]]></MsgType>
  <Event><![CDATA[subscribe]]></Event>
</xml>";
	$appid = "wx5b18b274db7372d6";
	$signature = "dsdasdfdas";
	$timestamp = "1122321234534";
	$nonce = "123456";
	$echostr = "123adds";
	$res = Wechat::getInstance()->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce,$echostr);
	var_dump($res);*/
	//公众号回复消息
	/*$message = [
		"ToUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"FromUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"Content" => "您好"
	];
	Wechat::getInstance()->replyUser(Waljqiang\Wechat\Handles\Reply::TEXT,$message);*/
	//添加客服账号
	/*$res = Wechat::getInstance()->createKfAccount([
		"kf_account" => "test1@test",
		"nickname" => "客服1",
		"password" => md5("123456")
	]);
	var_dump($res);*/
	//修改客服账号
	/*$res = Wechat::getInstance()->modifyKfAccount([
		"kf_account" => "test",
		"nickname" => "客服2",
		"password" => "123456"
	]);
	var_dump($res);*/
	/*//删除客服账号
	$res = Wechat::getInstance()->deleteKfAccount([
		"kf_account" => "test",
		"nickname" => "客服1",
		"password" => "123456"
	]);
	var_dump($res);*/
/*	$res = Wechat::getInstance()->uploadAvatar("test@wxid","/vagrant/wechat/timg.jpg");
	var_dump($res);*/
	//获取客服账号
	/*$res = Wechat::getInstance()->getKfAccount();
	var_dump($res);*/
}catch(\Exception $e){
	var_dump($e);
}