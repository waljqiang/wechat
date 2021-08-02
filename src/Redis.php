<?php
namespace Waljqiang\Wechat;
use Predis\Client;
use Waljqiang\Wechat\RedisCommands\VagueDel;

/**
 * redis处理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */
class Redis{
    private $enabled = true;
    private $redis;

	public function __construct($connection,$options=[],$enabled = true){
        $this->enabled = $enabled;
        $this->redis = new Client($connection,$options);
        //添加lua相关脚本
        $this->loaderLuas();
	}


    public function setValues($key,$value,$ttl = NULL){
        if($this->enabled){
    	   return !empty($ttl) ? $this->redis->setex($key,$ttl,serialize($value)) : $this->redis->set($key,serialize($value));
        }
    }

    public function getValues($key){
        if($this->enabled){
        	$result = unserialize($this->redis->get($key));
        	return !empty($result) ? $result : "";
        }
    }

    public function __call($method,$args){
        if($this->enabled){
            return call_user_func_array([$this->redis, $method], $args);
        }
    }

    private function loaderLuas(){
        $profile = $this->redis->getProfile();
        foreach (scandir(__DIR__ . "/RedisCommands") as $fileName) {
            if($fileName != "." && $fileName != ".."){
                $className = str_replace(strrchr($fileName, "."),"",$fileName);
                $commandName = lcfirst($className) . "Command";
                $class = __NAMESPACE__ . "\\RedisCommands\\" . $className;
                $profile->defineCommand($commandName,$class);
            }
        }
    }
}