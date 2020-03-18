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
	//客服发消息
/*	$message = [
		"content" => "你好"
	];
	$res = Wechat::getInstance()->kfSendMessage("o9lXF0oPTBOMS44dILU1kfZMlra0",Waljqiang\Wechat\Handles\Message::TEXT,$message);
	var_dump($res);*/
	//加密消息
	// 第三方发送消息给公众平台
	/*$encodingAesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
	$token = "pamtest";
	$timeStamp = "1409304348";
	$nonce = "123456";
	$appid = "wx5b18b274db7372d6";
	$text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";
	Wechat::getInstance()->initDecrypt($token,$encodingAesKey,$appid);
	$res = Wechat::getInstance()->encryptMsg($text,$timeStamp,$nonce);
	var_dump("加密后的消息:" . $res);

	$obj = simplexml_load_string($res,'SimpleXMLElement',LIBXML_NOCDATA);
    $obj = json_decode(json_encode($obj),true);
    $msg_sigature = $obj["MsgSignature"];
    $msg_timeStamp = $obj["TimeStamp"];
    $msg_nonce = $obj["Nonce"];
    $msg_encryptMsg = $obj["Encrypt"];
    $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	$formatXml = sprintf($format, $obj["Encrypt"]);
	$res = Wechat::getInstance()->decryptMsg($msg_sigature, $msg_timeStamp, $msg_nonce,$formatXml);
	var_dump("解密后的消息:" . $res);*/
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
	$res = Wechat::getInstance()->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce);
	echo "-------------------------------------------------" . "</br>"; 
	echo "明文消息" . "</br>";
	var_dump($res);*/

	//加密,注意配置文件中encode要设置为true
	/*$appid = "wx5b18b274db7372d6";
	$text = "<xml>
  <ToUserName><![CDATA[toUser]]></ToUserName>
  <FromUserName><![CDATA[FromUser]]></FromUserName>
  <CreateTime>123456789</CreateTime>
  <MsgType><![CDATA[event]]></MsgType>
  <Event><![CDATA[subscribe]]></Event>
</xml>";
	$encrypt = Wechat::getInstance()->encryptMsg($text);
	echo "-------------------------------------------------" . "</br>";
	echo "密文消息" . "</br>";
	var_dump($encrypt);
	echo "</br>";

	$obj = simplexml_load_string($encrypt,'SimpleXMLElement',LIBXML_NOCDATA);
    $obj = json_decode(json_encode($obj),true);
    $msg_sigature = $obj["MsgSignature"];
    $msg_timeStamp = $obj["TimeStamp"];
    $msg_nonce = $obj["Nonce"];
    $msg_encryptMsg = $obj["Encrypt"];
    $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	$formatXml = sprintf($format, $obj["Encrypt"]);

	$res = Wechat::getInstance()->handleWechatMessage($formatXml,$appid,$msg_sigature,$msg_timeStamp,$msg_nonce);
	echo "-------------------------------------------------" . "</br>";
	echo "密文解密后消息" . "</br>";
	var_dump($res);
	echo "</br>";
	echo "-------------------------------------------------" . "</br>";*/
	//公众号回复消息
	/*$message = [
		"ToUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"FromUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"Content" => "您好"
	];
	Wechat::getInstance()->replyUser(Waljqiang\Wechat\Handles\Message::TEXT,$message);*/
	//设置所属行业
/*	$res = Wechat::getInstance()->setIndustry("1","4");
	var_dump($res);*/
	//获取所属行业
/*	$res = Wechat::getInstance()->getIndustry();
	var_dump($res);*/
	//获取模板id
	/*$res = Wechat::getInstance()->getTemplateID("TM00015");
	var_dump($res);*/
	//获取模板列表
	/*$res = Wechat::getInstance()->getTemplateList();
	var_dump($res);*/
	//删除模板
/*	$res = Wechat::getInstance()->deleteTemplate("123");
	var_dump($res);*/
	//发送模板消息
