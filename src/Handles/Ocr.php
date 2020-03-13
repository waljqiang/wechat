<?php
namespace Waljqiang\Wechat\Handles;

class Ocr extends Base{
	/**
	 * 微信身份证识别API地址
	 */
	const UIDCARD = "https://api.weixin.qq.com/cv/ocr/idcard?img_url=%s&access_token=%s";
	/**
	 * 微信银行卡识别API地址
	 */
	const UBANKCARD = "https://api.weixin.qq.com/cv/ocr/bankcard?img_url=%s&access_token=%s";
	/**
	 * 微信行驶证识别API地址
	 */
	const UDRIVECARD = "https://api.weixin.qq.com/cv/ocr/driving?img_url=%s&access_token=%s";
	/**
	 * 微信驾驶证识别API地址
	 */
	const UDRIVELICENSE = "https://api.weixin.qq.com/cv/ocr/drivinglicense?img_url=%s&access_token=%s";
	/**
	 * 微信营业执照识别API地址
	 */
	const UBIZLICENSE = "http://api.weixin.qq.com/cv/ocr/bizlicense?img_url=%s&access_token=%s";
	/**
	 * 微信通用印刷体识别API地址
	 */
	const UOCRCOMM = "http://api.weixin.qq.com/cv/ocr/comm?img_url=%s&access_token=%s";
	/**
	 * 身份证识别
	 *
	 * @return
	 */
	public function identityCard($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UIDCARD,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

	/**
	 * 银行卡识别
	 */
	public function bankCard($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UBANKCARD,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

	/**
	 * 行驶证识别
	 */
	public function drivingCard($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UDRIVECARD,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

	/**
	 * 驾驶证识别
	 */
	public function drivingLicense($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UDRIVELICENSE,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

	/**
	 * 营业执照识别
	 */
	public function bizLicense($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UBIZLICENSE,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

	/**
	 * 通用印刷体识别
	 */
	public function orcComm($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf(self::UOCRCOMM,"ENCODE_URL",$this->accessToken);
		$res = $this->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		$res = $this->request($url);
		unset($res["errcode"]);
		unset($res["errmsg"]);
		return $res;
	}

}