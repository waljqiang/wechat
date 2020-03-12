<?php
namespace Waljqiang\Wechat\Handles;

class WIFI extends Base{

	//获取wifi门店列表
	public function getWifiShopList($pageIndex = 1,$pageOffset = 10){
		$shopListKey = self::WIFISHOPLIST . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($shopListKey))){
			$start = 1;
			$size = 20;
			$data = $this->_getWifiShopList($start,$size);
			$res["total"] = $data["data"]["totalcount"];
			$res["list"] = count($data["data"]["records"]) > 0 ? $data["data"]["records"] : [];
			while(count($data["data"]["records"]) > 0){
				$start = $start + 1;
				$data = $this->_getWifiShopList($start,$size);
				$shopList = count($data["data"]["records"]) > 0 ? $data["data"]["records"] : [];
				$res["list"] = array_merge($res["list"],$shopList);
			}
			self::$cache && $this->redis->setValues($shopListKey,$res,self::$commonExpire); 
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	//查询wifi门店信息
	public function getWifiShop($shopID){
		$shopKey = self::WIFISHOP . $this->appid . ":" . $shopID;
		if(!self::$cache || !($res = $this->redis->getValues($shopKey))){
			$url = sprintf(self::$wechatUrl["wifishop"],$this->accessToken);
			$data = $this->request($url,"POST",[
				"body" => json_encode(["shop_id" => $shopID],JSON_UNESCAPED_UNICODE)
			]);
			$res = $data["data"];
			self::$cache && $this->redis->setValues($shopKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($data) . "]",DEBUG);
		}
		return $res;
	}

	//修改门店信息
	public function modifyWifiShop($data){
		$url = sprintf(self::$wechatUrl["wifishopmodify"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		if(self::$cache){
			$keys = [
				self::WIFISHOP . $this->appid . ":" . $data["shop_id"],
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $data["shop_id"],
				self::DEVICELIST . $this->appid
			];
			$this->redis->del($keys);
		}
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	//清空门店网络设备
	public function clearWifiShop($shopID,$ssid = ""){
		$data = !empty($ssid) ? [
			"shop_id" => $shopID,
			"ssid" => $ssid
		] : [
			"shop_id" => $shopID
		];
		$url = sprintf(self::$wechatUrl["wifishopclear"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		if(self::$cache){
			$keys = [
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid
			];
			$this->redis->del($keys);
			//清除所有设备列表缓存
			$this->redis->matchDel(self::DEVICELIST . $this->appid . "*");
		}
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	//添加密码型设备
	public function addPasswordDevice($shopID,$ssid,$password){
		$url = sprintf(self::$wechatUrl["devicetopwd"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode([
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"password" => $password
			],JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		if(self::$cache){
			$keys = [
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID,
				self::DEVICELIST . $this->appid
			];
			$this->redis->del($keys);
		}
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	//添加portal型设备
	public function addPortalDevice($shopID,$ssid,$reset = false){
		$url = sprintf(self::$wechatUrl["devicetoportal"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode([
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"reset" => $reset
			],JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		if(self::$cache){
			$keys = [
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID,
				self::DEVICELIST . $this->appid
			];
			$this->redis->del($keys);
		}
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res["data"]["secretkey"];
	}

	//查询设备列表
	public function getDeviceList($pageIndex = 1,$pageOffset = 10,$shopID = ""){
		$deviceListKey = !empty($shopID) ? self::DEVICELIST . $this->appid . ":" . $shopID : self::DEVICELIST . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($deviceListKey))){
			$start = 1;
			$size = 20;
			$data = $this->_getDeviceList($start,$size);
			$res["total"] = $data["data"]["totalcount"];
			$res["list"] = count($data["data"]["records"]) > 0 ? $data["data"]["records"] : [];
			while(count($data["data"]["records"]) > 0){
				$start = $start + 1;
				$data = $this->_getDeviceList($start,$size);
				$shopList = count($data["data"]["records"]) > 0 ? $data["data"]["records"] : [];
				$res["list"] = array_merge($res["list"],$shopList);
			}
			self::$cache && $this->redis->setValues($deviceListKey,$res,self::$commonExpire); 
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	public function _getWifiShopList($pageIndex = 1,$pageOffset = 10){
		$url = sprintf(self::$wechatUrl["wifishops"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode(["pageindex" => 1,"pagesize" => $pageOffset],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}

	public function _getDeviceList($pageIndex = 1,$pageOffset = 10,$shopID = ""){
		$buffer = !empty($shopID) ? [
			"pageindex" => $pageIndex,
			"pagesize" => $pageOffset,
			"shop_id" => $shopID
		] : [
			"pageindex" => $pageIndex,
			"pagesize" => $pageOffset
		];
		$url = sprintf(self::$wechatUrl["devicelist"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}
}