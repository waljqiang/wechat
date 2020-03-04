<?php
namespace Waljqiang\Wechat;

class Handle{
	public static function create($className,$wechat){
		$class = __NAMESPACE__ . "\\Handles\\" . $className;
		return new $class($wechat);
	}
}