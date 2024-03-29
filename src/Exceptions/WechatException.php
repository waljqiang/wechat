<?php
namespace Waljqiang\Wechat\Exceptions;

/**
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class WechatException extends \Exception{
	const APPIDINVALID = 600900100;//appid无效
	const APPSECRETINVALID = 600900101;//secret无效
	const AESKEYINVALID = 600900102;//encodingAesKey无效
	const GENERATESIGANATUREFAILURE = 600900103;//生成签名失败
	const SIGNATUREERROR = 600900104;//签名验证错误
	const ENCODINGFAILURE = 600900105;//加密失败
	const DECRYPTFAILURE = 600900106;//解密失败
	const BASE64ENCODEFAILURE = 600900107;//base64加密失败
	const BASE64DECRYPTFAILURE = 600900108;//base64解密失败
	const BUFFERINIVALID = 600900109;//解密后的buffer非法
	const GENERATEXMLFAILURE = 600900110;//生成xml失败
	const XMLPARSEERROR = 600900111;//xml解析错误
	const ATTRIBUTEMISS = 600900112;//缺少必要的属性
	const HTTPREQUESTERROR = 600900113;//http请求失败
	const HTTPRESPONSEEXPLAINFAILURE = 600900114;//http返回结果解析失败
	const UNSUPPORTMETHOD = 600900115;//不支持的方法
	const TAGNAMEERROR = 600900116;//标签名长度超过30个字符
	const USERREMARKINVALID = 600900117;//用户备注名称不合法
	const UNSUPPORTFILETYPE = 600900118;//不支持的文件类型
	const FILENO = 600900119;//文件不存在
	const UNSUPPORTMESSAGETYPE = 600900120;//不支持的消息类型
	const DECRYPTERNOINIT = 600900121;//加密器未初始化
}