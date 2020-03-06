# wechat
sdk for wechat official palt

## 备注
* 引入代码包后，需要执行以下命令用以生成应用的配置文件信息，如果不执行该命令，应用则会使用默认配置文件

* 因微信接口请求次数有限制，所以尽量不要关闭缓存，即配置文件中的cache应该设为true,如果强制关闭cache缓存，则用户需要根据自己需要实现缓存。

* access_token缓存时间不应该超过微信文档中提到的access_token过期时间。

## 调试

* 需要调试时，可将配置文件中log下的enable设置为true，同时日志级别设置自己需要的日志级别即可，线上环境建议关闭日志信息或将日志信息设置为ERROR，如果需要保留一些基本信息，可将日志级别设置为INFO。

* 调试信息使用Monolog\Logger日志形式输出，在Waljqiang\Wechat\Wechat类、Base类及其子类中已经载入，可以使用$this->logger->log($message,$level=DEBUG,$context=[])来写入日志

* 提供了公共函数logger($message,$level=DEBUG,$context=[])方法

## 消息说明

* 如果公众号配置的是明文消息，请将配置文件中的encode改为false，如果公众号中配置的是加密消息，请将配置文件中的encode改为true。

* 公众号推送的消息，接收后请导向Wechat::getInstance()->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce,$echostr)来解析，该方法解析后可将消息转变成数组。

* 如需要公众号回复用户消息，请使用Wechat::getInstance()->replyUser($messageType,$message)方法，其中$messageType在Waljqiang\Wechat\Handles\Reply::class中已经定义好，不要使用其他的消息类型值。
