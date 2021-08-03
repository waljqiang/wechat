<?php
namespace Waljqiang\Wechat\Exceptions;

/**
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class WechatPayException extends \Exception{
	const ARRAYERROR = 600901100;//数组数据异常
	const XMLERROR = 600901101;//XML数据异常
	const SIGNATUREERROR = 600901102;//签名错误
	const OUTTRADENONO = 600901103;//缺少参数out_trade_no
	const BODYNO = 600901104;//缺少参数body
	const TOTALFEENO = 600901105;//缺少参数total_fee
	const TRADETYPENO = 600901106;//缺少参数trade_type
	const OPENIDMUST = 600901107;//缺少openid
	const PRODUCTIDMUST = 600901108;//缺少product_id
	const SCENEINFOMUST = 600901109;//缺少scene_info
	const CURLERROR = 600901110;//curl出错
	const OUTTRADENOTRANSNO = 600901111;//out_trade_no、transaction_id至少填一个
	const OUTREFUNDNONO = 600901112;//缺少必填参数out_refund_no
	const REFUNDFEENO = 600901113;//缺少必填参数refund_fee
	const OPUSERID = 600901114;//缺少必填参数op_user_id
	const OOTRNO = 600901115;//out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
	const BILLDATENO = 600901116;//缺少必填参数bill_date
	const AUTHCODENO = 600901117;//提交被扫支付API接口中,缺少必填参数auth_code
	const INTERFACEURLNO = 600901118;//缺少必填参数interface_url
	const RETURNCODENO = 600901119;//缺少必填参数return_code
	const RESULTCODENO = 600901120;//缺少必填参数result_code
	const USERIPNO = 600901121;//缺少必填参数user_ip
	const EXECUTETIMENO = 600901122;//缺少必填参数execute_time
	const LONGURLERROR = 600901123;//需要转换的URL,签名用原串,传输需URL encode
	const PAYCODEERROR = 600901124;//生成支付二维码失败
}