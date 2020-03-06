<?php
namespace Waljqiang\Wechat;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Handle;
use Waljqiang\Wechat\Handles\Reply;
use Waljqiang\Wechat\Decryption\Decrypt;

/**
 * 公众号处理类
 *
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 * @method 
 * void init($appid,$secret);初始化公众号使用，当不需要使用配置文件中的公众号时，调用其他方法前必须先使用该方法
 * string getAccessToken();获取公众号访问凭证access_token
 * string getAppid();返回公众号使用的appid
 * string getSecret();返回公众号使用的secret
 * string getPublishAccount();返回全网测试账号
 * array request($url,$method = 'GET',$options = []);发送http请求
 *
 * 用户管理相关(Account)
 * string getQrcode($str,$type = "QR_SCENE",$expire = 30);获取公众号二维码
 *
 * 公众号菜单相关(Menu)
 * boolean setMenu($options);设置公众号菜单
 * array getMenu();查询公众号菜单
 * boolean deleteMenu();删除公众号菜单
 *
 * 公众号客服相关(Message)
 * boolean createKfAccount($data);创建客服账号
 * boolean modifyKfAccount($data);修改客服账号
 * boolean deleteKfAccount($data);删除客服账号
 * array getKfAccount();获取所有客服账号
 * boolean uploadAvatar($kfAccount,$imageUrl,$fileName = "");设置公众号客服账号头像
 * boolean kfSendMessage($openID,$messageType,$data);客服发消息
 *
 * 公众号推送消息相关(Receive)
 * array handleWechatMessage($message,$appid = "",$signature = "",$timestamp ="",$nonce = "",$echostr = "");公众号推送的消息
 *
 * 公众号回复用户消息相关(Reply)
 * void replyUser($messageType,$message);回复用户消息
 *
 * 用户管理相关(User)
 * integer setTag($tagName);创建公众号标签
 * array getTag()getTag();获取公众号标签
 * boolean deleteTag($tagID);删除公众号标签
 * boolean tagToUsers($tagID,$openIDs);批量为多个用户打标签,最多支持20个用户
 * boolean tagDelUsers($tagID,$openIDs);批量为多个用户取消标签,最多支持50个用户
 * array getUserTags($openID);获取用户列表
 * array getTagFans($tagID,$pageIndex = 1,$pageOffset = 10);获取公众号标签下的粉丝列表
 * boolean setUserRemark($openid,$remark);为用户打备注
 * array function getUserInfo($openid,$lang="zh_CN");获取用户基本信息
 * array getUserList($pageIndex = 0,$pageOffset = 10);获取用户列表
 *
 * 消息加解密
 * void initDecrypt($token,$encodingAesKey,$appid);初始化加解密类,如果信息使用配置文件则不需要使用此函数
 * string encryptMsg($replyMsg, $timeStamp, $nonce);加密消息
 */
class Wechat{
	/**
	 * access_token缓存key标识
	 */
	const ACCESSTOKEN = "WECHAT:ACCESSTOKEN:";

	/**
	 * [微信公众号appid]
	 */
	private $appid;
	/**
	 * 微信公众号secret
	 */
	private $secret;

	/**
	 * 是否启用缓存
	 * @var boolean
	 */
	public static $cache = true;

	/**
	 * 是否启用消息加密
	 */
	public static $encode = true;

	/**
	 * 微信公众号配置信息
	 * @var array
	 */
	public static $config;

	/**
	 * 类实例
	 * @var [type]
	 */
	public static $container;
	/**
	 * Waljqiang\Wechat\Redis
	 */
	public $redis;

	/**
	 * GuzzleHttp\Client
	 */
	public $httpClient;

	/**
	 * Monolog\Logger
	 * @var [type]
	 */
	public $logger;

	public $log;

	private $decrypt;//加解密类

	/**
	 * access_token缓存过期时间
	 * @var integer
	 */
	public static $accessTokenExpire = 7200;

	private $accessToken;

	/**
	 * Wechat实例
	 */
	private static $instances;

