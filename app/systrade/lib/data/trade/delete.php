<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class systrade_data_trade_delete{

    public function generate($tids)
    {
        if(!$tids)
        {
            $msg = "删除交易记录失败，缺少条件参数";
            throw new \LogicException($msg);
            return false;
        }

        $params['rows'] = "tid,status";
        $params['filter'] = array(
            'tid' => $tids,
            'status'=>array('TRADE_FINISHED','TRADE_CLOSED','TRADE_CLOSED_BY_SYSTEM'),
        );

        

        $objLibTrade = kernel::single('systrade_data_trade');
        $tradeList = $objLibTrade->getTradeList($params);
        if(!$tradeList)
        {
            throw new \LogicException(sprintf("删除交易记录失败，订单‘%s’处理中，不可删除", implode(',',$tids)));
        }

        foreach($tradeList as $key=>$value)
        {
            $canDel[] = $value['tid'];
            foreach($value['order'] as $val)
            {
                $order[] = $val['oid'];
            }
        }

        $notDel = array_diff($tids,$canDel);
        if($notDel)
        {
            $notdel = implode(',',$notDel);
            $db->rollback();
            $msg = "删除交易记录失败，订单‘".$notdel."’处理中，不可删除";
            throw new \LogicException($msg);
            return false;
        }

        $objMdlTrade = app::get('systrade')->model('trade');
        
        $db = app::get('systrade')->database();
        $db->beginTransaction();
        try
        {
            $delTrade = $objMdlTrade->delete(array('tid'=>$canDel));
            if(!$objMdlTrade->delete(array('tid'=>$canDel)))
            {
                throw new \LogicException("删除交易记录失败，主订单表出错");
            }

            if(!$objMdlTrade->delete(array('tid'=>$canDel,'oid'=>$order)))
            {
                throw new \LogicException("删除交易记录失败，子订单表出错");
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        
        return true;
    }
}
