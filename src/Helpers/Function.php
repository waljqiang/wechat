<?php
function logger($message,$level = DEBUG,$context = []){
	$logger = Waljqiang\Wechat\Wechat::getInstance()->logger;
	return $logger->log($message,$level,$context);
}