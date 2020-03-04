<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;

class Receive extends Base{
	/**
	 * 处理公众号推送的消息
	 *
	 * 其中appid、signature、timestamp、nonce可以从接收微信消息的url的get参数中获得
	 * 其中message需要从$GLOBALS["HTTP_RAW_POST_DATA"]中取得
	 * 这些参数获取需要在接收微信消息的请求方法中取得
	 *
	 * @param  string $appid	公众号appid
	 * @param  string $signature 签名
	 * @param  string $timestamp 时间戳
	 * @param  string $nonce     随机数
	 * @param  string $message   消息体
	 * @param  string $echostr   随机字符串
	 * @return
	 */
	public function handleWechatMessage($message,$appid = "",$signature = "",$timestamp ="",$nonce = "",$echostr = ""){
		$message = Wechat::$encode ? $this->decryptMessage() : $message;
		$this->parseMessage($message,$timestamp,$nonce);
	}

	private function parseMessage($message,$timestamp,$nonce){
		$response = "";
		if(!empty($message)){
			$obj = simplexml_load_string($message,'SimpleXMLElement',LIBXML_NOCDATA);
			$obj = json_decode(json_encode($obj),true);
			var_dump($obj);
		}
		/*$responseMsg = '';
        if (!empty($msg)){
            $postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = trim($postObj->MsgType);
            //全网发布测试
            if($postObj->ToUserName == 'gh_3c884a361561'){
                $this->testPublish($postObj,$timeStamp,$nonce);
                exit(-1);
            }
            //todo微信支付
            switch ($msgType) {
                case 'text':
                case 'image':
                case 'voice':
                case 'video':
                case 'music':
                case 'news':
                    $str = $this->handleText($postObj,$timeStamp,$nonce);
                    break;
                case 'event':
                    $str = $this->handleEvent($postObj,$timeStamp,$nonce);
                    break;
                default:
                    $str = '';
                    break;
            }
            $responseMsg = $this->responseText($postObj, $str, $timeStamp,$nonce);
        }
        echo $responseMsg;
        exit(-1);*/
	}

	public function decryptMessage(){
		return $message;
	}
}