<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;
use Carbon\Carbon;

use Waljqiang\Wechat\Wechat;

/**
 * 回复用户消息处理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Reply extends Message{

	/**
	 * 回复消息给用户
	 *
	 * @param  string $messageType 消息类型
	 * @param  array $message     消息体,请参照微信回复消息数字中key与微信中xml保持一致，不需要消息类型及创建时间字段
	 * @return [type]              [description]
	 */
	public function replyUser($messageType,$message){
		if(!$this->checkMsgType($messageType)){
			throw new WechatException("Unsupport message type",WechatException::UNSUPPORTMESSAGETYPE);	
		}
		$message["CreateTime"] = Carbon::now()->timestamp;
		$res = NULL;
		switch ($messageType) {
			case self::TEXT:
				$res = $this->pareseText($message);
				break;
			case self::IMAGE:
				$res = $this->parseImage($message);
				break;
			case self::VOICE:
				$res = $this->parseVoice($message);
				break;
			case self::VIDEO:
				$res = $this->parseVideo($message);
				break;
			case self::MUSIC:
				$res = $this->parseMusic($message);
				break;
			case self::NEWS:
				$res = $this->parseNews($message);
				break;
			default:
				break;
		}
		if(!is_null($res)){
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Reply Message[" . $res . "]",DEBUG);
			echo Wechat::$encode ? $this->wechat->encryptMsg($res) : $res;
		}
		exit(-1);
	}

	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接收方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"Content" => "欢迎您!"
	 * ]
	 */
	private function pareseText($message){
		$tpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<Content><![CDATA[%s]]></Content>
				</xml>";
        return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::TEXT,$message["Content"]);
	}

	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接收方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"MediaId" => "通过素材管理中的接口上传多媒体文件得到的id"
	 * ]
	 */

	private function parseImage($message){
		$tpl = "<xml>
			  	<ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<Image>
			    <MediaId><![CDATA[%s]]></MediaId>
			  	</Image>
				</xml>";
		return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::IMAGE,$message["MediaId"]);
	}

	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接收方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"MediaId" => "通过素材管理中的接口上传多媒体文件得到的id"
	 * ]
	 */
	private function parseVoice($message){
		$tpl = "<xml>
			  	<ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<Voice>
			    	<MediaId><![CDATA[%s]]></MediaId>
			  	</Voice>
				</xml>";
		return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::VOICE,$message["MediaId"]);
	}

	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接收方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"MediaId" => "通过素材管理中的接口上传多媒体文件得到的id",
	 * 		"Title" => "视频消息的标题",
	 * 		"Description" => "视频消息的描述"
	 * ]
	 */
	private function parseVideo($message){
		$tpl = "<xml>
			  	<ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<Video>
			    	<MediaId><![CDATA[%s]]></MediaId>
			    	<Title><![CDATA[%s]]></Title>
			    	<Description><![CDATA[%s]]></Description>
			  	</Video>
				</xml>";
				return $tpl;
		return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::VIDEO,$message["MediaId"],$message["Title"],$message["Description"]);
	}

	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接受方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"Title" => "音乐标题",
	 * 		"Description" => "音乐描述",
	 * 		"MusicUrl" => "音乐链接",
	 * 		"HQMusicUrl" => "高质量音乐链接,WIFI环境优先使用该链接播放音乐",
	 * 		"ThumbMediaId" => "缩略图的媒体id,通过素材管理中的接口上传多媒体文件得到的id"
	 * ]
	 */
	private function parseMusic($message){
		$tpl = "<xml>
			  	<ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<Music>
			    	<Title><![CDATA[%s]]></Title>
			    	<Description><![CDATA[%s]]></Description>
			    	<MusicUrl><![CDATA[%s]]></MusicUrl>
			    	<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
			    	<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
			  	</Music>
				</xml>";
		return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::MUSIC,$message["Title"],$message["Description"],$message["MusicUrl"],$message["HQMusicUrl"],$message["ThumbMediaId"]);
	}


	/**
	 * 参数示例
	 *
	 * [
	 * 		"ToUserName" => "接收方openID",
	 * 		"FromUserName" => "开发者微信号",
	 * 		"Articles" => [
	 * 			[
	 * 				"Title" => "图文标题1",
	 * 				"Description" => "图文描述1",
	 * 				"PicUrl" => "图片链接1",
	 * 				"Url" => "图片跳转链接1"
	 * 			],
	 * 			[
	 * 				"Title" => "图文标题2",
	 * 				"Description" => "图文描述2",
	 * 				"PicUrl" => "图片链接2",
	 * 				"Url" => "图片跳转链接2"
	 * 			]
	 * 		]
	 * ]
	 */
	private function parseNews($message){
		$tpl = "<xml>
			  	<ToUserName><![CDATA[%s]]></ToUserName>
			  	<FromUserName><![CDATA[%s]]></FromUserName>
			  	<CreateTime>%s</CreateTime>
			  	<MsgType><![CDATA[%s]]></MsgType>
			  	<ArticleCount>%s</ArticleCount>
			  	<Articles>%s</Articles>
				</xml>";
		$item = "<item>
		      		<Title><![CDATA[%s]]></Title>
		      		<Description><![CDATA[%s]]></Description>
		      		<PicUrl><![CDATA[%s]]></PicUrl>
		      		<Url><![CDATA[%s]]></Url>
	    		</item>";
	    $articles = "";
	    foreach ($message["Articles"] as $article) {
	    	$articles .= sprintf($item,$article["Title"],$article["Description"],$article["PicUrl"],$article["Url"]);
	    }
		return sprintf($tpl, $message["ToUserName"], $message["FromUserName"],$message["CreateTime"],self::MUSIC,count($message["Articles"]),$articles);
	}

}