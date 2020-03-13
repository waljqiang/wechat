<?php
namespace Waljqiang\Wechat\Handles;

/**
 * 账号管理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Account extends Base{
	/**
	 * 微信获取二维码ticket API地址
	 */
	const UQRCODETICKET = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
	/**
	 * 微信获取二维码API地址
	 */
	const UQRCODE = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s";
	/**
	 * 获取二维码ticket
	 *
	 * @param  integer,string  $str    场景值ID
	 * @param  string  $type   二维码类型
	 * @param  integer $expire 二维码过期时间,永久二维码不需要填
	 * @return
	 */
	private function getTicket($data){
		$url = sprintf(self::UQRCODETICKET,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}

	/**
	 * 获取用户二维码
	 *
	 * @param  integer  $str    场景值ID
	 * @param  string  $type   二维码类型,具体值请查看微信公众号文档说明
	 * @param  integer $expire 二维码过期时间
	 * @return
	 */
	public function getQrcode($str,$type = "QR_SCENE",$expire = 30){
		$data = in_array($type,["QR_SCENE","QR_LIMIT_SCENE"]) ? [
			"action_name" => $type,
			"action_info" => [
				"scene" => [
					"scene_id" => $str
				]
			]
		] : [
			"action_name" => $type,
			"action_info" => [
				"scene" => [
					"scene_str" => $str
				]
			]
		];
		switch ($type) {
			case "QR_SCENE":
			case "QR_STR_SCENE":
				$qrcodeKey = self::QRCODE . $this->appid . ":" . $type . ":" . $str . ":" . $expire;
				$data["expire_seconds"] = $expire;
				break;
			case "QR_LIMIT_SCENE":
			case "QR_LIMIT_STR_SCENE":
				$qrcodeKey = self::QRCODE . $this->appid . ":" . $type . ":" . $str;
				break;
			default:
				$qrcodeKey = self::QRCODE . $this->appid . $str . $expire;
				break;
		}
		if(!self::$cache || !($res = $this->redis->getValues($qrcodeKey))){
			$ticket = $this->getTicket($data);
			$res = sprintf(self::UQRCODE,urlencode($ticket["ticket"]));
			if(in_array($type,["QR_LIMIT_SCENE","QR_LIMIT_STR_SCENE"]))
				$this->redis->setValues($qrcodeKey,$res);
			else
				$this->redis->setValues($qrcodeKey,$res,$expire);
		}
		return $res;
	}
}