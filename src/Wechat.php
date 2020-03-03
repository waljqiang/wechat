<?php
namespace Waljqiang\Wechat;

use Waljqiang\Wechat\Exceptions\WechatException;

class Wechat{
	/**
	 * [微信公众号appid]
	 */
	public $appid;
	/**
	 * 微信公众号secret
	 */
	public $secret;

	/**
	 * 是否启用缓存
	 * @var boolean
	 */
	public static $cache = true;

	/**
	 * 微信公众号业务请求地址
	 * @var array
	 */
	public static $wechatUrl;

	/**
	 * 类实例
	 * @var [type]
	 */
	public static $container;
	/**
	 * Waljqiang\Wechat\Redis
	 */
	private $redis;

	/**
	 * GuzzleHttp\Client
	 */
	private $httpClient;

	/**
	 * access_token缓存过期时间
	 * @var integer
	 */
	public static $accessTokenExpire = 7200;

	/**
	 * access_token缓存key标识
	 */
	const ACCESSTOKEN = "ACCESSTOKEN";

	public function __construct($appid,$secret){
		$this->appid = $appid;
		$this->secret = $secret;
		$this->httpClient = self::$container->make("HttpClient");
		$this->redis = self::$container->make("Redis");
	}

	/**
	 * 获取access_token
	 *
	 * @return [type] [description]
	 */
	public function getAccessToken(){
		$accessTokenKey = self::ACCESSTOKEN . ":" . $this->appid;
		if(self::$cache){
			$res = $this->redis->getValues($accessTokenKey);
		}
		if(empty($res)){
			$url = sprintf(self::$wechatUrl["getaccesstoken"],$this->appid,$this->secret);
            $res = $this->request($url);
			$this->redis->setValues($accessTokenKey,$res,self::$accessTokenExpire);
		}
		return $res;
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
	private function request($url,$method = 'GET',$options = []){
		try{
			$result = $this->httpClient->request(
				$method,
				$url,
				$options
			)->getBody();
			if (!is_null($result = @json_decode($result, true))){
				if(isset($result["errcode"]))
					throw new  WechatException($result["errmsg"],$result["errcode"]);			
            } else {
                throw new \Exception("result explain error",-1);
            }
            return $result;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage(),$e->getCode());
		}
	}

}