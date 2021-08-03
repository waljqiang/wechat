<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;

class Template extends Base{

	/**
	 * 设置所属行业
	 *
	 * @param string $primaryNumber   主营行业编号
	 * @param string $secondaryNumber 副营行业编号
	 */
	public function setIndustry($primaryNumber,$secondaryNumber){
		$url = sprintf($this->api["template"]["industry_set"],$this->wechat->getAccessToken());
		$buffer = [
			"industry_id1" => $primaryNumber,
			"secondaryNumber" => $secondaryNumber
		];
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		$this->wechat->getRedis()->del(self::INDUSTRY . $this->wechat->getAppid());
		return true;
	}

	/**
	 * 获取所属行业
	 *
	 * @return
	 */
	public function getIndustry(){
		$industryKey = self::INDUSTRY . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($industryKey))){
			$url = sprintf($this->api["template"]["industry_get"],$this->wechat->getAccessToken());
			$res = $this->wechat->request($url);
			$this->wechat->getRedis()->setValues($industryKey,$res,Wechat::$common_expire_in);
		}
		return $res;
	}

	/**
	 * 获取模板ID
	 *
	 * @param  string $templateIdShort 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
	 * @return
	 */
	public function getTemplateID($templateIdShort){
		$url = sprintf($this->api["template"]["tpl_id_get"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["template_id_short" => $templateIdShort]]);
		return $res['template_id'];
	}

	/**
	 * 获取模板列表
	 *
	 * @return
	 */
	public function getTemplateList(){
		$tplListKey = self::TEMPLATELIST . $this->wechat->getAppid();
		if(!($list = $this->wechat->getRedis()->getValues($tplListKey))){
			$url = sprintf($this->api["template"]["tpl_list"],$this->wechat->getAccessToken());
			$res = $this->wechat->request($url);
			$list = $res["template_list"];
			$this->wechat->getRedis()->setValues($tplListKey,$list,Wechat::$common_expire_in);
		}
		return $list;
	}

	/**
	 * 删除模板
	 *
	 * @param  string $templateID 模板ID
	 * @return
	 */
	public function deleteTemplate($templateID){
		$url = sprintf($this->api["template"]["tpl_del"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["template_id" => $templateID]]);
		//清楚模板列表缓存
		$this->wechat->getRedis()->del(self::TEMPLATELIST . $this->wechat->getAppid());
		return true;
	}

	/**
	 * 发送模板消息
	 *
	 * @param  string $openID   用户openID
	 * @param  string $templateID 模板ID
	 * @param  array $data     消息体
	 * @param  string $url      模板跳转链接（海外帐号没有跳转能力）
	 * @param  string $appid    所需跳转到的小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系，暂不支持小游戏）
	 * @param  string $pagePath 所需跳转到小程序的具体页面路径，支持带参数,（示例index?foo=bar），要求该小程序已发布，暂不支持小游戏
	 * @return
	 */
	public function sendTemplate($openID,$templateID,$data,$url = "",$appid = "",$pagePath = ""){
		$buffer = [
			"touser" => $openID,
			"template_id" => $templateID,
			"data" => $data
		];
		if(!empty($url))
			$buffer["url"] = $url;
		if(!empty($appid)){
			$buffer["miniprogram"]["appid"] = $appid;
			if(!empty($pagePath))
				$buffer["miniprogram"]["pagepath"] = $pagePath;
		}
		$url = sprintf($this->api["template"]["send_tpl_msg"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		return isset($res["msgid"]) ? $res["msgid"] : false;
	}

}