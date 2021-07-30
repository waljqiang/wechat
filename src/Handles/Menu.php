<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

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
		$url = sprintf($this->api["menu_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $options]);
		$this->wechat->redis->del(self::MENU . $this->wechat->getAppid());
		return true;
	}

	/**
	 * 查询自定义菜单
	 *
	 * @return
	 */
	public function getMenu(){
		$menuKey = self::MENU . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($menuKey))){
			$url = sprintf(self::UMENUGET,$this->accessToken);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($menuKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		}
		return $res;
	}

	/**
	 * 删除菜单
	 *
	 * @return
	 */
	public function deleteMenu(){
		$url = sprintf(self::UMENUDEL,$this->accessToken);
		$res = $this->request($url);
		self::$cache && $this->redis->del(self::MENU . $this->appid);
		return true;
	}
}