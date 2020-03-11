<?php
namespace Waljqiang\Wechat\Handles;

class Ocr extends Base{
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
		
		$url = sprintf(self::$wechatUrl["idcard"],"ENCODE_URL",$this->accessToken);
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
		
		$url = sprintf(self::$wechatUrl["bankcard"],"ENCODE_URL",$this->accessToken);
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
		
		$url = sprintf(self::$wechatUrl["drivecard"],"ENCODE_URL",$this->accessToken);
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
		
		$url = sprintf(self::$wechatUrl["drivelicense"],"ENCODE_URL",$this->accessToken);
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
		
		$url = sprintf(self::$wechatUrl["bizlicense"],"ENCODE_URL",$this->accessToken);
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
		
		$url = sprintf(self::$wechatUrl["ocrcomm"],"ENCODE_URL",$this->accessToken);
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