<?php
namespace Waljqiang\Wechat;

use Monolog\Logger as MLogger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;

class Logger{
	private $config;
	private $logger;

	public function __construct($config){
		$this->config = $config;
		$logFile = $this->config["path"] . $this->config["channel"] . Carbon::now()->format("Ymd") . ".log";
		$this->logger = new MLogger($this->config["channel"]);
		$this->logger->pushHandler(new StreamHandler($logFile,constant("Monolog\Logger::" . strtoupper($this->config["level"]))));
	}

	/**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  mixed   $level   The log level
     * @param  string $message The log message
     * @param  array  $context The log context
     * @return bool   Whether the record has been processed
     */
    public function log($message, $level = DEBUG, array $context = []){
    	return $this->logger->log($level,$message,$context);
    }

	public function __call($method,$args){
		if(method_exists($this->logger,$method)){
			return call_user_func_array([$this->logger,$method],$args);
		}
	}
}