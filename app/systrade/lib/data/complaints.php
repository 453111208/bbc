<?php

class systrade_data_complaints {

    public function __construct()
    {
        $this->objMdlComplaints = app::get('systrade')->model('order_complaints');
        $this->objMdlOrder = app::get('systrade')->model('order');
    }

    /**
     * 买家发起订单投诉
     */
    public function create($data, $userId)
    {
        $oid = $data['oid'];
        $orderData = $this->objMdlOrder->getRow('oid,tid,shop_id,status,aftersales_status,complaints_status,user_id',array('oid'=>$oid));

        $this->__check($data, $orderData, $userId);

        $savedata['tid'] = $orderData['tid'];
        $savedata['oid'] = $data['oid'];
        $savedata['tel'] = $data['tel'];
        $savedata['image_url'] = $data['image_url'];
        $savedata['complaints_type'] = $data['complaints_type'];
        $savedata['content'] = trim($data['content']);
        $savedata['user_id'] = $userId;
        $savedata['shop_id'] = $orderData['shop_id'];
        $savedata['created_time'] = time();

        $db = app::get('systrade')->database();
        $db->beginTransaction();

        try
        {
            if( !$this->objMdlComplaints->save($savedata) || !$this->objMdlOrder->update(['complaints_status'=>'WAIT_SYS_AGREE'], ['oid'=>$data['oid']]) )
            {
                $db->rollback();
                throw new \LogicException('保存失败');
            }
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        $db->commit();
        return true;
    }

    private function __check($data, $orderData, $userId)
    {
        //订单数据不存在， 或者当前会员不是下单的会员
        if( !$orderData || $orderData['user_id'] != $userId )
        {
            throw new \LogicException('权限不足');
        }

        if( $orderData['aftersales_status'] != 'SELLER_REFUSE_BUYER')
        {
            throw new \LogicException('权限不足');
        }

        //一个子订单号只能进行一次投诉
        if( $orderData['complaints_status'] != 'NOT_COMPLAINTS' )
        {
            throw new \LogicException('该订单已投诉');
        }

        //投诉图片凭证，最多上传五个
        if( $data['image_url'] )
        {
            $imageUrl = explode(',', $data['image_url']);
            if( count($imageUrl) > 5 )
            {
                throw new \LogicException('最多上传5张');
            }
        }

        return true;
    }

    /**
     * 平台处理订单投诉
     */
    public function process($data, $complaintsId)
    {
        $complaintsData = $this->objMdlComplaints->getRow('status,complaints_id,oid', ['complaints_id'=>$complaintsId]);
        if( !$complaintsData || $complaintsData['status'] != 'WAIT_SYS_AGREE' )
        {
            throw new \LogicException('投诉已处理，不需要重复处理');
        }

        $db = app::get('systrade')->database();
        $db->beginTransaction();

        try
        {
            //状态更新到订单投诉表
            $updataComplaints = $this->objMdlComplaints->update($data,['complaints_id'=>$complaintsId]);

            //投诉状态更新到订单表
            $updateOrder = $this->objMdlOrder->update(['complaints_status'=>$data['status']], ['oid'=>$complaintsData['oid']]);
            if( !$updataComplaints || !$updateOrder )
            {
                $db->rollback();
                throw new \LogicException('保存失败');
            }
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        $db->commit();
        return true;
    }

    /**
     * 买家撤销订单投诉
     *
     * @param $complaintsId 订单投诉ID
     * @param $userId 撤销订单投诉会员ID
     * @param $buyerCloseReasons 买家撤销原因
     */
    public function buyerClose($complaintsId, $userId, $buyerCloseReasons)
    {
        $complaintsData = $this->objMdlComplaints->getRow('status,complaints_id,oid', ['complaints_id'=>$complaintsId,'user_id'=>$userId]);
        if( empty($complaintsData) )
        {
            throw new \LogicException('没有操作权限');
        }

        if( $complaintsData['status'] != 'WAIT_SYS_AGREE' )
        {
            throw new \LogicException('投诉已处理，不能撤销');
        }

        $db = app::get('systrade')->database();
        $db->beginTransaction();
        try
        {
            //状态更新到订单投诉表
            $updataComplaints = $this->objMdlComplaints->update(['buyer_close_reasons'=>$buyerCloseReasons,'status'=>'BUYER_CLOSED'],['complaints_id'=>$complaintsId]);

            //投诉状态更新到订单表
            $updateOrder = $this->objMdlOrder->update(['complaints_status'=>'BUYER_CLOSED'], ['oid'=>$complaintsData['oid']]);
            if( !$updataComplaints || !$updateOrder )
            {
                $db->rollback();
                throw new \LogicException('保存失败');
            }
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        $db->commit();
        return true;

    }

    /**
     * 根据子订单号获取订单投诉详情
     *
     * @param $oid  子订单号
     * @param $userId  会员ID
     * @param $fields 订单投诉需要返回的字段
     * @param $ordersFields 需要返回子订单数据的字段
     */
    public function getInfo($oid, $userId, $fields, $ordersFields=null)
    {
        $complaintsData = $this->objMdlComplaints->getRow($fields, ['oid'=>$oid,'user_id'=>$userId]);

        if( $ordersFields && $complaintsData )
        {
            $complaintsData['orders'] = $this->objMdlOrder->getRow($ordersFields, ['oid'=>$oid]);
        }

        return $complaintsData;
    }

}
