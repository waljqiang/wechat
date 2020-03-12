<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

/**
 * 消息管理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Message extends Base{
	const TEXT = "text";
	const IMAGE = "image";
	const VOICE = "voice";
	const VIDEO = "video";
	const SHORTVIDEO = "shortvideo";
	const MUSIC = "music";
	const NEWS = "news";
	const MPNEWS = "mpnews";
	const LOCATION = "location";
	const LINK = "location";
	const EVENT = "event";
	const MSGMENU = "msgmenu";
	const WXCARD = "wxcard";
	const MINIPROGRAMPAGE = "miniprogrampage";

	const EVENTTYPE = [
		"SUBSCRIBE" => "subscribe",
		"UNSUBSCRIBE" => "unsubscribe",
		"SCAN" => "SCAN",
		"LOCATION" => "LOCATION",
		"CLICK" => "CLICK",
		"VIEW" => "VIEW",
		"TEMPLATESENDJOBFINISH" => "TEMPLATESENDJOBFINISH",
		"POICHECKNOTIFY" => "poi_check_notify",
		"WIFICONNECTED" => "WifiConnected"
	];

	/**
	 * 添加客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function createKfAccount($data){
		$url = sprintf(self::$wechatUrl["kfcreate"],$this->accessToken);
		$res = $this->request($url,"POST",["body" => json_encode($data, JSON_UNESCAPED_UNICODE)]);
		self::$cache && $this->redis->del(self::KFACCOUNT . $this->appid);
		return true;
	}

	/**
	 * 修改客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function modifyKfAccount($data){
		$url = sprintf(self::$wechatUrl["kfmodify"],$this->accessToken);
		$res = $this->request($url,"POST",["body" => json_encode($data, JSON_UNESCAPED_UNICODE)]);
		self::$cache && $this->redis->del(self::KFACCOUNT . $this->appid);
		return true;
	}

	/**
	 * 删除客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function deleteKfAccount($data){
		$url = sprintf(self::$wechatUrl["kfmodify"],$this->accessToken);
		$res = $this->request($url,"POST",["body" => json_encode($data, JSON_UNESCAPED_UNICODE)]);
		self::$cache && $this->redis->del(self::KFACCOUNT . $this->appid);
		return true;
	}

	/**
	 * 获取所有客服账号
	 *
	 * @return
	 */
	public function getKfAccount(){
		$kfAccountKey = self::KFACCOUNT . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($kfAccountKey))){
			$url = sprintf(self::$wechatUrl["kfget"],$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($kfAccountKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		}
		return $res;
	}

	/**
	 * 上传客服账号头像
	 *
	 *头像图片文件必须是jpg格式，推荐使用640*640大小的图片以达到最佳效果
	 * 
	 * @param  string $kfAccount 客服账号
	 * @param  string $imageUrl  头像地址
	 * @param  string $fileName  上传的图片名称,为空时使用图片默认名称
	 * @return
	 */
	public function uploadAvatar($kfAccount,$imageUrl,$fileName = ""){
		$pathinfo = pathinfo($imageUrl);
		if(!in_array($pathinfo["extension"],self::AVATARTYPE)){
			throw new WechatException("Avatar is allowed by " . implode(",",self::AVATARTYPE),WechatException::UNSUPPORTFILETYPE);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		$data = !empty($fileName) ? [
			[
				"name" => "file",
	            "contents" => @fopen($imageUrl,"r"),
	        ]
		] : [
			[
				"name" => "file",
	            "contents" => @fopen($imageUrl,"r"),
	            "filename" => $fileName,
	        ]
		];
		$url = sprintf(self::$wechatUrl["kfavatar"],$this->accessToken,$kfAccount);
		$res = $this->request($url,"POST",[
			"multipart" => $data
		]);
		return true;
	}

	/**
	 * 客服发消息
	 *
	 * @param  string $openID      用户openID
	 * @param  string $messageType 消息类型
	 * @param  array $message        消息数据
	 *
	 * 文本消息
	 * [
	 * 		"content" => "你好"
	 * ]
	 * 图片消息
	 * [
	 * 		"media_id" => "123"
	 * ]
	 * 语音消息
	 * [
	 * 		"media_id" => "123"
	 * ]
	 * 视频消息
	 * [
	 * 		"media_id" => "123",
	 * 		"thumb_media_id" => "124",
	 * 		"title" => "视频标题",
	 * 		"description" => "视频描述"
	 * ]
	 * 音乐消息
	 * [
	 * 		"title" => "音乐标题",
	 * 		"description" => "音乐描述",
	 * 		"musicurl" => "音乐链接",
	 * 		"hqmusicurl" => "高音质音乐链接",
	 * 		"thumb_media_id" => "123"
	 * ]
	 * 图文消息
	 * [
	 * 		"articles" => [
	 * 			[
	 * 		 		"title" => "标题",
	 * 		   		"description" => "描述",
	 * 		     	"url" => "图片跳转链接",
	 * 		      	"picurl" => "图片链接"
	 *          ]
	 * 		]
	 * ]
	 * 
	 * [
	 * 		"media_id" => "123"
	 * ]
	 * 菜单消息
	 * [
	 * 		"head_content" => "您对本次服务是否满意",
	 * 		"list" => [
	 * 			[
		 * 			"id" => "101",
		 * 			"content" => "满意"
		 * 		],
		 * 		[
		 * 			"id" => "102",
		 * 			"content" => "不满意"
		 * 		]
	 * 		],
	 * 		"tail_content" => "欢迎再次光临"
	 * ]
	 * 卡券消息
	 * [
	 * 		"card_id" => "123"
	 * ]
	 * 发送小程序卡片
	 * [
	 * 		"title" => "title",
	 * 		"appid" => "appid",
	 * 		"pagepath" => "pagepath",
	 * 		"thumb_media_id" => "thumb_media_id"
	 * ]
	 * 
	 * @return
	 */
	public function kfSendMessage($openID,$messageType,$message,$kfAccount = ""){
		if(!$this->checkMsgType($messageType)){
			throw new WechatException("Unsupport message type",WechatException::UNSUPPORTMESSAGETYPE);	
		}
		$buffer["touser"] = $openID;
		$buffer["msgtype"] = $messageType;
		$buffer[$messageType] = $message;
		if(!empty($kfAccount)){
			$buffer["customservice"]["kf_account"] = $kfAccount;
		}
		$url = sprintf(self::$wechatUrl["kfsendmsg"],$this->accessToken,$openID);
		$data = json_encode($buffer,JSON_UNESCAPED_UNICODE);
		$this->request($url,"POST",[
			"body" => $data
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]send data[" . json_encode($data) . "]",DEBUG);
		return true;
	}

	protected function checkMsgType($messageType){
		return in_array($messageType,[self::TEXT,self::IMAGE,self::VOICE,self::VIDEO,self::SHORTVIDEO,self::MUSIC,self::NEWS,self::LOCATION,self::LINK,self::EVENT]);
	}
}