<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Wechat;

/**
 * 菜单管理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Menu extends Base{
	/**
	 * 创建自定义菜单
	 *
	 * @param  array $options 自定义菜单设置项，具体查看微信公众号自定义菜单说明
	 * @return
	 */
	public function setMenu($options){
		$url = sprintf($this->api["menu"]["set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $options]);
		$this->wechat->getRedis()->del(self::MENU . $this->wechat->getAppid());
		return true;
	}

	/**
	 * 查询自定义菜单
	 *
	 * @return
	 */
	public function getMenu(){
		$menuKey = self::MENU . $this->wechat->getAppid();
		if(!$res = $this->wechat->getRedis()->getValues($menuKey)){
			$url = sprintf($this->api["menu"]["get"],$this->wechat->getAccessToken());
			$res = $this->wechat->request($url);
			$this->wechat->getRedis()->setValues($menuKey,$res,Wechat::$common_expire_in - Wechat::$pre_expire_in);
		}
		return $res;
	}

	/**
	 * 删除菜单
	 *
	 * @return
	 */
	public function deleteMenu(){
		$url = sprintf($this->api["menu"]["del"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url);
		$this->wechat->getRedis()->del(self::MENU . $this->wechat->getAppid());
		return true;
	}
}