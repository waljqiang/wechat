<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

class Message extends Base{
	const TEXT = "text";
	const IMAGE = "image";
	const VOICE = "voice";
	const VIDEO = "video";
	const SHORTVIDEO = "shortvideo";
	const MUSIC = "music";
	const NEWS = "news";
	const LOCATION = "location";
	const LINK = "location";
	const EVENT = "event";

	const EVENTTYPE = [
		"SUBSCRIBE" => "subscribe",
		"UNSUBSCRIBE" => "unsubscribe",
		"SCAN" => "SCAN",
		"LOCATION" => "LOCATION",
		"CLICK" => "CLICK",
		"VIEW" => "VIEW"
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
		self::$cache && $this->redis->del(self::KFACCOUNT . ":" . $this->appid);
		return $res;
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
		self::$cache && $this->redis->del(self::KFACCOUNT . ":" . $this->appid);
		return $res;
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
		self::$cache && $this->redis->del(self::KFACCOUNT . ":" . $this->appid);
		return $res;
	}

	/**
	 * 获取所有客服账号
	 *
	 * @return
	 */
	public function getKfAccount(){
		$kfAccountKey = self::KFACCOUNT . ":" . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($kfAccountKey))){
			$url = sprintf(self::$wechatUrl["kfget"],$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($kfAccountKey,$res,self::$commonExpire);
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

	protected function checkMsgType($messageType){
		return in_array($messageType,[self::TEXT,self::IMAGE,self::VOICE,self::VIDEO,self::SHORTVIDEO,self::MUSIC,self::NEWS,self::LOCATION,self::LINK,self::EVENT]);
	}
}