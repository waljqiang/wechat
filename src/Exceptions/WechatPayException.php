<?php
namespace Waljqiang\Wechat\Exceptions;

/**
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class WechatPayException extends \Exception{
	const ARRAYERROR = 11000;//数组数据异常
	const XMLERROR = 11001;//XML数据异常
	const SIGNATUREERROR = 11002;//签名错误
	const OUTTRADENONO = 11003;//缺少参数out_trade_no
	const BODYNO = 11004;//缺少参数body
	const TOTALFEENO = 11005;//缺少参数total_fee
	const TRADETYPENO = 11006;//缺少参数trade_type
	const OPENIDMUST = 11007;//缺少openid
	const PRODUCTIDMUST = 11008;//缺少product_id
	const SCENEINFOMUST = 11009;//缺少scene_info
	const CURLERROR = 11010;//curl出错
	const OUTTRADENOTRANSNO = 11011;//out_trade_no、transaction_id至少填一个
	const OUTREFUNDNONO = 11012;//缺少必填参数out_refund_no
	const REFUNDFEENO = 11013;//缺少必填参数refund_fee
	const OPUSERID = 11014;//缺少必填参数op_user_id
	const OOTRNO = 11015;//out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	const BILLDATENO = 11016;//缺少必填参数bill_date
	const AUTHCODENO = 11017;//提交被扫支付API接口中,缺少必填参数auth_code
	const INTERFACEURLNO = 11018;//缺少必填参数interface_url
	const RETURNCODENO = 11019;//缺少必填参数return_code
	const RESULTCODENO = 11020;//缺少必填参数result_code
	const USERIPNO = 11021;//缺少必填参数user_ip
	const EXECUTETIMENO = 11022;//缺少必填参数execute_time
	const LONGURLERROR = 11023;//需要转换的URL,签名用原串,传输需URL encode
	const PAYCODEERROR = 11024;//生成支付二维码失败
}