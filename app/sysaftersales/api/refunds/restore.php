<?php
class sysaftersales_api_refunds_restore{

    /**
     * 接口作用说明
     */
    public $apiDescription = '平台对退款申请进行退款处理';

    public function getParams()
    {
        $return['params'] = array(
            'aftersales_bn' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的编号'],
            'refunds_id' => ['type'=>'string','valid'=>'required', 'description'=>'退款申请单编号'],
            'refundsData' => ['type'=>'json','valid'=>'required', 'description'=>'退款单内容'],
        );
        return $return;
    }

    public function restore($params)
    {

        $filter['refunds_id'] = $params['refunds_id'];
        $objMdlRefunds = app::get('sysaftersales')->model('refunds');
        $refunds = $objMdlRefunds->getRow('status,aftersales_bn,user_id,shop_id,tid,oid',$filter);
        if($refunds['aftersales_bn'] != $params['aftersales_bn'])
        {
            throw new \LogicException(app::get('sysaftersales')->_('数据有误，请重新处理'));
            return false;
        }

        if($refunds['status'] > 0)
        {
            throw new \LogicException(app::get('sysaftersales')->_('当前申请已被处理，不能在处理'));
            return false;
        }


        try{
            $refundsData = json_decode($params['refundsData'],true);
            $refund_fee = $refundsData['money'];
            $refundsData['aftersales_bn'] = $params['aftersales_bn'];
            unset($params['refundsData']);
            app::get('sysaftersales')->rpcCall('refund.create',$refundsData);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException(app::get('sysaftersales')->_($e->getMessage()));
            return false;
        }

        $db = app::get('sysaftersales')->database();
        $db->beginTransaction();

        try
        {
            $params['status'] ="1";
            $result = $objMdlRefunds->update($params,$filter);
            if(!$result)
            {
                throw new \LogicException(app::get('sysaftersales')->_('退款申请单更新失败'));
            }

            $objMdlAftersales = app::get('sysaftersales')->model('aftersales');
            $aftersales = $objMdlAftersales->getRow('progress,status,tid,oid,user_id,shop_id',array('aftersales_bn'=>$refunds['aftersales_bn']));
            if($aftersales['tid'] != $refunds['tid'] || $aftersales['oid'] != $refunds['oid'] || $aftersales['user_id'] != $refunds['user_id'] || $aftersales['shop_id'] != $refunds['shop_id'])
            {
                throw new \LogicException(app::get('sysaftersales')->_('数据有误，请重新处理'));
            }

            if(in_array($aftersales['progress'],['3','4','6','7']) || in_array($aftersales['status'],['2','3']))
            {
                throw new \LogicException(app::get('sysaftersales')->_('当前处理异常，无法处理'));
            }

            $afterparams['progress'] = '7';
            $afterparams['status'] = '2';
            $afterFilter['aftersales_bn'] = $refunds['aftersales_bn'];
            $result = $objMdlAftersales->update($afterparams,$afterFilter);
            if(!$result)
            {
                throw new \LogicException(app::get('sysaftersales')->_('售后单状态更新失败'));
            }

            try
            {
                $orderparams['oid'] = $refunds['oid'];
                $orderparams['tid'] = $refunds['tid'];
                $orderparams['user_id'] = $refunds['user_id'];
                $orderparams['aftersales_status'] = 'SUCCESS';
                $orderparams['refund_fee'] = $refund_fee;
                app::get('sysaftersales')->rpcCall('order.aftersales.status.update', $orderparams);
            }
            catch(\Exception $e)
            {
                throw new \LogicException(app::get('sysaftersales')->_($e->getMessage()));
            }
            $db->commit();
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }
}


