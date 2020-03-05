<?php
namespace Waljqiang\Wechat;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Handle;
use Waljqiang\Wechat\Handles\Reply;

class Wechat{
	/**
	 * access_token缓存key标识
	 */
	const ACCESSTOKEN = "WECHAT:ACCESSTOKEN";

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
		$this->httpClient = self::$container->make("HttpClient");
		$this->redis = self::$container->make("Redis");
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
			$accessTokenKey = self::ACCESSTOKEN . ":" . $this->appid;
			if(self::$cache){
				$res = $this->redis->getValues($accessTokenKey);
			}
			if(empty($res)){
				$url = sprintf(self::$config["wechaturl"]["accesstoken"],$this->appid,$this->secret);
	            $res = $this->request($url);
				self::$cache && $this->redis->setValues($accessTokenKey,$res,self::$accessTokenExpire);
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
	        	throw new \Exception($e->getMessage(),$e->getCode());
	        }
		}catch(\Exception $e){
			throw new \Exception($e->getMessage(),$e->getCode());
		}
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