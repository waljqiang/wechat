<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

class Menu extends Base{
	/**
	 * 创建自定义菜单
	 *
	 * @param  array $options 自定义菜单设置项，具体查看微信公众号自定义菜单说明
	 * @return
	 */
	public function setMenu($options){
		$url = sprintf(self::$wechatUrl['menuset'],$this->accessToken);
		$res = $this->request($url,"POST",[
			"headers" => [
				"Accept" => "application/json"
			],
			"json" => $options
		]);
		self::$cache && $this->redis->del(self::MENU . ":" . $this->appid);
		return true;
	}

	/**
	 * 查询自定义菜单
	 *
	 * @return
	 */
	public function getMenu(){
		$menuKey = self::MENU . ":" . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($menuKey))){
			$url = sprintf(self::$wechatUrl["menuget"],$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($menuKey,$res,self::$commonExpire);
		}
		return $res;
	}

	/**
	 * 删除菜单
	 *
	 * @return
	 */
	public function deleteMenu(){
		$url = sprintf(self::$wechatUrl["menudel"],$this->accessToken);
		$res = $this->request($url);
		self::$cache && $this->redis->del(self::MENU . ":" . $this->appid);
		return true;
	}
}