<?php
require_once __DIR__ . "/shared.php";
use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Decryption\XmlParse;
try{


	$wechat = new Wechat($redis,$logger,[
		"appid" => $wechatConfig["appid"],
		"appSecret" => $wechatConfig["appSecret"],
		"encodingAesKey" => $wechatConfig["encodingAesKey"],
		"token" => $wechatConfig["token"],
		"encoded" => $wechatConfig["encoded"]
	]);
	//$wechat->init($wechatConfig["appid"],$wechatConfig["appSecret"]);

	//接入指引,将微信公众平台发送get请求中的参数传入join方法即可
	//http://xxxxxx?signature=ec8afbdef5c1834e79f08b72c6b914e4ab0af6f0&timestamp=1409304348&nonce=123456&echostr=abcdef
	$signature = "ec8afbdef5c1834e79f08b72c6b914e4ab0af6f0";
	$timestamp = 1409304348;
	$nonce = 123456;
	$echostr = "abcdef";
	$wechat->join($signature,$timestamp,$nonce,$echostr);

	$accessToken = $wechat->getAccessToken();
	var_dump($accessToken);

	//创建菜单
	/*$options = [
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
	$rs = $wechat->setMenu($options);
	var_dump($rs);
	exit;*/
	//查询菜单
	/*$menu = $wechat->getMenu();
	var_dump($menu);
	exit;*/
	//删除菜单
	/*$rs = $wechat->deleteMenu();
	var_dump($rs);
	exit;*/
	//创建公众号标签
	/*$res = $wechat->setTag("店小二1");
	var_dump($res);
	exit;*/
	//获取公众号标签
	/*$res = $wechat->getTag();
	var_dump($res);
	exit;*/
	//删除公众号标签
	/*$res = $wechat->deleteTag(105);
	var_dump($res);
	exit;*/
	//获取公众号标签下的粉丝列表
	/*$res = $wechat->getTagFans(103,1,10);
	var_dump($res);
	exit;*/
	//为用户打标签，一次最多支持20个
	/*$rs = $wechat->tagToUsers(103,["o9lXF0oPTBOMS44dILU1kfZMlra0"]);
	var_dump($rs);
	exit;*/
	//为用户取消标签,一次最多支持50个
	/*$rs = $wechat->tagDelUsers(102,["o9lXF0oPTBOMS44dILU1kfZMlra0"]);
	var_dump($rs);
	exit;*/
	//获取用户标签列表
	/*$res = $wechat->getUserTags("o9lXF0oPTBOMS44dILU1kfZMlra0");
	var_dump($res);
	exit;*/
	//为用户打备注
	/*$res = $wechat->setUserRemark("o9lXF0oPTBOMS44dILU1kfZMlra0",'123');
	var_dump($res);
	exit;*/
	//获取用户基本信息
	/*$res = $wechat->getUserInfo("o9lXF0oPTBOMS44dILU1kfZMlra0");
	var_dump($res);
	exit;*/
	//获取用户列表
	/*$res = $wechat->getUserList(1,10);
	var_dump($res);
	exit;*/
	//生成带参数的二维码
	/*$res = $wechat->getQrcode(12,"QR_SCENE",120);
	var_dump($res);
	exit;*/
	//添加客服账号
	/*$res = $wechat->createKfAccount([
		"kf_account" => "test1@test",
		"nickname" => "客服1",
		"password" => md5("123456")
	]);
	var_dump($res);
	exit;*/
	//修改客服账号
	/*$res = $wechat->modifyKfAccount([
		"kf_account" => "test",
		"nickname" => "客服2",
		"password" => "123456"
	]);
	var_dump($res);
	exit;*/
	//删除客服账号
	/*$res = $wechat->deleteKfAccount([
		"kf_account" => "test",
		"nickname" => "客服1",
		"password" => "123456"
	]);
	var_dump($res);
	exit;*/
	/*$res = $wechat->uploadAvatar("test@wxid","/vagrant/wechat/timg.jpg");
	var_dump($res);
	exit;*/
	//获取客服账号
	/*$res = $wechat->getAllKfAccount();
	var_dump($res);
	exit;*/
	//客服发消息
	/*$message = [
		"content" => "你好"
	];
	$res = $wechat->kfSendMessage("o9lXF0oPTBOMS44dILU1kfZMlra0",Waljqiang\Wechat\Handles\Message::TEXT,$message);
	var_dump($res);
	exit;*/
	//加密消息
	// 第三方发送消息给公众平台
	/*$encodingAesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
	$token = "pamtest";
	$timestamp = "1409304348";
	$nonce = "123456";
	$appid = "wx5b18b274db7372d6";
	$text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";
	//当需要变动加密参数的话可使用init函数
	$wechat->getDecrypt()->init($token,$encodingAesKey,$appid);
	$res = $wechat->getDecrypt()->encryptMsg($text,$timestamp,$nonce);
	var_dump("加密后的消息:" . $res);


	$xmlParse = new Xmlparse;
	$obj = $xmlParse->extract($res,["Encrypt","MsgSignature","TimeStamp","Nonce"]);
	$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	$from_xml = sprintf($format,$obj["Encrypt"]);
	$decryptMsg = $wechat->getDecrypt()->decryptMsg($obj["MsgSignature"],$obj["TimeStamp"],$obj["Nonce"],$from_xml);
	var_dump("解密后的消息:" . $decryptMsg);
	exit;*/
	//公众号推送的消息处理,注意Wechat实例$encoded属性要设置为false
	/*$wechat->encoded = false;
	$message = "<xml>
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
	$res = $wechat->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce);
	echo "-------------------------------------------------" . "</br>"; 
	echo "明文消息" . "</br>";
	var_dump($res);
	exit;*/
	//加密,注意Wechat实例$encoded属性要设置为true
	/*$wechat->encoded = true;
	$appid = "wx5b18b274db7372d6";
	$timestamp = "1409304348";
	$nonce = "123456";
	$text = "<xml>
  <ToUserName><![CDATA[toUser]]></ToUserName>
  <FromUserName><![CDATA[FromUser]]></FromUserName>
  <CreateTime>123456789</CreateTime>
  <MsgType><![CDATA[event]]></MsgType>
  <Event><![CDATA[subscribe]]></Event>
</xml>";
	$encrypt = $wechat->getDecrypt()->encryptMsg($text,$timestamp,$nonce);
	echo "-------------------------------------------------" . "</br>";
	echo "密文消息" . "</br>";
	var_dump($encrypt);
	echo "</br>";

	$xmlParse = new Xmlparse;
	$obj = $xmlParse->extract($encrypt,["Encrypt","MsgSignature","TimeStamp","Nonce"]);
	$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	$msg_sigature = $obj["MsgSignature"];
    $msg_timeStamp = $obj["TimeStamp"];
    $msg_nonce = $obj["Nonce"];
    $msg_encryptMsg = $obj["Encrypt"];
    $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	$formatXml = sprintf($format, $obj["Encrypt"]);

	$res = $wechat->handleWechatMessage($formatXml,$appid,$msg_sigature,$msg_timeStamp,$msg_nonce);
	echo "-------------------------------------------------" . "</br>";
	echo "密文解密后消息" . "</br>";
	var_dump($res);
	echo "</br>";
	echo "-------------------------------------------------" . "</br>";
	exit;*/
	//公众号回复消息
	/*$message = [
		"ToUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"FromUserName" => "o9lXF0oPTBOMS44dILU1kfZMlra0",
		"Content" => "您好"
	];
	$wechat->replyUser(Waljqiang\Wechat\Handles\Message::TEXT,$message);
	exit;*/
	//设置所属行业
	/*$res = $wechat->setIndustry("1","4");
	var_dump($res);
	exit;*/
	//获取所属行业
	/*$res = $wechat->getIndustry();
	var_dump($res);
	exit;*/
	//获取模板id
	/*$res = $wechat->getTemplateID("TM00015");
	var_dump($res);
	exit;*/
	//获取模板列表
	/*$res = $wechat->getTemplateList();
	var_dump($res);
	exit;*/
	//删除模板
	/*$res = $wechat->deleteTemplate("123");
	var_dump($res);
	exit;*/
	//发送模板消息
	/*$message = [
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
	$res = $wechat->sendTemplate("o9lXF0oPTBOMS44dILU1kfZMlra0","ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",$message);
	var_dump($res);
	exit;*/
	//上传图片
	/*$res = $wechat->uploadImage("./timg.jpg");
	var_dump($res);
	exit;*/
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
	$res = $wechat->createShop($data);
	var_dump($res);
	exit;*/
	//查询门店信息
	/*$res = $wechat->getShop("271262077");
	var_dump($res);
	exit;*/
	//查询门店列表
	/*$res = $wechat->getShopList();
	var_dump($res);
	exit;*/
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
	$res = $wechat->modifyShop($data);
	var_dump($res);
	exit;*/
	//删除门店
	/*$res = $wechat->deleteShop("271864249");
	var_dump($res);
	exit;*/
	//身份证识别
	/*$res = $wechat->identityCard("./my.jpg");
	var_dump($res);
	exit;*/
	//银行卡识别
	/*$res = $wechat->bankCard("./my.jpg");
	var_dump($res);
	exit;*/
	//获取wifi门店列表
	/*$res = $wechat->getWifiShopList(1,10);
	var_dump($res);
	exit;*/
	//查询wifi门店信息
	/*$res = $wechat->getWifiShop(429620);
	var_dump($res);
	exit;*/
	//修改wifi门店信息
	/*$data = [
		"shop_id" => 429620,
	    "old_ssid" => "WX123",
	    "ssid" => "WX567"
	];
	$res = $wechat->modifyWifiShop($data);
	var_dump($res);
	exit;*/
	//添加密码型设备
	/*$res = $wechat->addPasswordDevice(429620,"WX123","123456789");
	var_dump($res);
	exit;*/
	//添加portal设备
	/*$res = $wechat->addPortalDevice(429620,"123");
	var_dump($res);
	exit;*/
	//获取设备列表
	/*$res = $wechat->getDeviceList();
	var_dump($res);
	exit;*/
	//配置连网方式
	/*$res = $wechat->wifiQrcode(429620,"WX123");
	var_dump($res);
	exit;*/
	//查询wifi数据统计
	/*$res = $wechat->getWifiStatistics("2020-03-01","2020-03-12");
	var_dump($res);
	exit;*/
	//设置门店卡券信息
	/*$res = $wechat->setWifiCoupon(
		[
			"shop_id" => 429620,
			"card_id" => "pBnTrjvZJXkZsPGwfq9F0Hl0WqE",
		 	"card_describe" => "10元代金券",
		  	"start_time" => 1457280000,
		  	"end_time" => 1457712000
		]
	);
	var_dump($res);
	exit;*/
	//查询门店卡券投放信息
	/*$res = $wechat->getWifiCoupon(429620);
	var_dump($res);
	exit;*/
	//微信支付
	/*$config = [
		"appid" => "wxdaa43d75b815f44e",//微信分配的公众账号ID（企业号corpid即为此appId)
		"mch_id" => "1347427701",//微信支付分配的商户号
		"key" => "otDEIs5YfaplAzvzXS5uBVFS7VD8rb12",
		"appsecret" => "3343b17da99cbd77d32d1d18f68f739a",
		"notify_url" => "http://www.yowifi.net/Wechat/wxpayh5",//接收微信支付异步通知回调地址,通知url必须为直接可访问的url,不能携带参数
	];
	$wechat->setPay($config);*/
	//微信支付统一下单接口
	//生成支付二维码
	/*$data = [
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
	];
	$res = $wechat->getPay()->unifiedOrder($data);
	var_dump($res);
	exit;*/
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
	];
	$res = $wechat->getPay()->unifiedOrder($data);
	var_dump($res);
	exit;*/
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
	];
	$res = $wechat->getPay()->unifiedOrder($data);
	var_dump($res);
	exit;*/
	
	//查询订单
	/*$data = [
		"transaction_id" => "1009660380201506130728806387",
		"out_trade_no" => "20150806125346",
	];
	$res = $wechat->getPay()->orderQuery($data);
	var_dump($res);
	exit;*/
	//关闭订单
	/*$res = $wechat->getPay()->closeOrder(["out_trade_no" => "20150806125346"]);
	var_dump($res);
	exit;*/
	//申请退款
	/*$data = [
		"transaction_id" => "1217752501201407033233368018",
		"out_trade_no" => "1415757673",
		"out_refund_no" => "1415701182",
		"total_fee" => 100,
		"refund_fee" => 100
	];
	$res = $wechat->getPay()->refund($data);
	var_dump($res);*/
	//查询退款
	/*$data = [
		"out_trade_no" => "1415757673"
	];
	$res = $wechat->getPay()->refundQuery($data);
	var_dump($res);*/
	//下载对账单
	/*$data = [
		"bill_date" => "20200316"
	];
	$res = $wechat->getPay()->downloadBill($data);
	var_dump($res);*/
}catch(\Exception $e){
	var_dump($e);
}