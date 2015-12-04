<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_delete {

    /**
     * 接口作用说明
     */
    public $apiDescription = '用户删除评价';

    public function getParams()
    {
        $return['params'] = array(
            'rate_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户要删除的评价ID'],
        );

        return $return;
    }

    public function del($params)
    {
        if( empty($params['rate_id']) ) return false;
        $userId = $params['oauth']['account_id'];

        $objMdlTraderate = app::get('sysrate')->model('traderate');
        $rateData = $objMdlTraderate->getList('rate_id,user_id,anony,oid,result,item_id',array('rate_id'=>$params['rate_id']));
        if( empty($rateData) )
        {
            throw new \LogicException(app::get('sysrate')->_('要删除的评价不存在'));
        }

        foreach( (array)$rateData as $row )
        {
            if( $row['user_id'] != $userId )
            {
                throw new \LogicException(app::get('sysrate')->_('无操作权限,可能已退出登录，请重新登录'));
            }

            $rateId[] = $row['rate_id'];

            if( $row['result'] == 'good' )
            {
                $filter['rate_good_count'] = -1;
            }
            elseif( $row['result'] == 'bad' )
            {
                $filter['rate_bad_count'] = -1;
            }
            else
            {
                $filter['rate_neutral_count'] = -1;
            }

            $filter['item_id'] = $row['item_id'];
        }

        $db = app::get('sysrate')->database();
        $db->beginTransaction();
        try
        {
            if( !app::get('sysrate')->rpcCall('item.updateRateQuantity', $filter,'buyer') )
            {
                $db->rollback();
                throw new \LogicException(app::get('sysrate')->_('该商品已被移除，不能评论！'));
            }

            if( !$objMdlTraderate->update(['disabled'=>1],['rate_id'=>$rateId]) )
            {
                $db->rollback();
                throw new \LogicException(app::get('sysrate')->_('删除失败'));
            }

            $db->commit();
        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }
}

