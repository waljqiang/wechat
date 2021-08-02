<?php
namespace Waljqiang\Wechat\Decryption;

use Waljqiang\Wechat\Exceptions\WechatException;

/**
 * XMLParse class
 *
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 */
class XmlParse{

	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取出的加密消息字符串
	 */
	public function extract($xmltext,$keys = ["Encrypt","ToUserName"]){
		try{
			$res = [];
			$xml = new \DOMDocument();
			$xml->loadXML($xmltext);
			foreach ($keys as $key) {
				$res[$key] = $xml->getElementsByTagName($key)->item(0)->nodeValue;
			}
			return $res;
		}catch(Exception $e) {
			throw new WechatException($e->getMessage(),WechatException::$XMLPARSEERROR);
		}
	}

	/**
	 * 生成xml消息
	 */
	public function generate($datas){
		$xml = "<xml>";
		foreach ($datas as $key => $value) {
			$xml .= "<" . $key . ">" . $value . "</" . $key . ">";
		}
		$xml .= "</xml>";
		return $xml;
	}

}