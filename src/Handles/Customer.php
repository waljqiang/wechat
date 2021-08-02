<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Handles\Message;

/**
 * 消息管理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */

class Customer extends Message{
	/**
	 * 添加客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function createKfAccount($data){
		$url = sprintf($this->api["customer"]["kf_add"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		$this->wechat->getRedis()->del(self::KFACCOUNT . $this->wechat->getAppid());
		return true;
	}

	/**
	 * 修改客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function modifyKfAccount($data){
		$url = sprintf($this->api["customer"]["kf_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		$this->wechat->getRedis()->del(self::KFACCOUNT . $this->wechat->getAppid());
		return true;
	}
	/**
	 * 删除客服账号
	 *
	 * @param  array $data 请参阅微信公众平台文档
	 * @return
	 */
	public function deleteKfAccount($data){
		$url = sprintf($this->api["customer"]["kf_del"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		$this->wechat->getRedis()->del(self::KFACCOUNT . $this->wechat->getAppid());
		return true;
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
		$url = sprintf($this->api["customer"]["kf_avatar_up"],$this->wechat->getAccessToken(),$kfAccount);
		$res = $this->wechat->request($url,"POST",["multipart" => $data]);
		return true;
	}

	/**
	 * 获取所有客服账号
	 *
	 * @return
	 */
	public function getAllKfAccount(){
		$kfAccountKey = self::KFACCOUNT . $this->wechat->getAccessToken();
		if(!($res = $this->wechat->getRedis()->getValues($kfAccountKey))){
			$url = sprintf($this->api["customer"]["kf_all_get"],$this->wechat->getAccessToken());
			$res = $this->wechat->request($url);
			$this->wechat->getRedis()->setValues($kfAccountKey,$res,Wechat::$common_expire_in);
		}
		return $res;
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
		$url = sprintf($this->api["customer"]["kf_send_message"],$this->wechat->getAccessToken(),$openID);
		$this->wechat->request($url,"POST",["json" => $buffer]);
		return true;
	}

}