/*	$message = [
		"first" => [
			"value" => "恭喜你购买成功",
			"color" => "#173177"
		],
		"keyword1" => [
			"value" => "巧克力",
			"color" => "#173177"
		],
		"keyword2" => [
			"value" => "39.8$",
			"color" => "#173177"
		],
		"keyword3" => [
			"value" => "2014年9月22日",
			"color" => "#173177"
		],
		"remark" => [
			"value" => "欢迎再次购买",
			"color" => "#173177"
		]
	];
	$res = Wechat::getInstance()->sendTemplate("o9lXF0oPTBOMS44dILU1kfZMlra0","ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",$message);
	var_dump($res);*/
	//上传图片
/*	$res = Wechat::getInstance()->uploadImage("./timg.jpg");
	var_dump($res);*/
	//创建门店
	/*$data = [
		"sid" => "33788392",
		"business_name" => "15个汉字或30个英文字符内",
		"branch_name" => "不超过10个字，不能含有括号和特殊字符",
		"province" => "不超过10个字",
		"city" => "不超过30个字",
		"district" => "不超过10个字",
		"address" => "门店所在的详细街道地址（不要填写省市信息）：不超过80个字",
		"telephone" => "不超53个字符（不可以出现文字）",
		"categories" => ["美食,小吃快餐"],
		"offset_type" => 1,
		"longitude" => 115.32375,
		"latitude" => 25.097486,
		"photo_list" => [
			[
				"photo_url" => "http://mmbiz.qpic.cn/mmbiz_jpg/5JvjiczLQWUxW8a2wgkTyYicAI9Ylf6sicPXyvJxU2PmeRvACPyJ9xy7Tk9LIibGO25BhVQiciaYYQMPdCviayiblU6H0w/0"
			]
		],
		"recommend" => "不超过200字。麦辣鸡腿堡套餐，麦乐鸡，全家桶",
		"special" => "不超过200字。免费wifi，外卖服务",
		"introduction" => "不超过300字。麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上大约拥有3 万间分店。
		主要售卖汉堡包，以及薯条、炸鸡、汽水、冰品、沙拉、 水果等快餐食品",
		"open_time" => "8:00-20:00",
		"avg_price" => 35
	];
	$res = Wechat::getInstance()->createShop($data);
	var_dump($res);*/
	//查询门店信息
	/*$res = Wechat::getInstance()->getShop("271262077");
	var_dump($res);*/
	//查询门店列表
	/*$res = Wechat::getInstance()->getShopList();
	var_dump($res);*/
	//修改门店信息
	/*$data = [
		"poi_id" => "271864249",
		"sid" => "A00001",
		"telephone " => "020-12345678",
		"photo_list" => [
			[
				"photo_url" => "http://mmbiz.qpic.cn/mmbiz_jpg/5JvjiczLQWUxW8a2wgkTyYicAI9Ylf6sicPXyvJxU2PmeRvACPyJ9xy7Tk9LIibGO25BhVQiciaYYQMPdCviayiblU6H0w/0"
			]
		],
		"recommend" => "麦辣鸡腿堡套餐，麦乐鸡，全家桶",
		"special" => "免费wifi，外卖服务",
		"introduction" => "麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上大约拥有3 万间分店。主要售卖汉堡包，以及薯条、炸鸡、汽水、冰品、沙拉、水果等快餐食品",
		"open_time" => "8:00-20:00",
		"avg_price" => 35
	];
	$res = Wechat::getInstance()->modifyShop($data);
	var_dump($res);*/
	//身份证识别
/*	$res = Wechat::getInstance()->identityCard("./my.jpg");
	var_dump($res);*/
	//银行卡识别
/*	$res = Wechat::getInstance()->bankCard("./my.jpg");
	var_dump($res);*/
	//获取wifi门店列表
/*	$res = Wechat::getInstance()->getWifiShopList(1,10);
	var_dump($res);*/
	//查询wifi门店信息
/*	$res = Wechat::getInstance()->getWifiShop(429620);
	var_dump($res);*/
	//修改wifi门店信息
	/*$data = [
		"shop_id" => 429620,
	    "old_ssid" => "WX123",
	    "ssid" => "WX567"
	];
	$res = Wechat::getInstance()->modifyWifiShop($data);
	var_dump($res);*/
	//添加密码型设备
	/*$res = Wechat::getInstance()->addPasswordDevice(429620,"WX123","123456789");
	var_dump($res);*/
	//添加portal设备
	/*$res = Wechat::getInstance()->addPortalDevice(429620,"123");
	var_dump($res);*/
	//获取设备列表
