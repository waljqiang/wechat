<?php
namespace Waljqiang\Wechat\Handles;

class Shop extends Base{

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
		
		$url = sprintf(self::$wechatUrl["upimage"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res["url"];
	}
}