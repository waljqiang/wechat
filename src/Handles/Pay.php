<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Pay\WxPay;
use Waljqiang\Wechat\Pay\WxPayConfig;
use Waljqiang\Wechat\Pay\WxPayUnifiedOrder;//统一下单输入对象
use Waljqiang\Wechat\Pay\WxPayOrderQuery;
use Waljqiang\Wechat\Pay\WxPayCloseOrder;
use Waljqiang\Wechat\Pay\WxPayRefund;
use Waljqiang\Wechat\Pay\WxPayRefundQuery;
use Waljqiang\Wechat\Exceptions\WechatPayException;

class Pay extends Base{

	//统一下单接口
    public function unifiedOrder($data,$payConfig = [],$timeOut = 600){
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
	    return $this->out($result);
    }

    //查询订单
    public function orderQuery($data,$payConfig = [],$timeOut = 600){
    	if(!isset($data["out_trade_no"]) || !isset($data["transaction_id"])){
    		throw new WechatPayException("订单查询接口中,out_trade_no、transaction_id至少填一个",WechatPayException::OUTTRADENOTRANSNO);
    	}
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : new WxPayConfig(Wechat::$config["pay"]);
    	$input = new WxPayOrderQuery;
    	//设置订单查询属性
	    foreach ($data as $key => $value) {
	    	$method = "Set" . ucwords($key);
	    	if(method_exists($input,$method)){
	    		$input->{$method}($value);
	    	}
	    }
    	$result = WxPay::orderQuery($input,$wxPayConfig,$timeOut);
    	return $this->out($result);
    }

    //关闭订单
    public function closeOrder($outTradeNo,$wxPayConfig = [],$timeOut = 600){
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : new WxPayConfig(Wechat::$config["pay"]);
    	$input = new WxPayCloseOrder;
    	$input->SetOut_trade_no($outTradeNo);
    	$result = WxPay::closeOrder($input,$wxPayConfig,$timeOut);
    	return $this->out($result);
    }

    //申请退款
    public function refund($data,$wxPayConfig = [],$timeOut = 600){
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : new WxPayConfig(Wechat::$config["pay"]);
    	$input = new WxPayRefund;
    	foreach ($data as $key => $value) {
	    	$method = "Set" . ucwords($key);
	    	if(method_exists($input,$method)){
	    		$input->{$method}($value);
	    	}
	    }
	    $result = WxPay::refund($input,$wxPayConfig,$timeOut);
	    return $this->out($result);
    }

    //查询退款
    public function refundQuery($data,$wxPayConfig = [],$timeOut = 600){
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : new WxPayConfig(Wechat::$config["pay"]);
    	$input = new WxPayRefundQuery;
    	foreach ($data as $key => $value) {
	    	$method = "Set" . ucwords($key);
	    	if(method_exists($input,$method)){
	    		$input->{$method}($value);
	    	}
	    }
	    $result = WxPay::refundQuery($input,$wxPayConfig,$timeOut);
	    return $this->out($result);
    }

    private function out($result){
    	if($result["return_code"] == "SUCCESS"){
    		if($result["result_code"] == "SUCCESS"){
    			unset($result["return_code"]);
	    		unset($result["result_code"]);
	    		unset($result["return_msg"]);
	    		unset($result["err_code"]);
	    		unset($result["err_code_des"]);
	    		return $result;
    		}else{
    			throw new WechatPayException($result["err_code_des"],WechatPayException::PAYCODEERROR);
    		}
    	}else{
    		throw new WechatPayException($result["return_msg"],WechatPayException::PAYCODEERROR);
    	}
    }
}