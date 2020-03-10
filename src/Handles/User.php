<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;

/**
 * 用户管理类
 * 
 * @author waljqiang<waljqiang@163.com>
 * @version 1.0
 * @link https://github.com/waljqiang/wechat.git
 */

class User extends Base{
	/**
	 * 创建公众号标签
	 *
	 * @param  string $tagName 标签名称
	 * @return
	 */
	public function setTag($tagName){
		if(strlen($tagName) >= 30){
			throw new WechatException("The name of tag must less 30",WechatException::TAGNAMEERROR);
		}
		$url = sprintf(self::$wechatUrl["tagset"],$this->accessToken);
		$res = $this->request($url,"POST",["body" => json_encode(["tag" => ["name" => $tagName]], JSON_UNESCAPED_UNICODE)]);
		self::$cache && $this->redis->del(self::TAG . $this->appid);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res["tag"]["id"];
	}

	/**
	 * 获取公众号标签
	 *
	 * @return
	 */
	public function getTag(){
		$tagKey = self::TAG . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($tagKey))){
			$url = sprintf(self::$wechatUrl["tagget"],$this->accessToken);
			$data = $this->request($url);
			$res = isset($data["tags"]) ? $data["tags"] : [];
			self::$cache && $this->redis->setValues($tagKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($data) . "]",DEBUG);
		}
		return $res;
	}

	/**
	 * 删除公众号标签
	 *
	 * 当某个标签下的粉丝超过10w时，后台不可直接删除标签。此时，开发者可以对该标签下的openid列表，先进行取消标签的操作，直到粉丝数不超过10w后，才可直接删除该标签。
	 * @param  int $tagID 标签ID
	 * @return
	 */
	public function deleteTag($tagID){
		//粉丝超过10w特殊处理
		$fans = $this->getTagFans($tagID,1,1000000000);
		if($fans['total'] > 1000000){
			$fansDel = array_chunk(array_slice($fans["list"],99999),50);
			foreach ($fansDel as $del) {
				$this->tagDelUsers($tagID,$del);
			}
		}
		$buffer = [
			"tag" => [
				"id" => $tagID
			]
		];
		$url = sprintf(self::$wechatUrl["tagdel"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer, JSON_UNESCAPED_UNICODE)
		]);
		if(self::$cache){
			$keys = [
				self::TAG . $this->appid,
				self::TAGFANS . $this->appid . ":" . $tagID
			];
			//删除公众号下标签缓存
			//删除标签下粉丝列表缓存
			$this->redis->del($keys);
			$keyword = self::USERTAGS . $this->appid . ":*";
			//删除粉丝下标签缓存
			$this->redis->matchDel($keyword);
		}
		return true;
	}

	/**
	 * 获取公众号标签下的粉丝列表
	 *
	 * @param  integer  $tagID      标签ID
	 * @param  integer $pageIndex  页码
	 * @param  integer $pageOffset 每页记录数
	 * @return
	 */
	public function getTagFans($tagID,$pageIndex = 1,$pageOffset = 10){
		$tagFansKey = self::TAGFANS . $this->appid . ":" . $tagID;
		if(!self::$cache || !($res = $this->redis->getValues($tagFansKey))){
			$data = $this->_getTagFans($tagID);
			$res["list"] = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
			while($data["count"] > 0 ){
				$data = $this->_getTagFans($tagID,$data["next_openid"]);
				$openIDs = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
				$res["list"] = array_merge($res["list"],$openIDs);
			}
			$res["total"] = count($res["list"]);
			self::$cache && $this->redis->setValues($tagFansKey,$res,self::$commonExpire);
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	/**
	 * 批量为用户打标签，一次最多支持20个
	 *
	 * @param  integer $tagID   标签ID
	 * @param  array $openIDs 多个用户openID
	 * @return
	 */
	public function tagToUsers($tagID,$openIDs){
		$url = sprintf(self::$wechatUrl["tagtousers"],$this->accessToken);
		$buffer = [
			"openid_list" => $openIDs,
			"tagid" => $tagID
		];
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		if(self::$cache){
			//删除该标签下粉丝列表
			$keys[] = self::TAGFANS . $this->appid . ":" . $tagID;
			//删除粉丝下标签列表
			foreach ($openIDs as $openID) {
				$keys[] = self::USERTAGS . $this->appid . ":" . $openID;
			}
			$this->redis->del($keys);
		}
		return true;
	}

	/**
	 * 批量为用户取消标签，一次最多支持50个
	 *
	 * @param  integer $tagID   标签ID
	 * @param  array $openIDs 多个用户openID
	 * @return
	 */
	public function tagDelUsers($tagID,$openIDs){
		$url = sprintf(self::$wechatUrl["tagdelusers"],$this->accessToken);
		$buffer = [
			"openid_list" => $openIDs,
			"tagid" => $tagID
		];
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		if(self::$cache){
			//删除该标签下粉丝列表
			$keys[] = self::TAGFANS . $this->appid . ":" . $tagID;
			//删除粉丝下标签列表
			foreach ($openIDs as $openID) {
				$keys[] = self::USERTAGS . $this->appid . ":" . $openID;
			}
			$this->redis->del($keys);
		}
		return true;
	}

	/**
	 * 获取用户标签列表
	 *
	 * @param  string $openID 用户openID
	 * @return
	 */
	public function getUserTags($openID){
		$userTagsKey = self::USERTAGS . $this->appid . ":" . $openID;
		if(!self::$cache || !($res = $this->redis->getValues($userTagsKey))){
			$buffer = [
				"openid" => $openID
			];
			$url = sprintf(self::$wechatUrl["usertags"],$this->accessToken);
			$res = $this->request($url,"POST",[
				"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
			]);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
			$res = $res["tagid_list"];
			self::$cache && $this->redis->setValues($userTagsKey,$res,self::$commonExpire);

		}
		return $res;
	}

	/**
	 * 为用户打备注
	 *
	 * @param string $openid openID
	 * @param string $remark 备注名
	 */
	public function setUserRemark($openID,$remark){
		if(strlen($remark) >= 30){
			throw new WechatException("The name of remark must less 30",WechatException::USERREMARKERROR);
		}
		$buffer = [
			"openid" => $openID,
			"remark" => $remark
		];
		$url = sprintf(self::$wechatUrl["userremarkset"],$this->accessToken);
		$res = $this->request($url,"POST",[
			"body" => json_encode($buffer,JSON_UNESCAPED_UNICODE)
		]);
		self::$cache && $this->redis->del(self::USERINFO . $this->appid . ":" . $openID);
		return true;
	}

	/**
	 * 获取用户基本信息
	 *
	 * @param  string $openID 用户openid
	 * @param  string $lang   语言，支持参数请查看微信获取用户基本信息接口说明
	 * @return
	 */
	public function getUserInfo($openID,$lang="zh_CN"){
		$userInfoKey = self::USERINFO . $this->appid . ":" . $openID;
		if(!self::$cache || !($res = $this->redis->getValues($userInfoKey))){
			$url = sprintf(self::$wechatUrl["userinfo"],$this->accessToken,$openID,$lang);
			$res = $this->request($url);
			self::$cache && $this->redis->setValues($userInfoKey,$res,self::$commonExpire);
			$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		}
		return $res;
	}

	/**
	 * 获取关注用户列表
	 *
	 * @param  integer $pageIndex  页码
	 * @param  integer $pageOffset 每页记录数
	 * @return
	 */
	public function getUserList($pageIndex = 0,$pageOffset = 10){
		$userListKey = self::USERLIST . $this->appid;
		if(!self::$cache || !($res = $this->redis->getValues($userListKey))){
			$data = $this->_getUserList();
			$res["total"] = $data["total"];
			$res["list"] = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
			while($data["count"] > 0){
				$data = $this->_getUserList($data["next_openid"]);
				$openIDs = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
				$res["list"] = array_merge($res["list"],$openIDs);
			}
			self::$cache && $this->redis->setValues($userListKey,$res,self::$commonExpire); 
		}
		$start = ($pageIndex-1) * $pageOffset;
		$end = $pageOffset;
		if($start < $res["total"])
			$list = array_slice($res["list"],$start,$end);
		else
			$list = [];
		return [
			"total" => $res["total"],
			"list" => $list
		];
	}

	private function _getTagFans($tagID,$openID = ""){
		$url = sprintf(self::$wechatUrl["tagfans"],$this->accessToken);
		$data = empty($openID) ? json_encode([ "tagid" => $tagID ],JSON_UNESCAPED_UNICODE) : json_encode([ "tagid" => $tagID,"next_openid" => $openID],JSON_UNESCAPED_UNICODE);
		$res = $this->request($url,"POST",[
			"body" => $data
		]);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}

	private function _getUserList($openID = ""){
		$url = sprintf(self::$wechatUrl["userlist"],$this->accessToken,$openID);
		$res = $this->request($url);
		$this->log && $this->logger->log("[" . __CLASS__ . "->" . __FUNCTION__ . "]Request[" . $url . "]result[" . json_encode($res) . "]",DEBUG);
		return $res;
	}
}