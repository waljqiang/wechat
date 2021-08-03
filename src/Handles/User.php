<?php
namespace Waljqiang\Wechat\Handles;

use Waljqiang\Wechat\Exceptions\WechatException;
use Waljqiang\Wechat\Wechat;

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
		$url = sprintf($this->api["user"]["tag_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",[
			"json" => [
				"tag" => [
					"name" => $tagName
				]
			]
		]);
		$this->wechat->getRedis()->del(self::TAG . $this->wechat->getAppid());
		return $res["tag"]["id"];
	}

	/**
	 * 获取公众号标签
	 *
	 * @return
	 */
	public function getTag(){
		$tagKey = self::TAG . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($tagKey))){
			$url = sprintf($this->api["user"]["tag_get"],$this->wechat->getAccessToken());
			$data = $this->wechat->request($url);
			$res = isset($data["tags"]) ? $data["tags"] : [];
			$this->wechat->getRedis()->setValues($tagKey,$res,Wechat::$common_expire_in);
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
		$url = sprintf($this->api["user"]["tag_of_user_del"],$this->wechat->getAccessToken());
		$res = $this->request($url,"POST",[
			"json" => $buffer
		]);
		
		$keys = [
			self::TAG . $this->wechat->getAppid(),
			self::TAGFANS . $this->wechat->getAppid() . ":" . $tagID
		];
		//删除公众号下标签缓存
		//删除标签下粉丝列表缓存
		$this->wechat->getRedis()->del($keys);
		$keyword = self::USERTAGS . $this->wechat->getAppid() . ":*";
		//删除粉丝下标签缓存
		$this->wechat->getRedis()->vagueDelCommand($keyword);

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
		$tagFansKey = self::TAGFANS . $this->wechat->getAppid() . ":" . $tagID;
		if(!($res = $this->wechat->getRedis()->getValues($tagFansKey))){
			$data = $this->_getTagFans($tagID);
			$res["list"] = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
			while($data["count"] > 0 ){
				$data = $this->_getTagFans($tagID,$data["next_openid"]);
				$openIDs = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
				$res["list"] = array_merge($res["list"],$openIDs);
			}
			$res["total"] = count($res["list"]);
			$this->wechat->getRedis()->setValues($tagFansKey,$res,Wechat::$common_expire_in);
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
		$url = sprintf($this->api["user"]["tag_of_user_set"],$this->wechat->getAccessToken());
		$buffer = [
			"openid_list" => $openIDs,
			"tagid" => $tagID
		];
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		//删除该标签下粉丝列表
		$keys[] = self::TAGFANS . $this->wechat->getAppid() . ":" . $tagID;
		//删除粉丝下标签列表
		foreach ($openIDs as $openID) {
			$keys[] = self::USERTAGS . $this->wechat->getAppid() . ":" . $openID;
		}
		$this->wechat->getRedis()->del($keys);
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
		$url = sprintf($this->api["user"]["tag_of_user_del"],$this->wechat->getAccessToken());
		$buffer = [
			"openid_list" => $openIDs,
			"tagid" => $tagID
		];
		$res = $this->wechat->request($url,"POST",[
			"json" => $buffer
		]);

		//删除该标签下粉丝列表
		$keys[] = self::TAGFANS . $this->wechat->getAppid() . ":" . $tagID;
		//删除粉丝下标签列表
		foreach ($openIDs as $openID) {
			$keys[] = self::USERTAGS . $this->wechat->getAppid() . ":" . $openID;
		}
		$this->wechat->getRedis()->del($keys);
		return true;
	}

	/**
	 * 获取用户标签列表
	 *
	 * @param  string $openID 用户openID
	 * @return
	 */
	public function getUserTags($openID){
		$userTagsKey = self::USERTAGS . $this->wechat->getAppid() . ":" . $openID;
		if(!($res = $this->wechat->getRedis()->getValues($userTagsKey))){
			$buffer = [
				"openid" => $openID
			];
			$url = sprintf($this->api["user"]["tag_of_user_get"],$this->wechat->getAccessToken());
			$res = $this->wechat->request($url,"POST",["json" => $buffer]);
			$res = $res["tagid_list"];
			$this->wechat->getRedis()->setValues($userTagsKey,$res,Wechat::$common_expire_in);

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
			throw new WechatException("USERREMARKINVALID",WechatException::USERREMARKERROR);
		}
		$buffer = [
			"openid" => $openID,
			"remark" => $remark
		];
		$url = sprintf($this->api["user"]["remark_of_user_set"],$this->wechat->getAccessToken());
		$res = $this->wechat->request($url,"POST",["json" => $buffer]);
		$this->wechat->getRedis()->del(self::USERINFO . $this->wechat->getAppid() . ":" . $openID);
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
		$userInfoKey = self::USERINFO . $this->wechat->getAppid() . ":" . $openID;
		if(!($res = $this->wechat->getRedis()->getValues($userInfoKey))){
			$url = sprintf($this->api["user"]["info"],$this->wechat->getAccessToken(),$openID,$lang);
			$res = $this->wechat->request($url);
			$this->wechat->getRedis()->setValues($userInfoKey,$res,Wechat::$common_expire_in);
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
		$userListKey = self::USERLIST . $this->wechat->getAppid();
		if(!($res = $this->wechat->getRedis()->getValues($userListKey))){
			$data = $this->_getUserList();
			$res["total"] = $data["total"];
			$res["list"] = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
			while($data["count"] > 0){
				$data = $this->_getUserList($data["next_openid"]);
				$openIDs = isset($data["data"]["openid"]) ? $data["data"]["openid"] : [];
				$res["list"] = array_merge($res["list"],$openIDs);
			}
			$this->wechat->getRedis()->setValues($userListKey,$res,Wechat::$common_expire_in); 
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
		$url = sprintf($this->api["user"]["tag_fans_get"],$this->wechat->getAccessToken());
		$buffer = empty($openID) ? [ "tagid" => $tagID ] : [ "tagid" => $tagID,"next_openid" => $openID];
		$res = $this->wechat->request($url,"POST",[
			"json" => $buffer
		]);
		return $res;
	}

	private function _getUserList($openID = ""){
		$url = sprintf($this->api["user"]["list"],$this->wechat->getAccessToken(),$openID);
		$res = $this->wechat->request($url);
		return $res;
	}
}