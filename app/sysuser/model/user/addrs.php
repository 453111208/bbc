<?php
class sysuser_mdl_user_addrs extends dbeav_model
{
	public $addrLimit = 20;

	/**
	*会员收货地址保存
	* @param $data
	* @return true or false
	*/
	public function saveAddrs($data)
	{
		$filter = array('user_id' => $data['user_id']);
		if($data['def_addr'])
		{
			$arrUpdate = array('def_addr'=>0);
			$this->update($arrUpdate, $filter);
		}

		$cnt = $this->count($filter);

		if((!$data['addr_id'] && $cnt < $this->addrLimit) || $data['addr_id'])
		{

			return $this->save($data);
		}
		else
		{
			throw new \LogicException(app::get('sysuser')->_('最多只能添加20个地址，请先删除一条地址之后再添加'));
			return false;
		}

	}
}
