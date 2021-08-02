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
	 * 获取二维码ticket
	 *
	 * @param  integer,string  $str    场景值ID
	 * @param  string  $type   二维码类型
	 * @param  integer $expire 二维码过期时间,永久二维码不需要填
	 * @return
	 */
	private function getTicket($data){
		$url = sprintf($this->api["account"]["qrcode_ticket"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
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
				$qrcodeKey = self::QRCODE . $this->wechat->getAppid() . ":" . $type . ":" . $str . ":" . $expire;
				$data["expire_seconds"] = $expire;
				break;
			case "QR_LIMIT_SCENE":
			case "QR_LIMIT_STR_SCENE":
				$qrcodeKey = self::QRCODE . $this->wechat->getAppid() . ":" . $type . ":" . $str;
				break;
			default:
				$qrcodeKey = self::QRCODE . $this->wechat->getAppid() . $str . $expire;
				break;
		}
		if(!($res = $this->wechat->getRedis()->getValues($qrcodeKey))){
			$ticket = $this->getTicket($data);
			$res = sprintf($this->api["account"]["qrcode"],urlencode($ticket["ticket"]));
			if(in_array($type,["QR_LIMIT_SCENE","QR_LIMIT_STR_SCENE"]))
				$this->wechat->getRedis()->setValues($qrcodeKey,$res);
			else
				$this->wechat->getRedis()->setValues($qrcodeKey,$res,$expire);
		}
		return $res;
	}
}