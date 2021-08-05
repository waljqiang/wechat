# wechat
sdk for wechat official palt

## 备注

* 因微信接口请求次数有限制，所以尽量不要关闭Redis缓存，即Redis配置中的enabled应该设为true,如果强制关闭Redis缓存，则用户需要根据自己需要实现缓存。

* access_token缓存时间不应该超过微信文档中提到的access_token过期时间。

## 调试

* 请自行将日志路径设置到需要存放的位置，同时日志级别设置自己需要的日志级别即可，线上环境建议将日志信息设置为ERROR。

## 消息说明

* 如果公众号配置的是明文消息，请将Wechat类的$encoded属性设置为false，如果公众号中配置的是加密消息，请将该属性设置为true。

* 公众号推送的消息，接收后请导向$wechat->handleWechatMessage($message,$appid,$signature,$timestamp,$nonce,$echostr)来解析，该方法解析后可将消息转变成数组。

* 如需要公众号回复用户消息，请使用$wechat->replyUser($messageType,$message)方法，其中$messageType在Waljqiang\Wechat\Handles\Reply::class中已经定义好，不要使用其他的消息类型值。
## API文档
* 访问wechat/docs/api.html文件查看