	public function __construct(){
		$this->appid = self::$config["appid"];
		$this->secret = self::$config["secret"];
		$this->log = self::$config["log"]["enable"];
		$this->httpClient = self::$container->make("HttpClient");
		$this->redis = self::$container->make("Redis");
		$this->logger = self::$container->make("Log");
		$this->decrypt = new Decrypt(self::$config["token"],self::$config["encodingAesKey"],$this->appid);
	}

	/**
	 * 初始化，使用于改变微信公众号业务
	 *
	 * @param  string $appid  微信公众号appid
	 * @param  string $secret 微信公众号secret
	 * @return void
	 */
	public function init($appid,$secret){
		$this->appid = $appid;
		$this->secret = $secret;
	}

	/**
	 * 初始化加密类信息
	 *
	 * @param  string $token          
	 * @param  string $encodingAesKey 
	 * @param  string $appid          
	 * @return
	 */
	public function initDecrypt($token,$encodingAesKey,$appid){
		$this->decrypt->init($token,$encodingAesKey,$appid);
	}

	public static function getInstance($className = __CLASS__){
		if(!isset(self::$instances[$className])){
			self::$instances[$className] = new $className();
		}
		return self::$instances[$className];
	}

	/**
	 * 获取access_token
	 *
	 * @return [type] [description]
	 */
	public function getAccessToken(){
		if(!$this->accessToken){
			$accessTokenKey = self::ACCESSTOKEN . $this->appid;
			if(self::$cache){
				$res = $this->redis->getValues($accessTokenKey);
			}
			if(empty($res)){
				$url = sprintf(self::$config["wechaturl"]["accesstoken"],$this->appid,$this->secret);
	            $res = $this->request($url);
				self::$cache && $this->redis->setValues($accessTokenKey,$res,self::$accessTokenExpire);
				$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
			}
			$this->accessToken = $res["access_token"];
		}
		return $this->accessToken;
	}

	public function getAppid(){
		return $this->appid;
	}

	public function getSecret(){
		return $this->secret;
	}

	public function getPublishAccount(){
		return self::$config["publish"];
	}

	/**
	 * 发送http请求
	 *
	 * @param  string $url     http请求地址
	 * @param  string $method  http请求方法
	 * @param  array  $options http请求参数
	 * @return array
	 * @throws Waljqiang\Wechat\Exception,\Exception 
	 */
	public function request($url,$method = 'GET',$options = []){
		try{
			$result = $this->httpClient->request(
				$method,
				$url,
				$options
			);
			if($result->getStatusCode() == 200){
				$result = $result->getBody();
				if (!is_null($result = @json_decode($result, true))){
					if(isset($result["errcode"]) && $result["errcode"] != 0)
						throw new  WechatException($result["errmsg"],$result["errcode"]);			
	            } else {
	                throw new \Exception("result explain error",WechatException::RESULTERROR);
	            }
	            return $result;
	        }else{
	        	$this->log && $this->logger->log("Request " . $url . " error info:code[" . $e->getCode() . "]message[" . $e->getMessage() . "]",ERROR);
	        	throw new \Exception($e->getMessage(),$e->getCode());
	        }
		}catch(\Exception $e){
			$this->log && $this->logger->log("Request " . $url . " error info:code[" . $e->getCode() . "]message[" . $e->getMessage() . "]",ERROR);
			throw new \Exception($e->getMessage(),$e->getCode());
		}
	}

	/**
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 *
	 * @return
	 */
	public function encryptMsg($replyMsg, $timeStamp = NULL, $nonce = NULL){
		return $this->decrypt->encryptMsg($replyMsg,$timeStamp,$nonce);
	}

	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $signature string 签名串，对应URL参数的msg_signature
	 * @param $timeStamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $encryptMsg string 密文，对应POST请求的数据
	 *
	 * @return
	 */
	public function decryptMsg($signature,$timeStamp,$nonce,$encryptMsg){
		return $this->decrypt->decryptMsg($signature,$timeStamp,$nonce,$encryptMsg);
	}

	public function __call($method,$args){
		$className = "";
		foreach (Handle::$handleType as $key => $value) {
			if(in_array($method,$value)){
				$className = $key;
				break;
			}
		}
		if(empty($className)){
			throw new WechatException("Unsupport method",WechatException::UNSUPPORT);
		}
		$obj = Handle::create($className,$this);
		return call_user_func_array([$obj,$method],$args);
	}

}