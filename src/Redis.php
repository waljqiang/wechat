<?php
namespace Waljqiang\Wechat;
use Predis\Client;

class Redis{
	private $redis;

	public function __construct($connection,$options=[]){
		$this->redis = new Client($connection,$options);
	}


    public function setValues($key,$value,$ttl = NULL){
    	return !empty($ttl) ? $this->redis->setex($key,$ttl,serialize($value)) : $this->redis->set($key,serialize($value));
    }

    public function getValues($key){
    	$result = unserialize($this->redis->get($key));
    	return !empty($result) ? $result : "";
    }

    public function __call($method,$args){
    	return call_user_func_array([$this->redis, $method], $args);
    }
}