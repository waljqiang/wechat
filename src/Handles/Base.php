<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;
/**
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Base{
	/**
	 * 自定义菜单缓存key标识
	 */
	const MENU = "wechat:menu:";

	/**
	 * 公众号标签key标识
	 */
	const TAG = "wechat:tag:";

	/**
	 * 公众号标签下粉丝列表key标识
	 */
	const TAGFANS = "wechat:tag:fans:";

	/**
	 * 用户下标签列表缓存key标识
	 */
	const USERTAGS = "wechat:user:tags:";

	/**
	 * 用户基本信息缓存key标识
	 */
	const USERINFO = "wechat:user:";

	/**
	 * 用户列表缓存key标识
	 */
	const USERLIST = "wechat:user:list:";

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
	 * 门店缓存key标识
	 */
	const SHOP = "WECHAT:SHOP:";

	/**
	 * 门店列表缓存key标识
	 */
	const SHOPLIST = "WECHAT:SHOP:LIST:";

	/**
	 * WiFi门店列表缓存key标识
	 */
	const WIFISHOPLIST = "WECHAT:WIFI:SHOP:LIST:";

	/**
	 * WiFi门店信息缓存key标识
	 */
	const WIFISHOP = "WECHAT:WIFI:SHOP:";

	/**
	 * WiFi设备列表缓存key标识
	 */
	const DEVICELIST = "WECHAT:WIFI:DEVICE:LIST:";

	/**
	 * 客服头像文件类型
	 */
	const AVATARTYPE = ["jpg"];

	/**
	 * 门店图片支持的类型
	 */
	const SHOPIMAGE = ["jpg"];

	protected $api = [
		"menu" => [
			"set" => "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s",//创建自定义菜单
			"get" => "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=%s",//查询自定义菜单
			"del" => "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s",//删除自定义菜单
		],
		"user" => [
			"tag_set" => "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=%s",//微信创建公众号标签API地址
			"tag_get" => "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=%s",//微信获取公众号标签API地址
			"tag_del" => "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=%s",//微信删除公众号标签API地址
			"tag_fans_get" => "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=%s",//微信获取标签下的粉丝列表API地址
			"tag_with_user_set" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=%s",//微信为用户打标签API地址
			"tag_with_user_del" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=%s",//微信为用户取消标签API地址
			"tag_with_user_get" => "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=%s",//微信获取用户身上的标签列表API地址
			"remark_with_user_set" => "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=%s",//微信设置用户备注API地址
			"info" => "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s",//微信获取用户基本信息API地址
			"list" => "https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=%s",//微信获取用户列表API地址
		]
	];

	/**
	 * Waljqiang\Wechat\Wechat
	 */
	protected $wechat;

	public function __construct(Wechat $wechat){
		$this->wechat = $wechat;
	}

	public function setWechat(Wechat $wechat){
		$this->wechat = $wechat;
		return $this;
	}
}