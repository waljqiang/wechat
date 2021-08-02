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
class Message{
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
}