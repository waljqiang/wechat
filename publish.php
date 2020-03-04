<?php
if(!isset($argv[1])){
	echo "please input first params for the path of the config";
	exit(-1);
}
$path = $argv[1];
$source = __DIR__ . "/src/Configs/wechat.php";
$dest = $path . "/wechat.php";

if(copy($source,$dest) && file_put_contents(__DIR__ . "/src/defined.php","<?php define('CONF','$dest');")){
	echo "The config file wechat.php is generate to " . $path;
}else{
	echo "Failure";
}
exit(-1);