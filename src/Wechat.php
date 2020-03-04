<?php
namespace Waljqiang\Wechat;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Handle;

class Wechat{
	/**
	 * access_token缓存key标识
	 */
	const ACCESSTOKEN = "WECHAT:ACCESSTOKEN";

	/**
	 * 自定义菜单缓存key标识
	 */
	const MENU = "WECHAT:MENU";

	/**
	 * 公众号标签key标识
	 */
	const TAG = "WECHAT:TAG";

	/**
	 * 公众号标签下粉丝列表key标识
	 */
	const TAGFANS = "WECHAT:TAG:FANS";

	/**
	 * 用户下标签列表缓存key标识
	 */
	const USERTAGS = "WECHAT:USER:TAGS";

	/**
	 * 用户基本信息缓存标识
	 */
	const USERINFO = "WECHAT:USER";

	const USERLIST = "WECHAT:USER:LIST";

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
	protected $redis;

	/**
	 * GuzzleHttp\Client
	 */
	protected $httpClient;

	/**
	 * access_token缓存过期时间
	 * @var integer
	 */
	public static $accessTokenExpire = 7200;

	public static $commonExpire = 2592000;

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

	/**
	 * 创建自定义菜单
	 *
	 * @param  array $options 自定义菜单设置项，具体查看微信公众号自定义菜单说明
	 * @return
	 */
	public function setMenu($options){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["menuset"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => $options
		]);
		self::$cache && $this->redis->del(self::MENU . ":" . $this->appid);
		return true;
	}

	/**
	 * 查询自定义菜单
	 *
	 * @return
	 */
	public function getMenu(){
		$menuKey = self::MENU . ":" . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($menuKey))){
			if(!$this->accessToken)
				$this->getAccessToken();
			$url = sprintf(self::$config["wechaturl"]["menuget"],$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($menuKey,$res,self::$commonExpire);
		}
		return $res;
	}

	/**
	 * 删除菜单
	 *
	 * @return
	 */
	public function deleteMenu(){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["menudel"],$this->accessToken);
		$res = $this->request($url);
		self::$cache && $this->redis->del(self::MENU . ":" . $this->appid);
		return true;
	}

	/**
	 * 创建公众号标签
	 *
	 * @param  string $tagName 标签名称
	 * @return
	 */
	public function setTag($tagName){
		if(strlen($tagName) >= 30){
			throw new WechatException("The name of tag must less 30",-2);
		}
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["tagset"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json",
			],
			"json" => [
				"tag" => [
					"name" => $tagName
				]
			]
		]);
		self::$cache && $this->redis->del(self::TAG . ":" . $this->appid);
		return $res;
	}

	/**
	 * 获取公众号标签
	 *
	 * @return
	 */
	public function getTag(){
		$tagKey = self::TAG . ":" . $this->appid;
		if(!self::$cache || ($res = $this->redis->getValues($tagKey))){
			if(!$this->accessToken)
				$this->getAccessToken();
			$url = sprintf(self::$config["wechaturl"]["tagget"],$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($tagKey,$res,self::$commonExpire);
		}
		return $res;
	}

	/**
	 * 删除公众号标签
	 *
	 * 当某个标签下的粉丝超过10w时，后台不可直接删除标签。此时，开发者可以对该标签下的openid列表，先进行取消标签的操作，直到粉丝数不超过10w后，才可直接删除该标签。
	 * @param  int $tagID 标签ID
	 * @return
	 */
	public function deleteTag($tagID){
		if(!$this->accessToken)
			$this->getAccessToken();
		//粉丝超过10w特殊处理
		$fans = $this->getTagFans($tagID,1,1000000000);
		if($fans['total'] > 1000000){
			$fansDel = array_chunk(array_slice($fans["list"],99999),50);
			foreach ($fansDel as $del) {
				$this->tagDelUsers($tagID,$del);
			}
		}
		$url = sprintf(self::$config["wechaturl"]["tagdel"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => [
				"tag" => [
					"id" => $tagID
				]
			]
		]);
		if(self::$cache){
			$keys = [
				self::TAG . ":" . $this->appid,
				self::TAGFANS . ":" . $this->appid . ":" . $tagID
			];
			//删除公众号下标签缓存
			//删除标签下粉丝列表缓存
			$this->redis->del($keys);
			$keyword = self::USERTAGS . ":" . $this->appid . ":*";
			//删除粉丝下标签缓存
			$this->redis->matchDel($keyword);
		}
		return true;
	}

	/**
	 * 获取公众号标签下的粉丝列表
	 *
	 * @param  integer  $tagID      标签ID
	 * @param  integer $pageIndex  页码
	 * @param  integer $pageOffset 每页记录数
	 * @return
	 */
	public function getTagFans($tagID,$pageIndex = 1,$pageOffset = 10){
		$tagFansKey = self::TAGFANS . ":" . $this->appid . ":" . $tagID;
		if(!self::$cache || !($this->redis->getValues($tagFansKey))){
			$data = $this->_getTagFans($tagID);
			$res["list"] = $data["data"]["openid"];
			while($data["count"] > 0 ){
				$data = $this->_getTagFans($tagID,$data["next_openid"]);
				$res["list"] = array_merge($res["list"],$data["data"]["openid"]);
			}
			$res["total"] = count($res["list"]);
			self::$cache && $this->redis->setValues($tagFansKey,$res,self::$commonExpire);
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	/**
	 * 批量为用户打标签，一次最多支持20个
	 *
	 * @param  integer $tagID   标签ID
	 * @param  array $openIDs 多个用户openID
	 * @return
	 */
	public function tagToUsers($tagID,$openIDs){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["tagtousers"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => [
				"openid_list" => $openIDs,
				"tagid" => $tagID
			]
		]);
		if(self::$cache){
			//删除该标签下粉丝列表
			$keys[] = self::TAGFANS . ":" . $this->appid . ":" . $tagID;
			//删除粉丝下标签列表
			foreach ($openIDs as $openID) {
				$keys[] = self::USERTAGS . ":" . $this->appid . ":" . $openID;
			}
			$this->redis->del($keys);
		}
		return true;
	}

	/**
	 * 批量为用户取消标签，一次最多支持50个
	 *
	 * @param  integer $tagID   标签ID
	 * @param  array $openIDs 多个用户openID
	 * @return
	 */
	public function tagDelUsers($tagID,$openIDs){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["tagdelusers"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => [
				"openid_list" => $openIDs,
				"tagid" => $tagID
			]
		]);
		if(self::$cache){
			//删除该标签下粉丝列表
			$keys[] = self::TAGFANS . ":" . $this->appid . ":" . $tagID;
			//删除粉丝下标签列表
			foreach ($openIDs as $openID) {
				$keys[] = self::USERTAGS . ":" . $this->appid . ":" . $openID;
			}
			$this->redis->del($keys);
		}
		return true;
	}

	/**
	 * 获取用户标签列表
	 *
	 * @param  string $openID 用户openID
	 * @return
	 */
	public function getUserTags($openID){
		$userTagsKey = self::USERTAGS . ":" . $this->appid . ":" . $openID;
		if(!self::$cache || !($res = $this->redis->getValues($userTagsKey))){
			if(!$this->accessToken)
				$this->getAccessToken();
			$url = sprintf(self::$config["wechaturl"]["usertags"],$this->accessToken);
			$res = $this->request($url,"POST",[
				"headers" => [
					"Accept" => "application/json"
				],
				"json" => [
					"openid" => $openID
				]
			]);
			$res = $res["tag_list"];
			self::$cache && $this->redis->setValues($userTagsKey,$res,self::$commonExpire);
		}
		return $res;
	}

	/**
	 * 为用户打备注
	 *
	 * @param string $openid 用户openid
	 * @param string $remark 备注名
	 */
	public function setUserRemark($openid,$remark){
		if(strlen($remark) >= 30){
			throw new WechatException("The name of remark must less 30",-2);
		}
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["userremarkset"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => [
				"openid" => $openid,
				"remark" => $remark
			]
		]);
		self::$cache && $this->redis->del(self::USERINFO . ":" . $this->appid . ":" . $openid);
		return true;
	}

	/**
	 * 获取用户基本信息
	 *
	 * @param  string $openid 用户openid
	 * @param  string $lang   语言，支持参数请查看微信获取用户基本信息接口说明
	 * @return
	 */
	public function getUserInfo($openid,$lang="zh_CN"){
		if(!$this->accessToken)
			$this->getAccessToken();
		$userInfoKey = self::USERINFO . ":" . $this->appid . ":" . $openid;
		if(!self::$cache || !($res = $this->redis->getValues($userInfoKey))){
			$url = sprintf(self::$config["wechaturl"]["userinfo"],$this->accessToken,$openid,$lang);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($userInfoKey,$res,self::$commonExpire);
		}
		return $res;
	}

	/**
	 * 获取关注用户列表
	 *
	 * @param  integer $pageIndex  页码
	 * @param  integer $pageOffset 每页记录数
	 * @return
	 */
	public function getUserList($pageIndex = 0,$pageOffset = 10){
		$userListKey = self::USERLIST . ":" . $this->appid;
		if(!self::$cache || !($this->redis->getValues($userListKey))){
			$data = $this->_getUserList();
			$res["total"] = $data["total"];
			$res["list"] = $data["data"]["openid"];
			while($data["count"] > 0){
				$data = $this->_getUserList($data["next_openid"]);
				$res["list"] = array_merge($res["list"],$data["data"]["openid"]);
			}
			self::$cache && $this->redis->setValues($userListKey,$res,self::$commonExpire); 
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	public function getAppid(){
		return $this->appid;
	}

	public function getSecret(){
		return $this->secret;
	}

	public function getWechatUrl($key){
		return self::$config["wechaturl"][$key];
	}

	private function _getTagFans($tagID,$openID = ""){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["tagfans"],$this->accessToken);
		$data = empty($openID) ? [ "tagid" => $tagID ] : [ "tagid" => $tagID,"next_openid" => $openID];
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => $data
		]);
		return $res;
	}

	private function _getUserList($openID = ""){
		if(!$this->accessToken)
			$this->getAccessToken();
		$url = sprintf(self::$config["wechaturl"]["userlist"],$this->accessToken,$openID);
		$res = $this->request($url);
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
	public function request($url,$method = 'GET',$options = []){
		try{
			$result = $this->httpClient->request(
				$method,
				$url,
				$options
			)->getBody();
			if (!is_null($result = @json_decode($result, true))){
				if(isset($result["errcode"]) && $result["errcode"] != 0)
					throw new  WechatException($result["errmsg"],$result["errcode"]);			
            } else {
                throw new \Exception("result explain error",-2);
            }
            return $result;
		}catch(\Exception $e){
			throw new \Exception($e->getMessage(),$e->getCode());
		}
	}

}