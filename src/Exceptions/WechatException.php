<?php
namespace Waljqiang\Wechat\Exceptions;
class WechatException extends \Exception{
	const RESULTERROR = 10001;//返回结果解析错误
	const UNSUPPORT = 10002;//不支持的方法
	const USERREMARKERROR = 10003;//用户备注名必须小于30个字符
	const TAGNAMEERROR = 45158;//标签名长度超过30个字符
}