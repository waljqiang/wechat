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
	 */
	public function getSHA1($data){
		//排序
		try{
			sort($data, SORT_STRING);
			$str = implode($data);
			return sha1($str);
		}catch (Exception $e) {
			throw new WechatException($e->getMessage(),WechatException::$SIGNATUREERROR);
		}
	}

}