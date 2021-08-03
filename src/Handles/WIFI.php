<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;

class WIFI extends Base{

	//获取wifi门店列表
	public function getWifiShopList($pageIndex = 1,$pageOffset = 10){
		$shopListKey = self::WIFISHOPLIST . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($shopListKey))){
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
			$this->wechat->getRedis()->setValues($shopListKey,$res,Wechat::$common_expire_in); 
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
		$shopKey = self::WIFISHOP . $this->wechat->getAppid() . ":" . $shopID;
		if(!($res = $this->wechat->getRedis()->getValues($shopKey))){
			$url = sprintf($this->api["wifi"]["shop"],$this->wechat->getAccessToken());
			$data = $this->wechat->request($url,"POST",["json" => ["shop_id" => $shopID]]);
			$res = $data["data"];
			$this->wechat->getRedis()->setValues($shopKey,$res,Wechat::$common_expire_in);
		}
		return $res;
	}

	//修改门店信息
	public function modifyWifiShop($data){
		$url = sprintf($this->api["wifi"]["shop_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		$keys = [
			self::SHOP . $this->wechat->getAppid() . ":" . $data["shop_id"],
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOP . $this->wechat->getAppid() . ":" . $data["shop_id"],
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid() . ":" . $data["shop_id"]
		];
		$this->wechat->getRedis()->del($keys);

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
		$url = sprintf($this->api["wifi"]["shop_clear"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		//清除门店信息缓存
		//清除门店列表缓存
		$keys = [
			self::SHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid() . ":" . $shopID
		];
		$this->wechat->getRedis()->del($keys);
		//清除所有设备列表缓存
		$this->wechat->getRedis()->vagueDelCommand(self::DEVICELIST . $this->wechat->getAppid());

		return true;
	}

	//添加密码型设备
	public function addPasswordDevice($shopID,$ssid,$password){
		$url = sprintf($this->api["wifi"]["dev_of_pwd_add"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"json" => [
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"password" => $password
			]
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		$keys = [
			self::SHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid() . ":" . $shopID
		];
		$this->wechat->getRedis()->del($keys);

		return true;
	}

	//添加portal型设备
	public function addPortalDevice($shopID,$ssid,$reset = false){
		$url = sprintf($this->api["wifi"]["dev_of_portal_add"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"json" => [
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"reset" => $reset
			]
		]);
		//清除门店信息缓存
		//清除门店列表缓存
		//清除设备列表缓存
		$keys = [
			self::SHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid() . ":" . $shopID
		];
		$this->wechat->getRedis()->del($keys);

		return $res["data"]["secretkey"];
	}

	//查询设备列表
	public function getDeviceList($pageIndex = 1,$pageOffset = 10,$shopID = ""){
		$deviceListKey = !empty($shopID) ? self::DEVICELIST . $this->wechat->getAppid() . ":" . $shopID : self::DEVICELIST . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($deviceListKey))){
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
			$this->wechat->getRedis()->setValues($deviceListKey,$res,Wechat::$common_expire_in); 
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
		$url = sprintf($this->api["wifi"]["dev_del"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["bssid" => $bssid]]);
		//清除缓存
		$keys = [
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid()
		];
		$this->wechat->getRedis()->del($keys);
		$this->wechat->getRedis()->vagueDelCommand(self::SHOP);
		$this->wechat->getRedis()->vagueDelCommand(self::WIFISHOP);
		$this->wechat->getRedis()->vagueDelCommand(self::DEVICELIST);

		return true;
	}

	//配置连网方式
	public function wifiQrcode($shopID,$ssid,$imageID = 0){
		$url = sprintf($this->api["wifi"]["wifi_qrcode"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"json" => [
				"shop_id" => $shopID,
				"ssid" => $ssid,
				"img_id" => $imageID
			]
		]);

		//清除缓存
		$keys = [
			self::SHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::SHOPLIST . $this->wechat->getAppid(),
			self::WIFISHOP . $this->wechat->getAppid() . ":" . $shopID,
			self::WIFISHOPLIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid(),
			self::DEVICELIST . $this->wechat->getAppid() . ":" . $shopID
		];
		$this->wechat->getRedis()->del($keys);
		return $res["data"]["qrcode_url"];
	}

	public function getWifiStatistics($begin,$end,$shopID = -1){
		$url = sprintf($this->api["wifi"]["statistics"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"json" => [
				"begin_date" => $begin,
				"end_date" => $end,
				"shop_id" => -1
			]
		]);
		return $res['data'];
	}

	//设置门店卡券投放信息
	public function setWifiCoupon($data){
		$url = sprintf($this->api["wifi"]["couponput_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $data]);
		return true;
	}

	//查询门店卡券投放信息
	public function getWifiCoupon($shopID){
		$url = sprintf($this->api["wifi"]["couponput_get"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["shop_id" => $shopID]]);
		return $res["data"];
	}

	public function _getWifiShopList($pageIndex = 1,$pageOffset = 10){
		$url = sprintf($this->api["wifi"]["shop_list"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["pageindex" => 1,"pagesize" => $pageOffset]]);
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
		$url = sprintf($this->api["wifi"]["dev_list"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		return $res;
	}
}