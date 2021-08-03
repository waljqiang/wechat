<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Wechat;
use Waljqiang\Wechat\Pay\WxPay;
use Waljqiang\Wechat\Pay\WxPayOrderQuery;
use Waljqiang\Wechat\Pay\WxPayCloseOrder;
use Waljqiang\Wechat\Pay\WxPayRefund;
use Waljqiang\Wechat\Pay\WxPayRefundQuery;
use Waljqiang\Wechat\Pay\WxPayDownloadBill;
use Waljqiang\Wechat\Pay\WxPayConfig;
use Waljqiang\Wechat\Pay\WxPayUnifiedOrder;//统一下单输入对象
use Waljqiang\Wechat\Exceptions\WechatPayException;

class Pay extends Base{
    private $config;

    public function setPayConfig($config){
        $this->config = new WxPayConfig($config);
    }

	//统一下单接口
    public function unifiedOrder($data,$payConfig = [],$timeOut = 60){
    	$time = time();
    	$wxPayConfig = !empty($payConfig) ? new WxPayConfig($payConfig) : $this->config;
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
    public function orderQuery($input,$config = [],$timeOut = 60){
        return $this->excuteCommands($input,$config,$timeOut,__FUNCTION__);
    }

    //关闭订单
    public function closeOrder($input,$config = [],$timeOut = 60){
        return $this->excuteCommands($input,$config,$timeOut,__FUNCTION__);
    }

    //申请退款
    public function refund($input,$config = [],$timeOut = 60){
        return $this->excuteCommands($input,$config,$timeOut,__FUNCTION__);
    }

    //查询退款
    public function refundQuery($input,$config = [],$timeOut = 60){
        return $this->excuteCommands($input,$config,$timeOut,__FUNCTION__);
    }

    //下载对账单
    public function downloadBill($input,$config = [],$timeOut = 60){
        return $this->excuteCommands($input,$config,$timeOut,__FUNCTION__);
    }

    //查询订单 关闭订单 申请退款 查询退款 下载对账单
    /*public function __call($method,$args){
    	$data = $args[0];
    	$wxPayConfig = !empty($args[1]) ? new WxPayConfig($args[1]) : new WxPayConfig(Wechat::$config["pay"]);
    	$timeOut = isset($args[2]) ? $args[2] : 60;
    	$class = "Waljqiang\Wechat\Pay\WxPay" . ucwords($method);
    	$input = new $class;
    	foreach ($data as $key => $value) {
	    	$function = "Set" . ucwords($key);
	    	if(method_exists($input,$function)){
	    		$input->{$function}($value);
	    	}
	    }
	    $result = call_user_func_array([WxPay::class,$method],[$input,$wxPayConfig,$timeOut]);
	    return $this->out($result);
    }*/

    private function excuteCommands($datas,$config,$timeOut,$command){
        $class = "Waljqiang\Wechat\Pay\WxPay" . ucwords($command);
        $input = new $class;
        foreach ($datas as $key => $value) {
            $function = "Set" . ucwords($key);
            if(method_exists($input,$function)){
                $input->{$function}($value);
            }
        }
        $wxPayConfig = !empty($config) ? new WxPayConfig($config) : $this->config;
        $result = call_user_func_array([WxPay::class,$command],[$input,$wxPayConfig,$timeOut]);
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