<?php
namespace Waljqiang\Wechat\Redis\Script;

use Predis\Command\ScriptCommand;

class MatchDelKeysBy extends ScriptCommand{
	public function getKeysCount(){
        // Tell Predis to use all the arguments but the last one as arguments
        // for KEYS. The last one will be used to populate ARGV.
        return -1;
    }

    public function getScript(){
        return <<<LUA
			local keys = redis.call('keys',ARGV[1])
			for i = 1,#keys,10000 do
				redis.call('del',unpack(keys,i,math.min(i+9999,#keys)))
			end
			return #keys
LUA;
    }
}