/*	$res = Wechat::getInstance()->getDeviceList();
	var_dump($res);*/
	//配置连网方式
	/*$res = Wechat::getInstance()->wifiQrcode(429620,"WX123");
	var_dump($res);*/
	//查询wifi数据统计
	/*$res = Wechat::getInstance()->getWifiStatistics("2020-03-01","2020-03-12");
	var_dump($res);*/
	//设置门店卡券信息
	/*$res = Wechat::getInstance()->setWifiCoupon(
		[
			"shop_id" => 429620,
			"card_id" => "pBnTrjvZJXkZsPGwfq9F0Hl0WqE",
		 	"card_describe" => "10元代金券",
		  	"start_time" => 1457280000,
		  	"end_time" => 1457712000
		]
	);
	var_dump($res);*/
	//查询门店卡券投放信息
/*	$res = Wechat::getInstance()->getWifiCoupon(429620);
	var_dump($res);*/
	//微信支付统一下单接口
	//生成支付二维码
/*	$data = [
		"body" => "商品描述",
		"attach" => "附加数据",
		"total_fee" => "888",
		"expire" => 300,
		"goods_tag" => "WXG",
		"trade_type" => "NATIVE",
		"product_id" => "12235413214070356458058",
		"detail" => [
		    "cost_price" => 608800, 
		    "receipt_id" => "wx123", 
		    "goods_detail" => [ //注意goods_detail字段的格式为"goods_detail":[[]],较多商户写成"goods_detail":[]
		        [
		            "goods_id" => "商品编码", 
		            "wxpay_goods_id" => "1001", 
		            "goods_name" => "", 
		            "quantity" => 1, 
		            "price" => 528800
		        ], 
		        [
		            "goods_id" => "商品编码", 
		            "wxpay_goods_id" => "1002", 
		            "goods_name" => "iPhone6s 32G", 
		            "quantity" => 1, 
		            "price" => 608800
		        ]
		    ]
		]
	];*/
	//JSAPI示例
	/*$data = [
		"body" => "商品描述",
		"attach" => "附加数据",
		"total_fee" => "608800",
		"expire" => 300,
		"goods_tag" => "WXG",
		"trade_type" => "JSAPI",
		"product_id" => "12235413214070356458058",
		"openid" => "oUpF8uMuAJO_M2pxb1Q9zNjWeS6o"
	];*/
	//H5示例
	/*$data = [
		"body" => "商品描述",
		"attach" => "附加数据",
		"total_fee" => "608800",
		"expire" => 300,
		"goods_tag" => "WXG",
		"trade_type" => "MWEB",
		"scene_info" => [
			"h5_info" => [
		   		"type" => "Wap",
		    	"wap_url" => "https://pay.qq.com",
		    	"wap_name" => "腾讯充值"
		    ]
		]
	];*/
/*	$config = [
		"appid" => "wxdaa43d75b815f44e",//微信分配的公众账号ID（企业号corpid即为此appId)
		"mch_id" => "1347427701",//微信支付分配的商户号
		"key" => "otDEIs5YfaplAzvzXS5uBVFS7VD8rb12",
		"appsecret" => "3343b17da99cbd77d32d1d18f68f739a",
		"notify_url" => "http://www.yowifi.net/Wechat/wxpayh5",//接收微信支付异步通知回调地址,通知url必须为直接可访问的url,不能携带参数
	];
	$res = Wechat::getInstance()->unifiedOrder($data,$config);
	var_dump($res);*/
	//查询订单
	/*$data = [
		"transaction_id" => "1009660380201506130728806387",
		"out_trade_no" => "20150806125346",
	];
	$res = Wechat::getInstance()->orderQuery($data);
	var_dump($res);*/
	//关闭订单
	$res = Wechat::getInstance()->closeOrder("20150806125346");
	var_dump($res);
}catch(\Exception $e){
	var_dump($e);
}