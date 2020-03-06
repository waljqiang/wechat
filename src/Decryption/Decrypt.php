<?php
namespace Waljqiang\Wechat\Decryption;

use Waljqiang\Wechat\Exceptions\WechatException;
use Carbon\Carbon;

/**
 * 公众账号的消息加解密
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 */
class Decrypt{
	private $token;
	private $encodingAesKey;
	private $appid;

	public function __construct($token,$encodingAesKey,$appid){
		if (strlen($encodingAesKey) != 43) {
			throw new WechatException("encodingAesKey 非法",WechatException::ENCODINGKEYERROR);
		}
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appid = $appid;
	}

	public function init($token,$encodingAesKey,$appid){
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appid = $appid;
	}

	/**
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 *
	 * @return
	 */
	public function encryptMsg($replyMsg, $timeStamp = NULL, $nonce = NULL){
		try{
			$pc = new Prpcrypt($this->encodingAesKey);
			$encrypt = $pc->encrypt($replyMsg,$this->appid);
			$timeStamp = is_null($timeStamp) ? Carbon::now()->timestamp : $timeStamp;
			$nonce = is_null($nonce) ? getRandomData(6) : $nonce;
			//生成安全签名
			$sha1 = new SHA1;
			$signature = $sha1->getSHA1($this->token,$timeStamp,$nonce,$encrypt);
			//生成发送的xml
			$xmlParse = new XMLParse;
			return $xmlParse->generate($encrypt,$signature,$timeStamp,$nonce);
		}catch(\Exception $e){
			throw new WechatException($e->getMessage(),$e->getCode());
		}
	}

	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $signature string 签名串，对应URL参数的msg_signature
	 * @param $timeStamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $encryptMsg string 密文，对应POST请求的数据
	 *
	 * @return
	 */
	public function decryptMsg($signature, $timeStamp, $nonce,$encryptMsg){
		$pc = new Prpcrypt($this->encodingAesKey);
		//提取密文
		$xmlparse = new XMLParse;
		$array = $xmlparse->extract($encryptMsg);
		$encrypt = $array["Encrypt"];
		$touserName = $array["ToUserName"];

		//验证安全签名
		$sha1 = new SHA1;
		$newSignature = $sha1->getSHA1($this->token,$timeStamp,$nonce,$encrypt);

		if ($signature != $newSignature) {
			throw new WechatException("签名验证错误",WechatException::SIGNATUREERROR);
		}
		return $pc->decrypt($encrypt,$this->appid);
	}
}