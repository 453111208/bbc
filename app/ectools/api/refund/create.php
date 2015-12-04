<?php
class ectools_api_refund_create{

    public $apiDescription = '创建退款单';
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的主订单编号', 'default'=>'', 'example'=>''],
            'oid' => ['type'=>'string','valid'=>'required', 'description'=>'申请售后的子订单编号', 'default'=>'', 'example'=>''],
            'money' => ['type'=>'json','valid'=>'required', 'description'=>'退款金额', 'default'=>'', 'example'=>''],
            'refund_bank' => ['type'=>'json','valid'=>'required', 'description'=>'退款银行', 'default'=>'', 'example'=>''],
            'refund_account' => ['type'=>'json','valid'=>'required', 'description'=>'退款账号', 'default'=>'', 'example'=>''],
            'refund_people' => ['type'=>'json','valid'=>'required', 'description'=>'退款操作人', 'default'=>'', 'example'=>''],
            'receive_bank' => ['type'=>'json','valid'=>'required', 'description'=>'收款银行', 'default'=>'', 'example'=>''],
            'receive_account' => ['type'=>'json','valid'=>'required', 'description'=>'收款账号', 'default'=>'', 'example'=>''],
            'beneficiary' => ['type'=>'json','valid'=>'required', 'description'=>'收款人', 'default'=>'', 'example'=>''],
            'aftersales_bn' => ['type'=>'json','valid'=>'required', 'description'=>'售后单编号', 'default'=>'', 'example'=>''],
        );
        return $return;
    }

    public function create($params)
    {
        $db = app::get('ectools')->database();
        $db->beginTransaction();
        try
        {
            $objRefund = kernel::single('ectools_data_refunds');
            $result = $objRefund->create($params);
        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw new \LogicException(app::get('ectools')->_($e->getMessage()));
            return false;
        }
        $db->commit();
        return true;
    }
}
