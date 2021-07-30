<?php
namespace Waljqiang\Wechat;
use Predis\Client;

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
        $this->redis = new Client($connection,$options);
        $this->enabled = $enabled;
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

    public function VagueDel($keyword){
        if($this->enabled){
            return $this->redis->getProfile()->vagueDelCommand($keyword);
        }
    }

    public function __call($method,$args){
        if($this->enabled){
            return call_user_func_array([$this->redis, $method], $args);
        }
    }
}