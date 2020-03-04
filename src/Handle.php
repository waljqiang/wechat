<?php
namespace Waljqiang\Wechat;

class Handle{
	public static $handleType = [
		"Menu" => [
			"setMenu",
			"getMenu",
			"deleteMenu"
		],
		"User" => [
			"setTag",
			"getTag",
			"deleteTag",
			"getTagFans",
			"tagToUsers",
			"tagDelUsers",
			"getUserTags",
			"setUserRemark",
			"getUserInfo",
			"getUserList"
		],
	];
	public static function create($className,$wechat){
		$class = __NAMESPACE__ . "\\Handles\\" . $className;
		return new $class($wechat);
	}
}