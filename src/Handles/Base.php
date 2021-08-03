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
	const QRCODE = "wechat:qrcode:";

	/**
	 * 客服账号列表缓存key标识
	 */
	const KFACCOUNT = "wechat:cfaccount:list:";

	/**
	 * 所属行业信息缓存key标识
	 */
	const INDUSTRY = "wechat:industry:";

	/**
	 * 模板列表缓存key标识
	 */
	const TEMPLATELIST = "wechat:tpl:list:";

	/**
	 * 门店缓存key标识
	 */
	const SHOP = "wechat:shop:";

	/**
	 * 门店列表缓存key标识
	 */
	const SHOPLIST = "wechat:shop:list:";

	/**
	 * WiFi门店列表缓存key标识
	 */
	const WIFISHOPLIST = "wechat:wifi:shop:list:";

	/**
	 * WiFi门店信息缓存key标识
	 */
	const WIFISHOP = "wechat:wifi:shop:";

	/**
	 * WiFi设备列表缓存key标识
	 */
	const DEVICELIST = "wechat:wifi:device:list:";

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
			"tag_set" => "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=%s",//创建公众号标签
			"tag_get" => "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=%s",//获取公众号标签
			"tag_del" => "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=%s",//删除公众号标签
			"tag_fans_get" => "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=%s",//获取标签下的粉丝列表
			"tag_of_user_set" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=%s",//为用户打标签
			"tag_of_user_del" => "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=%s",//为用户取消标签
			"tag_of_user_get" => "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=%s",//获取用户身上的标签列表
			"remark_with_user_set" => "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=%s",//设置用户备注
			"info" => "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s",//获取用户基本信息
			"list" => "https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=%s",//获取用户列表
		],
		"account" => [
			"qrcode_ticket" => "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s",//获取二维码ticket
			"qrcode" => "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s",//获取二维码
		],
		"customer" => [
			"kf_add" => "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=%s",//添加客服账号
			"kf_set" => "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=%s",//修改客服账号
			"kf_del" => "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=%s",//删除客服账号
			"kf_avatar_up" => "http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=%s&kf_account=%s",//上传客服头像
			"kf_all_get" => "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=%s",//获取所有客服账号
			"kf_send_message" => "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s",//发送客服消息
		],
		"template" => [
			"industry_set" => "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=%s",//设置所属行业
			"industry_get" => "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=%s",//获取行业信息
			"tpl_id_get" => "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s",//获取模板id
			"tpl_list" => "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=%s",//获取模板列表
			"tpl_del" => "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=%s",//删除模板
			"send_tpl_msg" => "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s",//发送模板消息
		],
		"shop" => [
			"upimage" => "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=%s",//上传图片
			"add" => "http://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token=%s",//创建门店
			"get" => "http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token=%s",//查询门店信息
			"list" => "https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=%s",//查询门店列表
			"set" => "https://api.weixin.qq.com/cgi-bin/poi/updatepoi?access_token=%s",//修改门店信息
			"del" => "https://api.weixin.qq.com/cgi-bin/poi/delpoi?access_token=%s",//删除门店
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