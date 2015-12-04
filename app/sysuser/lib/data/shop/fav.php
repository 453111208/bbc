<?php
class sysuser_data_shop_fav
{

	/**
	 * 添加店铺收藏
	 * @param string user id
	 * @param string  shop_id
	 * 
	 * @return boolean true or false
	 */
	public function addFav($userId,$shopId=null)
	{
		if(!$shopId || !$userId) return false;

		$objMdlShopFav = app::get('sysuser')->model('shop_fav');

		return $objMdlShopFav->addFav($userId,$shopId);
	}

	/**
	 * 获取当前页的收藏内容
	 * @param array $filter
	 * @return array data
	 */
	public function getFav($filter)
	{
		$objMdlShopFav = app::get('sysuser')->model('shop_fav');
		$rows       = '*';
		$rowsFilter = $filter['filter']  ? $filter['filter']  : null;
		$start      = $filter['start']   ? $filter['start']   : 0;
		$limit      = $filter['limit']   ? $filter['limit']   : -1;
		$orderBy    = $filter['orderBy'] ? $filter['orderBy'] : 'snotify_id DESC';
		$aData = $objMdlShopFav->getList($rows, $rowsFilter, $start, $limit, $orderBy);
		return $aData;
	}

	/**
	 * 删除当前页的收藏内容
	 * @param string user_id
	 * @param string shop_id
	 * @return true or false
	 */
	public function delFav($userId,$shopId=null)
	{
		if (!$userId ) return false;

		$objMdlShopFav = app::get('sysuser')->model('shop_fav');

		if (is_null($shopId))
		{
			return $objMdlShopFav->delAllFav($userId);
		}
		else
		{
			return $objMdlShopFav->delFav($userId,$shopId);
		}
	}

	public function countFav($userId)
	{
		
		if (!$userId) return false;
		$objMdlShopFav = app::get('sysuser')->model('shop_fav');
		$filter = array('user_id'=>$userId);

		return $objMdlShopFav->getcount($filter);
	}

}
?>
