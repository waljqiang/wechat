<?php
namespace Waljqiang\Wechat;

use Monolog\Logger as MLogger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;

class Logger{
	private $logger;

	public function __construct($config){
		$logFile = $config["path"] . $config["channel"] . Carbon::now()->format("Ymd") . ".log";
		$this->logger = new MLogger($config["channel"]);
		$this->logger->pushHandler(new StreamHandler($logFile,constant("Monolog\Logger::" . strtoupper($config["level"]))));
	}

	public function log($message,$level = \Monolog\Logger::DEBUG,$context = []){
		return $this->logger->log($level,$message,$context);
    }

	public function __call($method,$args){
           return call_user_func_array([$this->logger, $method], $args);
    }
}