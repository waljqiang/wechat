<?php
namespace Waljqiang\Wechat;

/**
 * 注册公众号请求类
 *
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 * @static $handleType 记录了各模块类中可使用Wechat调用的方法
 */
class Handle{
	public static $handleType = [
		"Menu" => [
			"setMenu",
			"getMenu",
			"deleteMenu"
		],
		"User" => [
			"setTag",
			"getTag",
			"deleteTag",
			"getTagFans",
			"tagToUsers",
			"tagDelUsers",
			"getUserTags",
			"setUserRemark",
			"getUserInfo",
			"getUserList"
		],
		"Account" => [
			"getQrcode"
		],
		"Message" => [
			"createKfAccount",
			"modifyKfAccount",
			"deleteKfAccount",
			"getKfAccount",
			"uploadAvatar",
			"kfSendMessage"
		],
		"Receive" => [
			"handleWechatMessage"
		],
		"Reply" => [
			"replyUser"
		],
		"Template" => [
			"setIndustry",
			"getIndustry",
			"getTemplateID",
			"getTemplateList",
			"deleteTemplate",
			"sendTemplate"
		],
		"Shop" => [
			"uploadImage",
			"createShop",
			"getShop",
			"getShopList",
			"modifyShop"
		],
		"Ocr" => [
			"identityCard",
			"bankCard",
			"drivingCard",
			"drivingLicense",
			"bizLicense",
			"orcComm"
		],
		"WIFI" => [
			"getWifiShopList",
			"getWifiShop",
			"modifyWifiShop",
			"clearWifiShop",
			"addPasswordDevice",
			"addPortalDevice",
			"getDeviceList",
			"deleteDevice",
			"wifiQrcode",
			"getWifiStatistics",
			"setWifiCoupon",
			"getWifiCoupon"
		],
		"Pay" => [
			"unifiedOrder",
			"orderQuery",
			"closeOrder",
			"refund",
			"refundQuery",
			"downloadBill"
		]
	];

	/**
	 * 注册公众号请求处理类
	 *
	 * @param  object $className
	 * @param  Waljqiang\Wechat\Wechat $wechat
	 * @return
	 */
	public static function create($className,$wechat){
		$class = __NAMESPACE__ . "\\Handles\\" . $className;
		return new $class($wechat);
	}
}