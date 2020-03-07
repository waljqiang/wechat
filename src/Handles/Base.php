<?php
namespace Waljqiang\Wechat\Handles;
/**
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Base{
	/**
	 * 自定义菜单缓存key标识
	 */
	const MENU = "WECHAT:MENU:";

	/**
	 * 公众号标签key标识
	 */
	const TAG = "WECHAT:TAG:";

	/**
	 * 公众号标签下粉丝列表key标识
	 */
	const TAGFANS = "WECHAT:TAG:FANS:";

	/**
	 * 用户下标签列表缓存key标识
	 */
	const USERTAGS = "WECHAT:USER:TAGS:";

	/**
	 * 用户基本信息缓存key标识
	 */
	const USERINFO = "WECHAT:USER:";

	/**
	 * 用户列表缓存key标识
	 */
	const USERLIST = "WECHAT:USER:LIST:";

	/**
	 * 二维码缓存key标识
	 */
	const QRCODE = "WECHAT:QRCODE:";

	/**
	 * 客服账号列表缓存key标识
	 */
	const KFACCOUNT = "WECHAT:KFACCOUNT:LIST:";

	/**
	 * 所属行业信息缓存key标识
	 */
	const INDUSTRY = "WECHAT:INDUSTRY:";

	/**
	 * 模板列表缓存key标识
	 */
	const TEMPLATELIST = "WECHAT:TPL:LIST:";

	/**
	 * 客服头像文件类型
	 */
	const AVATARTYPE = ["jpg"];

	protected $accessToken;
	protected $appid;
	protected $secret;

	/**
	 * Waljqiang\Wechat\Wechat
	 */
	protected $wechat;
	/**
	 * Waljqiang\Wechat\Redis\Redis
	 */
	protected $redis;

	/**
	 * Monolog\Logger
	 */
	protected $logger;
	protected $log;

	public static $cache = true;
	public static $wechatUrl = [];

	/**
	 * 公共缓存时间
	 * @var integer
	 */
	public static $commonExpire = 2592000;

	public function __construct($wechat){
		$this->appid = $wechat->getAppid();
		$this->secret = $wechat->getSecret();
		$this->log = $wechat->log;
		$this->accessToken = $wechat->getAccessToken();
		$this->redis = $wechat->redis;
		$this->logger = $wechat->logger;
		$this->wechat = $wechat;
	}

	public function request($url,$method = 'GET',$options = []){
		return $this->wechat->request($url,$method,$options);
	}
}