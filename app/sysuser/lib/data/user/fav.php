<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysuser_data_user_fav
{
    /**
     * 添加收藏
     * @param string user id
     * @param string object type
     * @param sttring goods id 不能为空
     * @return boolean true or false
     */
    public function addFav($userId,$objectType='goods',$nGid=null)
    {
        if(!$nGid || !$userId) return false;
        $objMdlFav = app::get('sysuser')->model('user_fav');
       
        return $objMdlFav->addFav($userId,$objectType,$nGid);
    }

    /**
     * 获取当前页的收藏内容
     * 
     * @param array $filter
     *
     * @return array data
     */
    public function getFav($filter)
    {   
        $objMdlFav = app::get('sysuser')->model('user_fav');
        $rows       = '*';
        $rowsFilter = $filter['filter']  ? $filter['filter']  : null;
        $start      = $filter['start']   ? $filter['start']   : 0;
        $limit      = $filter['limit']   ? $filter['limit']   : -1;
        $orderBy    = $filter['orderBy'] ? $filter['orderBy'] : 'gnotify_id DESC';
        
        $aData = $objMdlFav->getList($rows, $rowsFilter, $start, $limit, $orderBy);

        return $aData;
    }
    /**
     * 删除当前页的收藏内容
     * @param string user_id
     * @param string nGid
     * 
     * @return true or false
     */
    public function delFav($userId,$nGid=null)
    {
        if (!$userId) return false;

        $objMdlFav = app::get('sysuser')->model('user_fav');

        if (is_null($nGid))
        {
            return $objMdlFav->delAllFav($userId);
        }
        else
        {
            return $objMdlFav->delFav($userId,$nGid);
        }
    }

    public function countFav($userId)
    {
        if (!$userId) return false;
        $objMdlFav = app::get('sysuser')->model('user_fav');
        $filter = array('user_id'=>$userId);
        return $objMdlFav->getcount($filter);
    }
}
