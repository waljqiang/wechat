<?php
namespace Waljqiang\Wechat;

use Waljqiang\Wechat\Redis;
use Waljqiang\Wechat\Logger;
use GuzzleHttp\Client;
use Waljqiang\Wechat\Decryption\Decrypt;
use Waljqiang\Wechat\Exceptions\WechatException;

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
 *
 * 模板消息
 * boolean setIndustry($primaryNumber,$secondaryNumber);设置模板消息
 * array getIndustry();获取所属行业
 * string getTemplateID($templateIdShort);获取模板id
 * array function getTemplateList();获取模板列表
 * boolean deleteTemplate($templateID);删除模板
 * string sendTemplate($openID,$templateID,$data,$url = "",$appid = "",$pagePath = "");发送模板消息
 */
class Wechat{
	/**
	 * access_token缓存key标识
	 */
	const ACCESSTOKEN = "wechat:accesstoken:";

	private $api = [
		"access_token" => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",//获取access_token
	];
	/**
	 * 缓存提前过期时间
	 */
	public static $pre_expire_in = 600;
	/**
	 * 公共缓存时间
	 */
	public static $common_expire_in = 2592000;

	/**
	 * [微信公众号appid]
	 */
	private $appid;
	/**
	 * 微信公众号appSecret
	 */
	private $appSecret;
	/**
	 * 微信公众号encodingAesKey
	 */
	private $encodingAesKey;
	/**
	 * 微信公众号token
	 */
	private $token;
	/**
	 * 消息是否加密
	 */
	private $encoded = true;

	/**
	 * Predis\Client
	 */
	private $redis;
	/**
	 * Monolog\Logger
	 */
	private $logger;
	/**
	 * GuzzleHttp\Client
	 */
	private $httpClient;
	/**
	 * Waljqiang\Wechat\Decryption\Decrypt
	 */
	private $decrypt;

	/**
	 * 微信公众号接口调用凭证
	 */
	private $accessToken;
	/**
	 * 微信接口处理类
	 */
	private $handles;

	public function __construct(Redis $redis,Logger $logger,$config){
		if(!empty(array_diff(["appid","appSecret","encodingAesKey","token"],array_keys($config)))){
			throw new WechatException("Missing required attribute",WechatException::ATTRIBUTEMISS);
		}
		foreach ($config as $key => $value) {
			if(property_exists($this,$key)){
				$this->{$key} = $value;
			}
		}
		$this->redis = $redis;
		$this->logger = $logger;
		$this->httpClient = new Client;
		$this->decrypt = new Decrypt($this->token,$this->encodingAesKey,$this->appid);
		//加载处理类
		$this->loaderHandles();
	}

	/**
	 * 初始化，使用于改变微信公众号业务
	 *
	 * @param  string $appid  微信公众号appid
	 * @param  string $appSecret 微信公众号appSecret
	 * @return void
	 */
	public function init($appid,$appSecret){
		$this->appid = $appid;
		$this->appSecret = $appSecret;
		$this->decrypt->init($this->token,$this->encodingAesKey,$this->appid);
		foreach ($this->handles as $key => $handle) {
			$this->handles[$key] = $handle->setWechat($this);
		}
		return $this;
	}

	/**
	 * 初始化加密类信息
	 *
	 * @param  string $token          
	 * @param  string $encodingAesKey 
	 * @param  string $appid          
	 * @return
	 */
	/*public function initDecrypt($token,$encodingAesKey,$appid){
		$this->decrypt->init($token,$encodingAesKey,$appid);
	}*/

	/**
	 * 获取access_token
	 *
	 * @return [type] [description]
	 */
	public function getAccessToken(){
		if(!$this->accessToken){
			$res = $this->redis->getValues(self::ACCESSTOKEN . $this->appid);
			if(empty($res)){
				$url = sprintf($this->api["access_token"],$this->appid,$this->appSecret);
				$res = $this->request($url);
				$this->redis->setValues(self::ACCESSTOKEN . $this->appid,$res,$res["expires_in"] - self::$pre_expire_in);
			}
			$this->accessToken = $res["access_token"];
		}
		return $this->accessToken;
	}

	public function getAppid(){
		return $this->appid;
	}

	public function getAppSecret(){
		return $this->appSecret;
	}

	public function getRedis(){
		return $this->redis;
	}

	/*public function getPublishAccount(){
		return self::$config["publish"];
	}*/

	/**
	 * 发送http请求
	 *
	 * @param  string $url     http请求地址
	 * @param  string $method  http请求方法
	 * @param  array  $options http请求参数
	 * @return array
	 * @throws Waljqiang\Wechat\Exception,\Exception 
	 */
	public function request($url,$method = "GET",$data = [],$header = []){
		try{
			$body = [];
			if(!empty($header)){
				array_push($body,["headers" => $header]);
			}
			if(!empty($data)){
				array_merge($body,$data);
			}
			$response = $this->httpClient->request($method,$url,$body);
			if($response->getStatusCode() == 200){
				$result = $response->getBody();
				if(!is_null($result = @json_decode($result,true))){
					$this->logger->log("Request " . $url . " response[" . json_encode($result) . "]",\Monolog\Logger::DEBUG);
					if(isset($result["errcode"]) && $result["errcode"] != 0){
						throw new WechatException($result["errmsg"],$result["errcode"]);
					}
					return $result;
				}else{
					$this->logger->log("Explain response failure",\Monolog\Logger::ERROR);
					throw new WechatException("Explain response failure",WechatException::HTTPRESPONSEEXPLAINFAILURE);
				}
			}else{
				$this->logger->log("Request " . $url . " Failure, caused:" . $e->getMessage(),\Monolog\Logger::ERROR);
				throw new WechatException($e->getMessage(),WechatException::HTTPREQUESTERROR);
			}
		}catch(\Exception $e){
			$this->logger->log("Request " . $url . " Failure, caused:" . $e->getMessage(),\Monolog\Logger::ERROR);
			throw new WechatException($e->getMessage(),WechatException::HTTPREQUESTERROR);
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
	/*public function encryptMsg($replyMsg, $timeStamp = NULL, $nonce = NULL){
		return $this->decrypt->encryptMsg($replyMsg,$timeStamp,$nonce);
	}*/

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
	/*public function decryptMsg($signature,$timeStamp,$nonce,$encryptMsg){
		return $this->decrypt->decryptMsg($signature,$timeStamp,$nonce,$encryptMsg);
	}*/

	public function __call($method,$args){
		if(!$this->accessToken){
			$this->getAccessToken();
		}
		foreach ($this->handles as $key => $handle){
			if(method_exists($handle,$method)){
				return call_user_func_array([$handle,$method],$args);
			}
		}
	}

	private function loaderHandles(){
		foreach (scandir(__DIR__ . "/Handles") as $fileName) {
			if($fileName != "." && $fileName != ".."){
				$className = str_replace(strrchr($fileName, "."),"",$fileName);
				$className = __NAMESPACE__ . "\\Handles\\" . $className;
				$this->handles[$className] = new $className($this);
			}
		}
	}

}