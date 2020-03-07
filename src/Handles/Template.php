<?php
namespace Waljqiang\Wechat\Handles;

class Template extends Base{

	/**
	 * 设置所属行业
	 *
	 * @param string $primaryNumber   主营行业编号
	 * @param string $secondaryNumber 副营行业编号
	 */
	public function setIndustry($primaryNumber,$secondaryNumber){
		$url = sprintf(self::$wechatUrl["industryset"],$this->accessToken);
		$buffer = [
			"industry_id1" => $primaryNumber,
			"secondaryNumber" => $secondaryNumber
		];
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		self::$cache && $this->redis->del(self::INDUSTRY . $this->appid);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	/**
	 * 获取所属行业
	 *
	 * @return
	 */
	public function getIndustry(){
		$industryKey = self::INDUSTRY . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($industryKey))){
			$url = sprintf(self::$wechatUrl["industryget"],$this->accessToken);var_dump($url);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($industryKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
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
		$url = sprintf(self::$wechatUrl["templateid"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode(["template_id_short" => $templateIdShort],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res['template_id'];
	}

	/**
	 * 获取模板列表
	 *
	 * @return
	 */
	public function getTemplateList(){
		$tplListKey = self::TEMPLATELIST . $this->appid;
		if(!self::$cache || !($list = $this->redis->getValues($tplListKey))){
			$url = sprintf(self::$wechatUrl["templatelist"],$this->accessToken);
			$res = $this->request($url);
			$list = $res["template_list"];
			$this->redis->setValues($tplListKey,$list,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
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
		$url = sprintf(self::$wechatUrl["templatedel"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode(["template_id" => $templateID],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		//清楚模板列表缓存
		self::$cache && $this->redis->del(self::TEMPLATELIST . $this->appid);
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
		$url = sprintf(self::$wechatUrl["templatedel"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return isset($res["msgid"]) ? $res["msgid"] : false;
	}

}