<?php
class sysuser_data_user_addrs
{
	public $addrLimit = 20;

	/**
	*会员收货地址保存
	* @param $data
	* @return true or false
	*/
	public function saveAddrs($data)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		$userId = $data['user_id'];

		return $userMdlAddr->saveAddrs($data);
	}

	/**
	*会员收货地址获取
	* @param $userId
	* @return array $userAddrs
	*/
	public function getListUserAddrs($userId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId ) return false;

		$userAddrs = $userMdlAddr->getList('*',array('user_id'=>$userId));
		return $userAddrs;
	}
	/**
	*会员总收货地址
	* @param $userId
	* @return array $useraddrcount
	*/
	public function getAddrCount($userId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId ) return false;
		$Count = $userMdlAddr->count(array('user_id'=>$userId));
		$userAddrCount = array(
			'nowcount'=>$Count,
			'maxcount'=>$this->addrLimit
		);
		return $userAddrCount;
	}

	/**
	*编辑会员收货地址
	* @param $userId
	* @return array $useraddrcount
	*/
	public function getAddrInfo($userId,$addrId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId || !$addrId) return false;
		$addrInfo = $userMdlAddr->getRow('*',array('user_id'=>$userId,'addr_id'=>$addrId));

		return $addrInfo;
	}

	/**
	*会员默认收货地址
	* @param $userId
	* @return array $useraddrcount
	*/
	public function getDefAddr($userId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId ) return false;
		$defAddr = $userMdlAddr->getRow('*',array('user_id'=>$userId,'def_addr'=>1));

		return $defAddr;
	}

	/**
	*默认收货地址设置
	* @param $arrPost
	* @param $userId
	* @return true or false
	*/

	public function setDefAddr($userId,$addrId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId || !$addrId) return false;
		$addInfo = $this->getAddrInfo($userId,$addrId);
		$addInfo['def_addr'] = 1;
		return $userMdlAddr->saveAddrs($addInfo);
	}

	/**
	*删除收货地址
	* @param $addr_id
	* @param $userId
	* @return true or false
	*/

	public function delAddr($userId,$addrId)
	{
		$userMdlAddr = app::get('sysuser')->model('user_addrs');
		if( !$userId || !$addrId) return false;
		$filter = array(
			'user_id'=>$userId,
			'addr_id'=>$addrId,
		);
		return $userMdlAddr->delete($filter);
	}

}
