<?php
class sysuser_mdl_shop_fav extends dbeav_model
{
	/*
	*添加店铺收藏
	*@param $shop_id $user_id 
	*@return true or false
	*/
	function addFav($userId=null,$shopId=null)
	{
		$sysMdlShop = app::get('sysshop')->model('shop');
		if(!$userId || !$shopId) return false;
		$filter['user_id'] = $userId;
		$filter['shop_id'] = $shopId;
		if($row = $this->getList('snotify_id',$filter))
			return false;
		$goodsData = $sysMdlShop->getList('shop_name,shop_logo',array('shop_id'=>$shopId));

		$sdf = array(
			'shop_id' =>$shopId,
			'shop_name' =>$goodsData[0]['shop_name'],
			'shop_logo' =>$goodsData[0]['shop_logo'],
			'user_id' =>$userId,
			'create_time' => time(),
		);
		if($this->save($sdf))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	*删除店铺收藏
	*@param $shop_id $user_id 
	*@return true or false
	*/
	function delFav($userId,$shopId)
	{
		$is_delete = false;
		$is_delete = $this->delete(array('shop_id' => $shopId,'user_id' => $userId));
		return $is_delete;
	}

	/*
	*删除所有店铺收藏
	*@param  $user_id 
	*@return true or false
	*/
	function delAllFav($user_id)
	{
		return $this->delete(array('user_id' => $user_id));
	}

	/*
	*统计所有店铺收藏
	*@param  $user_id 
	*@return true or false
	*/
	function getcount($filter)
	{
		return $this->count($filter);
	}


}
