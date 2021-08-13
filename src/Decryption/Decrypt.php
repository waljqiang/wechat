<?php
namespace Waljqiang\Wechat\Decryption;

/**
 * 消息加解密.
 *
 * @copyright Copyright (c) 2021-2024 Tencent Inc.
 */

use Carbon\Carbon;
use Waljqiang\Wechat\Decryption\Prpcrypt;
use Waljqiang\Wechat\Decryption\SHA1;
use Waljqiang\Wechat\Decryption\Xmlparse;
use Waljqiang\Wechat\Exceptions\WechatException;

/**
 * 1.加密消息；
 * 2.验证消息的安全性，并对消息进行解密。
 */
class Decrypt{
	private $token;
	private $encodingAesKey;
	private $appId;

	private static $sha1;

	/**
	 * 构造函数
	 * @param $token string 公众平台上，开发者设置的token
	 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
	 * @param $appId string 公众平台的appId
	 */
	public function __construct($token = "", $encodingAesKey = "", $appId = ""){
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appId = $appId;
		if(!isset(self::$sha1)){
			self::$sha1 = new SHA1;
		}
	}

	public function init($token,$encodingAesKey,$appId){
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appId = $appId;
	}

	/**
	 * 消息加密打包.
	 * 对要发送的消息进行AES-CBC加密
	 * 生成安全签名
	 * 将消息密文和安全签名打包成xml格式
	 *
	 * @param $content string 待加密的消息，xml格式的字符串
	 * @param $timestamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 *
	 * @return string
	 * @throws Waljqiang\Wechat\Exceptions\WechatException 
	 */
	public function encryptMsg($content, $timestamp = null, $nonce = null){
		if(empty($this->appId)){
			throw new WechatException("The crypter is not init",WechatException::$DECRYPTERNOINIT);
		}
		$pc = new Prpcrypt($this->encodingAesKey);

		//加密
		$encrypt = $pc->encrypt($content, $this->appId);

		if ($timestamp == null) {
			$timestamp = Carbon::now()->timestamp;
		}

		if($nonce == null){
			$nonce = $pc->getRandomStr(6);
		}

		//生成安全签名
		//$sha1 = new SHA1;
		$signature = $this->getSignature([$this->token, $timestamp, $nonce, $encrypt]);

		//生成发送的xml
		/*$Encrypt 加密后的消息密文
	 	$MsgSignature 安全签名
	 	$TimeStamp 时间戳
	 	$Nonce 随机字符串*/
		$xmlparse = new XMLParse;
		$rs = $xmlparse->generate([
			"Encrypt" => "<![CDATA[" . $encrypt . "]]>",
			"MsgSignature" => "<![CDATA[" . $signature . "]]>",
			"TimeStamp" => $timestamp,
			"Nonce" => "<![CDATA[" . $nonce . "]]>"
		]);
		return $rs;
	}


	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * 利用收到的密文生成安全签名，进行签名验证
	 * 若验证通过，则提取xml中的加密消息
	 * 对消息进行解密
	 *
	 * @param $signature string 签名串，对应URL参数的msg_signature
	 * @param $timestamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $encryptMsg string 密文，对应POST请求的数据
	 *
	 * @return string 解密后的明文
	 * @throws Waljqiang\Wechat\Exceptions\WechatException
	 */
	public function decryptMsg($signature,$timestamp = null,$nonce,$encryptMsg){
		if(empty($this->appId)){
			throw new WechatException("The crypter is not init",WechatException::$DECRYPTERNOINIT);
		}

		if (strlen($this->encodingAesKey) != 43) {
			throw new WechatException("EncodingAesKey invalid",WechatException::$AESKEYINVALID);
		}

		$pc = new Prpcrypt($this->encodingAesKey);

		//提取密文
		$xmlparse = new XMLParse;
		$array = $xmlparse->extract($encryptMsg);

		if ($timestamp == null) {
			$timestamp = Carbon::now()->timestamp;
		}

		$encrypt = $array["Encrypt"];
		$touser_name = $array["ToUserName"];

		//验证安全签名
		$msgSignature = $this->getSignature([$this->token, $timestamp, $nonce, $encrypt]);
		
		if ($signature != $msgSignature) {
			throw new WechatException("Signature error",WechatException::$SIGNATUREERROR);
		}

		return $pc->decrypt($encrypt, $this->appId);
	}

	public function getSignature($data){
		return self::$sha1->getSHA1($data);
	}
}