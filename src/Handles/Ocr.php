<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

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
		
		$url = sprintf($this->api["ocr"]["idcard"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

	/**
	 * 银行卡识别
	 */
	public function bankCard($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["ocr"]["bankcard"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

	/**
	 * 行驶证识别
	 */
	public function drivingCard($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["ocr"]["drivecard"],$this->accessToken);
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

	/**
	 * 驾驶证识别
	 */
	public function drivingLicense($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["ocr"]["drivelicense"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

	/**
	 * 营业执照识别
	 */
	public function bizLicense($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["ocr"]["bizlicense"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

	/**
	 * 通用印刷体识别
	 */
	public function orcComm($imageUrl){
		if(!file_exists($imageUrl)){
			throw new WechatException("File is not exists",WechatException::FILENO);
		}
		
		$url = sprintf($this->api["ocr"]["common"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"GET",[
			"multipart" => [
				[
					"name" => "file",
		            "contents" => @fopen($imageUrl,"r")
		        ]
			]
		]);
		return $res;
	}

}