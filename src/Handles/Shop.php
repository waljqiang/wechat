<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Wechat;

class Shop extends Base{

	private $baseinfo = [
		"sid" => "",//商户自己id
      	"business_name" => "",//门店名称,15个汉字或30个英文字符内,不应包含地区、地址、分店名等信息
      	"branch_name" => "",//分店名称,20个字,不应包含地区信息，不应与门店名有重复
      	"province" => "",//门店所在省,10个字以内
      	"city" => "",//门店所在市,10个字以内
      	"district" => "",//门店所在区,10个字以内
      	"address" => "",//门店所在详细街道地址,不要填写省市信息
      	"telephone" => "",//门店电话,纯数字,区号,分机号均由"-"隔开
      	"categories" => [],//门店分类,不同级分类用","隔开
      	"offset_type" => 1,//坐标类型,坐标类型:1,为火星坐标;2,sogou经纬度;3,为百度经纬度;4,mapbar经纬度;5,GPS坐标;6,sogou墨卡托坐标 注：高德经纬度无需转换可直接使用
      	"longitude" => 0,//门店所在经度
      	"latitude" => 0,//门店所在维度
      	"photo_list" => [],//图片列表
      	"recommend" => "",//推荐品,不超过200字
      	"special" => "",//特殊服务,不超过200字
      	"introduction" => "",//商户简介,主要介绍商户信息等 300字以内
      	"open_time" => "",//营业时间,24 小时制表示,用"-"连接,如 8:00-20:00
      	"avg_price" => 0//人均价格
	];

	/**
	 * 上传图片到微信公众平台
	 *
	 * @param  string $imageUrl 图片地址
	 * @return string 图片在微信公众平台地址
	 */
	public function uploadImage($imageUrl){
		$pathinfo = pathinfo($imageUrl);
		if(!in_array($pathinfo["extension"],self::SHOPIMAGE)){
			throw new WechatException("Image is allowed by " . implode(",",self::AVATARTYPE),WechatException::UNSUPPORTFILETYPE);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["shop"]["upimage"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res["url"];
	}

	/**
	 * 创建门店
	 *
	 * @param  array $data 门店信息数组
	 * @return
	 */
	public function createShop($data){
		$buffer['business']['base_info'] = array_intersect_key($data,$this->baseinfo);
		$url = sprintf($this->api["shop"]["add"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		//清楚门店列表缓存
		$this->wechat->getRedis()->del(self::SHOPLIST . $this->wechat->getAppid());
		return $res["poi_id"];
	}

	/**
	 * 查询门店信息
	 *
	 * @param  string $poiID 门店poi_id
	 * @return array
	 */
	public function getShop($poiID){
		$shopKey = self::SHOP . $this->wechat->getAppid() . ":" . $poiID;
		if(!($res = $this->wechat->getRedis()->getValues($shopKey))){
			$url = sprintf($this->api["shop"]["get"],$this->wechat->getAccessToken());
			$data = $this->wechat->request($url,"POST",["json" => ["poi_id" => $poiID]]);
			$res = isset($data["business "]["base_info"]) ? $data["business"]["base_info"] : [];
			$this->wechat->getRedis()->setValues($shopKey,$res,Wechat::$common_expire_in);
		}
		return $res;
	}

	/**
	 * 查询门店列表
	 *
	 * @param  integer $pageIndex  页码
	 * @param  integer $pageOffset 每页记录数
	 * @return
	 */
	public function getShopList($pageIndex = 1,$pageOffset = 10){
		$shopListKey = self::SHOPLIST . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($shopListKey))){
			$start = 0;
			$limit = 50;
			$data = $this->_getShopList($start,$limit);
			$res["total"] = $data["total"];
			$res["list"] = $data["data"]["total"] > 0 ? array_column($data["business_list"],"base_info") : [];
			while(count($data["business_list"]) > 0){
				$start = $start + $limit;
				$data = $this->_getShopList($start,$limit);
				$shopList = count($data["data"]["business_list"]) > 0 ? array_column($data["business_list"],"base_info") : [];
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

	//修改门店信息
	public function modifyShop($data){
		$buffer['business']['base_info'] = array_intersect_key($data,[
			"poi_id" => "",
			"sid" => "",
	      	"telephone" => "",
	      	"photo_list" => [],
	      	"recommend" => "",
	      	"special" => "",
	      	"introduction" => "",
	      	"open_time" => "",
	      	"avg_price" => 0
		]);
		$url = sprintf($this->api["shop"]["set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		//清除门店列表缓存
		//清除门店缓存

		$keys = [
			self::SHOPLIST . $this->wechat->getAppid(),
			self::SHOPLIST . $this->wechat->getAppid() . ":" . $data["poi_id"]
		];
		$this->wechat->getRedis()->del($keys);

		return true;
	}

	//删除门店
	public function deleteShop($poiID){
		$url = sprintf($this->api["shop"]["del"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["poi_id" => $poiID]]);
		//清除门店列表缓存
		//清除门店缓存
		$keys = [
			self::SHOPLIST . $this->wechat->getAppid(),
			self::SHOPLIST . $this->wechat->getAppid() . ":" . $data["poi_id"]
		];
		$this->wechat->getRedis()->del($keys);
		return true;
	}

	private function _getShopList($begin = 0,$limit = 20){
		$url = sprintf($this->api["shop"]["list"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => ["begin" => $begin,"limit" => $limit]]);
		return $res;
	}

}