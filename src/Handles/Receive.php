<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;

class Receive extends Message{
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
        $response = "";
		$message = Wechat::$encode ? $this->decryptMessage() : $message;
		if(!empty($message)){
            $obj = simplexml_load_string($message,'SimpleXMLElement',LIBXML_NOCDATA);
            $obj = json_decode(json_encode($obj),true);
            //开放平台全网发布测试
            if($obj["ToUserName"] == $this->wechat->getPublishAccount()){
                $this->testPublish($obj,$timestamp,$nonce);
                exit(-1);
            }
        }
        return $obj;
	}

	public function decryptMessage(){
		return $message;
	}

	/**
	 * 全网发布测试
	 *
	 * @param  array $message   微信全网发布消息体
	 * @param  string $timestamp 时间戳
	 * @param  integer $nonce     随机数
	 * @return
	 */
	private function testPublish($message,$timestamp,$nonce){
		/*$res = "";
		switch ($message["MsgType"]) {
			case "value":
				//2模拟粉丝发送文本消息给专用测试公众号,第三方平台方需要根据文本消息的内容进行相应的响应
				break;
			
			default:
				# code...
				break;
		}


		$returnStr = '';
        switch (trim($object->MsgType)) {
            case "text":
                //2模拟粉丝发送文本消息给专用测试公众号，第三方平台方需根据文本消息的内容进行相应的响应
                if($object->Content == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
                    $contentStr = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
                    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
                    $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $contentStr,0);
                    $resultStr = OpenWechat::getInstance()->encryptMsg($resultStr,$timeStamp, $nonce);
                    echo $resultStr;
                }elseif(substr($object->Content,0,32) == 'QUERY_AUTH_CODE:queryauthcode@@@'){//3、模拟粉丝发送文本消息给专用测试公众号，第三方平台方需在5秒内返回空串表明暂时不回复，然后再立即使用客服消息接口发送消息回复粉丝
                    echo "";
                    @ob_flush();
                    @flush();
                    $queryauthcode = trim(str_replace("QUERY_AUTH_CODE:", "",$object->Content));
                    $query_auth_code = explode("@@@", $queryauthcode);
                    $query_auth_code = $query_auth_code[1];
                    $component_token = OpenWechat::getInstance()->init();
                    $url = sprintf(C('OPENWECHAT.api_query_auth'),$component_token);
                    $buffer = array('component_appid'=>C('OPENWECHAT.APPID'),'authorization_code'=>$query_auth_code);
                    $data = HJSON::decode($this->_curl($url,'POST',HJSON::encode($buffer)));
                    if(!isset($data['errCode'])){
                        $authorization_token = $data['authorization_info']['authorizer_access_token'];
                        //发客服消息
                        $url = sprintf(C('WEIXIN.CUSTOMERSERVERMSG'),$authorization_token);
                        $buffer = array(
                                "touser" => ''.$object->FromUserName.'',
                                "msgtype" => "text",
                                "text" => array(
                                        "content" => $queryauthcode.'_from_api'
                                    )
                            );
                        $rs = $this->_curl($url,'POST',HJSON::encode($buffer));
                    }
                }else{
                    
                }
                break;
            case "event":
                //1模拟粉丝触发专用测试公众号的事件，并推送事件消息到专用测试公众号，第三方平台方开发者需要提取推送XML信息中的event值，并在5秒内立即返回按照下述要求组装的文本消息给粉丝
                $contentStr = $object->Event . "from_callback";
                $returnStr = $this->responseText($object, $contentStr,$timeStamp,$nonce);
                echo $returnStr;
                break;
            default:
                # code...
                break;
        }
        exit(-1);*/
	}

    private function handleText($message,$timestamp,$nonce){
        /*$resultStr = "";
        $toUsername = trim($object->ToUserName);
        $keyword = trim($object->Content);
        $info = WechatModel::getInstance()->getData(array('authorizer_wxname'=>$toUsername),'wechat');
        $resultStr = $info['name'] . "欢迎您!";
        return $resultStr; */
    }
}