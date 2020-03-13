<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Pay\WxPayUnifiedOrder;//统一下单输入对象
use Waljqiang\Wechat\Pay\WxPayConfig;
use Waljqiang\Wechat\Pay\WxPay;
use Waljqiang\Wechat\Exceptions\WechatPayException;

class Pay extends Base{

	//统一下单接口
    public function unifiedOrder($data,$payConfig = [],$timeOut = 6){
    	$time = time();
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : new WxPayConfig(Wechat::$config["pay"]);
    	$data["out_trade_no"] = isset($data["out_trade_no"]) ? $data["out_trade_no"] : $wxPayConfig->MCHID . date("YmdHis",$time);
    	$data["fee_type"] = isset($data["fee_type"]) ? $data["fee_type"] : "CNY";
    	$data["time_start"] = date("YmdHis",$time);
    	$data["time_expire"] = date("YmdHis",$time + $data["expire"]);
    	$input = new WxPayUnifiedOrder();//统一支付输入对象

	    //设置支付异步通知地址
	    $input->SetNotify_url($wxPayConfig->NOTIFY_URL);
	    //设置统一订单属性
	    foreach ($data as $key => $value) {
	    	$method = "Set" . ucwords($key);
	    	if(method_exists($input,$method)){
	    		$input->{$method}($value);
	    	}
	    }

	    $result = WxPay::unifiedOrder($input,$wxPayConfig,$timeOut);
	    
	    if($result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS"){
    		//$url = "http://paysdk.weixin.qq.com/example/qrcode.php?data=" . urlencode($result["code_url"]);
    		unset($result["return_code"]);
    		unset($result["result_code"]);
    		unset($result["return_msg"]);
    		unset($result["err_code"]);
    		unset($result["err_code_des"]);
    		return $result;
	    }else{
	    	throw new WechatPayException($result["return_msg"],WechatPayException::PAYCODEERROR);
	    }
    }
}