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
		"menu_set" => "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s",//创建自定义菜单
		"menu_get" => "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=%s",//查询自定义菜单
		"menu_del" => "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s",//删除自定义菜单
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