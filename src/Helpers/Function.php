<?php
function logger($message,$level = DEBUG,$context = []){
	$logger = Waljqiang\Wechat\Wechat::getInstance()->logger;
	return $logger->log($message,$level,$context);
}

/**
 * 随机生成指定长度的字符串
 * @return string 生成的字符串
 */
function getRandomStr($length = 16){
	$str = "";
	$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($str_pol) - 1;
	for ($i = 0; $i < $length; $i++) {
		$str .= $str_pol[mt_rand(0, $max)];
	}
	return $str;
}

function getRandomData($length = 6){
	$str = "";
	$str_pol = "0123456789";
	$max = strlen($str_pol) - 1;
	for ($i = 0; $i < $length; $i++) {
		$str .= $str_pol[mt_rand(0, $max)];
	}
	return $str;
}