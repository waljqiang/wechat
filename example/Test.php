<?php
require_once __DIR__ . "/../vendor/autoload.php";
use Waljqiang\Wechat\Wechat;
try{
	//如果使用配置文件中的公众号则不需要使用init函数，如果要改变微信公众号，则需要使用init方法重新进行初始化
	//Wechat::getInstance()->init("wx5b18b274db7372d6","3cfcde5dfaf9eb01c2762a5aeadf68b2");
	$accessToken = Wechat::getInstance()->getAccessToken();
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
	$rs = Wechat::getInstance()->setMenu($options);
	var_dump($rs);*/
	//查询菜单
/*	$menu = Wechat::getInstance()->getMenu();
	var_dump($menu);*/
	//删除菜单
/*	$rs = Wechat::getInstance()->deleteMenu();
	var_dump($rs);*/
	//创建公众号标签
/*	$res = Wechat::getInstance()->setTag("店小二");
	var_dump($res);*/
	//获取公众号标签
/*	$res = Wechat::getInstance()->getTag();
	var_dump($res);*/
	//删除公众号标签
/*	$res = Wechat::getInstance()->deleteTag(134);
	var_dump($res);
*/	//获取公众号标签下的粉丝列表
/*	$res = Wechat::getInstance()->getTagFans(134,1,10);
	var_dump($res);
*/	//为用户打标签，一次最多支持20个
/*	$rs = Wechat::getInstance()->tagToUsers(134,["ocYxcuAEy30bX0NXmGn4ypqx3tI0"]);
	var_dump($rs);*/
	//为用户取消标签,一次最多支持50个
/*	$rs = Wechat::getInstance()->tagDelUsers(134,["ocYxcuAEy30bX0NXmGn4ypqx3tI0"]);
	var_dump($rs);*/
	//获取用户标签列表
/*	$res = Wechat::getInstance()->getUserTags("ocYxcuAEy30bX0NXmGn4ypqx3tI0");
	var_dump($res);
*/	//为用户打备注
/*	$res = Wechat::getInstance()->setUserRemark("ocYxcuAEy30bX0NXmGn4ypqx3tI0",'123');
	var_dump($res);*/
	//获取用户基本信息
	/*$res = Wechat::getInstance()->getUserList(1,10);
	var_dump($res);*/
	//生成带参数的二维码
/*	$res = Wechat::getInstance()->getQrcode(12,"QR_SCENE",120);
	var_dump($res);*/
	//公众号推送的消息处理
	$message = "<xml>
  <ToUserName><![CDATA[toUser]]></ToUserName>
  <FromUserName><![CDATA[fromUser]]></FromUserName>
  <CreateTime>1348831860</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[this is a test]]></Content>
  <MsgId>1234567890123456</MsgId>
</xml>";
	$appid = "";
	$signature = "";
	$timestamp = "";
	$nonce = "";
	$echostr = "";
	$res = Wechat::getInstance()->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce,$echostr);
	var_dump($res);
}catch(\Exception $e){
	var_dump($e);
}


/*use Predis\Command\ScriptCommand;

class MatchDelKeysBy extends ScriptCommand
{
    public function getKeysCount()
    {
        // Tell Predis to use all the arguments but the last one as arguments
        // for KEYS. The last one will be used to populate ARGV.
        return -1;
    }

    public function getScript()
    {
        return <<<LUA
			local keys = redis.call('keys',ARGV[1])
			for i = 1,#keys,10000 do
				redis.call('del',unpack(keys,i,math.min(i+9999,#keys)))
			end
			return #keys
LUA;
    }
}

$client = new Predis\Client([
		"host" => "192.168.33.10",
		"port" => 6379,
		"database" => 0
	],[
		"prefix" => "waljqiang:",
		"parameters" => [
			"password" => "1f494c4e0df9b837dbcc82eebed35ca3f2ed3fc5f6428d75bb542583fda2170f"
		],
	    'profile' => function ($options) {
	        $profile = $options->getDefault('profile');
	        $profile->defineCommand('matchDel', 'MatchDelKeysBy');

	        return $profile;
	    },
]);

//$client->mset('foo', 10, 'foobar', 100);

var_export($client->matchDel('foo*'));*/