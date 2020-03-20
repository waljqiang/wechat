<?php
namespace Waljqiang\Wechat\Pay;

use Waljqiang\Wechat\Exceptions\WechatPayException;

/**
 *
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * @author widyhu
 *
 */

class WxPay{
	/**
	 * 微信统一下单API请求地址
	 */
	const UNIFIEDORDER = "https://api.mch.weixin.qq.com/pay/unifiedorder";

	/**
	 * 微信查询订单API地址
	 */
	const ORDERQUERY = "https://api.mch.weixin.qq.com/pay/orderquery";

	/**
	 * 微信关闭订单API地址
	 */
	const CLOSEORDER = "https://api.mch.weixin.qq.com/pay/closeorder";

	/**
	 * 微信申请退款API地址
	 */
	const REFUND = "https://api.mch.weixin.qq.com/secapi/pay/refund";

	/**
	 * 微信查询退款API地址
	 */
	const REFUNDQUERY = "https://api.mch.weixin.qq.com/pay/refundquery";

	/**
	 * 微信下载对账单API地址
	 */
	const DOWNLOADBILL = "https://api.mch.weixin.qq.com/pay/downloadbill";

	/**
	 * 微信提交被扫支付API地址
	 */
	const MICROPAY = "https://api.mch.weixin.qq.com/pay/micropay";

	/**
	 * 微信撤销订单API地址
	 */
	const REVERSE = "https://api.mch.weixin.qq.com/secapi/pay/reverse";

	/**
	 * 微信测速上报API地址
	 */
	const REPORT = "https://api.mch.weixin.qq.com/payitil/report";

	/**
	 * 微信转换短链接API地址
	 */
	const SHORTURL = "https://api.mch.weixin.qq.com/tools/shorturl";


	/**
	 *
	 * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayUnifiedOrder $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */

	public static function unifiedOrder(WxPayUnifiedOrder $inputObj,WxPayConfig $wxPayConfig, $timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet()) {
			throw new WechatPayException("缺少统一支付接口必填参数out_trade_no",WechatPayException::OUTTRADENONO);
		}else if(!$inputObj->IsBodySet()){
			throw new WechatPayException("缺少统一支付接口必填参数body",WechatPayException::BODYNO);
		}else if(!$inputObj->IsTotal_feeSet()) {
			throw new WechatPayException("缺少统一支付接口必填参数total_fee",WechatPayException::TOTALFEENO);
		}else if(!$inputObj->IsTrade_typeSet()) {
			throw new WechatPayException("缺少统一支付接口必填参数trade_type",WechatPayException::TRADETYPENO);
		}

