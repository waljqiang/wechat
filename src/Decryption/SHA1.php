<?php
namespace Waljqiang\Wechat\Decryption;

use Waljqiang\Wechat\Exceptions\WechatException;

/**
 * SHA1 class
 *
 * 计算公众平台的消息签名接口.
 */
class SHA1{
	/**
	 * 用SHA1算法生成安全签名
	 * @param string $token 票据
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 * @param string $encrypt 密文消息
	 */
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg){
		//排序
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
			return sha1($str);
		} catch (Exception $e) {
			throw new WechatException("sha加密生成签名失败",WechatException::SHAENCODEERROR);
		}
	}
}