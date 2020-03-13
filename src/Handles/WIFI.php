<?php
namespace Waljqiang\Wechat\Handles;

class WIFI extends Base{
	/**
	 * 微信获取WiFi门店列表API地址
	 */
	const UWIFISHOPS = "https://api.weixin.qq.com/bizwifi/shop/list?access_token=%s";
	/**
	 * 微信查询WiFi门店信息API地址
	 */
	const UWIFISHOP = "https://api.weixin.qq.com/bizwifi/shop/get?access_token=%s";
	/**
	 * 微信修改WiFi门店信息API地址
	 */
	const UWIFISHOPMODIFY = "https://api.weixin.qq.com/bizwifi/shop/update?access_token=%s";
	/**
	 * 微信清空门店网络设备API地址
	 */
	const UWIFISHOPCLEAR = "https://api.weixin.qq.com/bizwifi/shop/clean?access_token=%s";
	/**
	 * 微信添加密码型设备API地址
	 */
	const UDEVICETOPWD = "https://api.weixin.qq.com/bizwifi/device/add?access_token=%s";
	/**
	 * 微信添加portal型设备API地址
	 */
	const UDEVICETOPORTAL = "https://api.weixin.qq.com/bizwifi/apportal/register?access_token=%s";
	/**
	 * 微信查询设备列表API地址
	 */
	const UDEVICELIST = "https://api.weixin.qq.com/bizwifi/device/list?access_token=%s";
	/**
	 * 微信删除设备API地址
	 */
	const UDEVICEDEL = "https://api.weixin.qq.com/bizwifi/device/delete?access_token=%s";
	/**
	 * 微信配置连网方式API地址
	 */
	const UWIFIQRCODE = "https://api.weixin.qq.com/bizwifi/qrcode/get?access_token=%s";
	/**
	 * 微信统计WiFi数据API地址
	 */
	const USTATISTICS = "https://api.weixin.qq.com/bizwifi/statistics/list?access_token=%s";
	/**
	 * 微信设置门店卡券投放API地址
	 */
	const UCOUPONPUT = "https://api.weixin.qq.com/bizwifi/couponput/get?access_token=%s";
	/**
	 * 微信查询门店卡券信息API地址
	 */
	const UCOUPONGET = "https://api.weixin.qq.com/bizwifi/couponput/get?access_token=%s";

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
			$url = sprintf(self::UWIFISHOP,$this->accessToken);
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
		$url = sprintf(self::UWIFISHOPMODIFY,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		if(self::$cache){
			$keys = [
				self::SHOP . $this->appid . ":" . $data["shop_id"],
				self::SHOPLIST . $this->appid,
				self::WIFISHOP . $this->appid . ":" . $data["shop_id"],
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $data["shop_id"]
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
		$url = sprintf(self::UWIFISHOPCLEAR,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		if(self::$cache){
			$keys = [
				self::SHOP . $this->appid . ":" . $shopID,
				self::SHOPLIST . $this->appid,
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID
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
		$url = sprintf(self::UDEVICETOPWD,$this->accessToken);
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
				self::SHOP . $this->appid . ":" . $shopID,
				self::SHOPLIST . $this->appid,
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID
			];
			$this->redis->del($keys);
		}
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	//添加portal型设备
	public function addPortalDevice($shopID,$ssid,$reset = false){
		$url = sprintf(self::UDEVICETOPORTAL,$this->accessToken);
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
				self::SHOP . $this->appid . ":" . $shopID,
				self::SHOPLIST . $this->appid,
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID
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

	//删除设备
	public function deleteDevice($bssid){
		$url = sprintf(self::UDEVICEDEL,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode(["bssid" => $bssid],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		//清除缓存
		if(self::$cache){
			$keys = [
				self::SHOPLIST . $this->appid,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid
			];
			$this->redis->del($keys);
			$this->redis->matchDel(self::SHOP . "*");
			$this->redis->matchDel(self::WIFISHOP . "*");
			$this->redis->matchDel(self::DEVICELIST . "*");
		}
		return true;
	}

	//配置连网方式
	public function wifiQrcode($shopID,$ssid,$imageID = 0){
		$url = sprintf(self::UWIFIQRCODE,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode([
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"img_id" => $imageID
			],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		//清除缓存
		if(self::$cache){
			$keys = [
				self::SHOP . $this->appid . ":" . $shopID,
				self::SHOPLIST . $this->appid,
				self::WIFISHOP . $this->appid . ":" . $shopID,
				self::WIFISHOPLIST . $this->appid,
				self::DEVICELIST . $this->appid,
				self::DEVICELIST . $this->appid . ":" . $shopID
			];
			$this->redis->del($keys);
		}
		return $res["data"]["qrcode_url"];
	}

	public function getWifiStatistics($begin,$end,$shopID = -1){
		$url = sprintf(self::USTATISTICS,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode([
				"begin_date" => $begin,
				"end_date" => $end,
				"shop_id" => -1
			],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res['data'];
	}

	//设置门店卡券投放信息
	public function setWifiCoupon($data){
		$url = sprintf(self::UCOUPONPUT,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($data,JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return true;
	}

	//查询门店卡券投放信息
	public function getWifiCoupon($shopID){
		$url = sprintf(self::UCOUPONGET,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode(["shop_id" => $shopID],JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res["data"];
	}

	public function _getWifiShopList($pageIndex = 1,$pageOffset = 10){
		$url = sprintf(self::UWIFISHOPS,$this->accessToken);
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
		$url = sprintf(self::UDEVICELIST,$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}
}