		//关联参数校验
		if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){
			throw new WechatPayException("trade_type为JSAPI时,openid为必填参数",WechatPayException::OPENIDMUST);
		}
		if($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()){
			throw new WechatPayException("trade_type为JSAPI时,product_id为必填参数",WechatPayException::PRODUCTIDMUST);
		}

		if($inputObj->GetTrade_type() == "MWEB" && !$inputObj->IsScene_info()){
			throw new WechatPayException("trade_type为MWEB时,scene_info为必填参数",WechatPayException::SCENEINFOMUST);
		}

		//异步通知url未设置，则使用配置文件中的url
		if(!$inputObj->IsNotify_urlSet()){
			$inputObj->SetNotify_url($wxPayConfig->NOTIFY_URL);//异步通知url
		}

		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetSpbill_create_ip($inputObj->GetCliIP());
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串
		//签名
		$inputObj->SetSign($wxPayConfig);
		$xml = $inputObj->ToXml();
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::UNIFIEDORDER, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::UNIFIEDORDER, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间
		return $result;
	}

	/**
	 *
	 * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayOrderQuery $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function orderQuery(WxPayOrderQuery $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WechatPayException("订单查询接口中,out_trade_no、transaction_id至少填一个",WechatPayException::OUTTRADENOTRANSNO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串
		$inputObj->SetSign($wxPayConfig);//签名
		$xml = $inputObj->ToXml();
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::ORDERQUERY, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::ORDERQUERY, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间
		return $result;
	}

	/**
	 *
	 * 关闭订单，WxPayCloseOrder中out_trade_no必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayCloseOrder $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function closeOrder(WxPayCloseOrder $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet()) {
			throw new WechatPayException("订单查询接口中,out_trade_no必填",WechatPayException::OUTTRADENONO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign($wxPayConfig);//签名
		$xml = $inputObj->ToXml();

		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::CLOSEORDER, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::CLOSEORDER, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

	/**
	 *
	 * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
	 * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayRefund $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refund(WxPayRefund $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WechatPayException("退款申请接口中,out_trade_no、transaction_id至少填一个",WechatPayException::OUTTRADENOTRANSNO);
		}else if(!$inputObj->IsOut_refund_noSet()){
			throw new WechatPayException("退款申请接口中,缺少必填参数out_refund_no",WechatPayException::OUTREFUNDNONO);
		}else if(!$inputObj->IsTotal_feeSet()){
			throw new WechatPayException("退款申请接口中,缺少必填参数total_fee",WechatPayException::TOTALFEENO);
		}else if(!$inputObj->IsRefund_feeSet()){
			throw new WechatPayException("退款申请接口中,缺少必填参数refund_fee",WechatPayException::REFUNDFEENO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign($wxPayConfig);//签名
		$xml = $inputObj->ToXml();
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml,self::REFUND,true,$timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::REFUND, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

	/**
	 *
	 * 查询退款
	 * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
	 * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
	 * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayRefundQuery $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function refundQuery(WxPayRefundQuery $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_refund_noSet() &&
			!$inputObj->IsOut_trade_noSet() &&
			!$inputObj->IsTransaction_idSet() &&
			!$inputObj->IsRefund_idSet()) {
			throw new WechatPayException("退款查询接口中,out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个",WechatPayException::OOTRNO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign($wxPayConfig);//签名
		$xml = $inputObj->ToXml();

		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml,self::REFUNDQUERY, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::REFUNDQUERY, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

	/**
	 * 下载对账单，WxPayDownloadBill中bill_date为必填参数
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayDownloadBill $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function downloadBill(WxPayDownloadBill $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsBill_dateSet()) {
			throw new WechatPayException("对账单接口中,缺少必填参数bill_date",WechatPayException::BILLDATENO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign($wxPayConfig);//签名
		$xml = $inputObj->ToXml();

		$response = self::postXmlCurl($wxPayConfig,$xml, self::DOWNLOADBILL, false, $timeOut);

		if(substr($response, 0 , 5) == "<xml>"){
			$result = WxPayResults::Init($response,$wxPayConfig->KEY);
			return $result;
		}else{
			return [
				"return_code" => "SUCCESS",
				"result_code" => "SUCCESS",
				"content" => $response
			];
		}
	}

	/**
	 * 提交被扫支付API
	 * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
	 * 由商户收银台或者商户后台调用该接口发起支付。
	 * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayMicroPay $inputObj
	 * @param int $timeOut
	 */
	public static function micropay(WxPayMicroPay $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsBodySet()) {
			throw new WechatPayException("提交被扫支付API接口中,缺少必填参数body",WechatPayException::BODYNO);
		} else if(!$inputObj->IsOut_trade_noSet()) {
			throw new WechatPayException("提交被扫支付API接口中,缺少必填参数out_trade_no",WechatPayException::OUTTRADENONO);
		} else if(!$inputObj->IsTotal_feeSet()) {
			throw new WechatPayException("提交被扫支付API接口中,缺少必填参数total_fee",WechatPayException::TOTALFEENO);
		} else if(!$inputObj->IsAuth_codeSet()) {
			throw new WechatPayException("提交被扫支付API接口中,缺少必填参数auth_code",WechatPayException::AUTHCODENO);
		}

		$inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();

		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::MICROPAY, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::MICROPAY, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

	/**
	 *
	 * 撤销订单API接口，WxPayReverse中参数out_trade_no和transaction_id必须填写一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayReverse $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 */
	public static function reverse(WxPayReverse $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WechatPayException("撤销订单API接口中,参数out_trade_no和transaction_id必须填写一个",WechatPayException::OUTTRADENOTRANSNO);
		}

		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();

		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::REVERSE, true, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::REVERSE, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

	/**
	 *
	 * 测速上报，该方法内部封装在report中，使用时请注意异常流程
	 * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayReport $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function report(WxPayReport $inputObj,$wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsInterface_urlSet()) {
			throw new WechatPayException("接口URL,缺少必填参数interface_url",WechatPayException::INTERFACEURLNO);
		} if(!$inputObj->IsReturn_codeSet()) {
			throw new WechatPayException("返回状态码,缺少必填参数return_code",WechatPayException::RETURNCODENO);
		} if(!$inputObj->IsResult_codeSet()) {
			throw new WechatPayException("业务结果,缺少必填参数result_code",WechatPayException::RESULTCODENO);
		} if(!$inputObj->IsUser_ipSet()) {
			throw new WechatPayException("访问接口IP,缺少必填参数user_ip",WechatPayException::USERIPNO);
		} if(!$inputObj->IsExecute_time_Set()) {
			throw new WechatPayException("接口耗时,缺少必填参数execute_time",WechatPayException::EXECUTETIMENO);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']);//终端ip
		$inputObj->SetTime(date("YmdHis"));//商户上报时间
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();

		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::REPORT, false, $timeOut);
		return $response;
	}

	/**
	 *
	 * 生成二维码规则,模式一生成支付二维码
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayBizPayUrl $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function bizpayurl(WxPayBizPayUrl $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		if(!$inputObj->IsProduct_idSet()){
			throw new WechatPayException("生成二维码,缺少必填参数product_id",WechatPayException::PRODUCTIDMUST);
		}

		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetTime_stamp(time());//时间戳
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串

		$inputObj->SetSign();//签名

		return $inputObj->GetValues();
	}

	/**
	 *
	 * 转换短链接
	 * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
	 * 减小二维码数据量，提升扫描速度和精确度。
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayShortUrl $inputObj
	 * @param int $timeOut
	 * @throws WechatPayException
	 * @return 成功时返回，其他抛异常
	 */
	public static function shorturl(WxPayShortUrl $inputObj,WxPayConfig $wxPayConfig,$timeOut = 60){
		//检测必填参数
		if(!$inputObj->IsLong_urlSet()) {
			throw new WechatPayException("需要转换的URL,签名用原串,传输需URL encode",WechatPayException::LONGURLERROR);
		}
		$inputObj->SetAppid($wxPayConfig->APPID);//公众账号ID
		$inputObj->SetMch_id($wxPayConfig->MCHID);//商户号
		$inputObj->SetNonce_str(self::getNonceStr());//随机字符串
		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();
		$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::postXmlCurl($wxPayConfig,$xml, self::SHORTURL, false, $timeOut);
		$result = WxPayResults::Init($response,$wxPayConfig->KEY);
		self::reportCostTime(self::SHORTURL, $startTimeStamp, $result,$wxPayConfig);//上报请求花费时间

		return $result;
	}

 	/**
 	 *
 	 * 支付结果通用通知
 	 * @param function $callback
 	 * 直接回调函数使用方法: notify(you_function);
 	 * 回调类成员函数方法:notify(array($this, you_function));
 	 * $callback  原型为：function function_name($data){}
 	 */
	public static function notify($callback, &$msg){
		//获取通知的数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//如果返回成功则验证签名
		try {
			$result = WxPayResults::Init($xml,$wxPayConfig);
		} catch (WechatPayException $e){
			$msg = $e->getMessage();
			return false;
		}

		return call_user_func($callback, $result);
	}

	/**
	 *
	 * 产生随机字符串
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32){
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}

	/**
	 *
	 * 上报数据， 上报的时候将屏蔽所有异常流程
	 * @param string $usrl
	 * @param int $startTimeStamp
	 * @param array $data
	 */
	private static function reportCostTime($url, $startTimeStamp, $data,$wxPayConfig){
		//如果不需要上报数据
		if($wxPayConfig->REPORT_LEVENL == 0){
			return;
		}
		//如果仅失败上报
		if($wxPayConfig->REPORT_LEVENL == 1 &&
			 array_key_exists("return_code", $data) &&
			 $data["return_code"] == "SUCCESS" &&
			 array_key_exists("result_code", $data) &&
			 $data["result_code"] == "SUCCESS")
		 {
		 	return;
		 }

		//上报逻辑
		$endTimeStamp = self::getMillisecond();
		$objInput = new WxPayReport();
		$objInput->SetInterface_url($url);
		$objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
		//返回状态码
		if(array_key_exists("return_code", $data)){
			$objInput->SetReturn_code($data["return_code"]);
		}
		//返回信息
		if(array_key_exists("return_msg", $data)){
			$objInput->SetReturn_msg($data["return_msg"]);
		}
		//业务结果
		if(array_key_exists("result_code", $data)){
			$objInput->SetResult_code($data["result_code"]);
		}
		//错误代码
		if(array_key_exists("err_code", $data)){
			$objInput->SetErr_code($data["err_code"]);
		}
		//错误代码描述
		if(array_key_exists("err_code_des", $data)){
			$objInput->SetErr_code_des($data["err_code_des"]);
		}
		//商户订单号
		if(array_key_exists("out_trade_no", $data)){
			$objInput->SetOut_trade_no($data["out_trade_no"]);
		}
		//设备号
		if(array_key_exists("device_info", $data)){
			$objInput->SetDevice_info($data["device_info"]);
		}

		try{
			self::report($objInput,$wxPayConfig);
		} catch (WechatPayException $e){
			//不做任何处理
		}
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认60s
	 * @throws WechatPayException
	 */
	private static function postXmlCurl($wxPayConfig,$xml, $url, $useCert = false, $second = 60){
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		//如果有配置代理这里就设置代理
		if($wxPayConfig->CURL_PROXY_HOST != "0.0.0.0"
			&& $wxPayConfig->CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, $wxPayConfig->CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, $wxPayConfig->CURL_PROXY_PORT);
		}
		curl_setopt($ch,CURLOPT_URL, $url);

		if(stripos($url,"https://")!==FALSE){
	        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }else{
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		}
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if($useCert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $wxPayConfig->SSLCERT_PATH);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $wxPayConfig->SSLKEY_PATH);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_error($ch);
			curl_close($ch);
			throw new WechatPayException("curl出错，错误码:$error",WechatPayException::CURLERROR);
		}
	}

	/**
	 * 获取毫秒级别的时间戳
	 */
	private static function getMillisecond(){
		//获取毫秒的时间戳
		$time = explode ( " ", microtime () );
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode( ".", $time );
		$time = $time2[0];
		return $time;
	}
}

