<?php

class sysshop_api_account_sellerDel {

    public $apiDescription = "删除指定子帐号";

    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'required','description'=>'角色id','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
        );

        return $return;
    }

    public function delete($params)
    {
        $objMdlSeller = app::get('sysshop')->model('seller');
        $objMdlPamUser = app::get('sysshop')->model('account');

        $db = app::get('sysshop')->database();
        $db->beginTransaction();

        $result = $objMdlSeller->delete(array('seller_id'=>$params['seller_id'],'shop_id'=>$params['shop_id'],'seller_type'=>'1'));
        if(!$result)
        {
            $msg = "删除失败";
            throw new \logicException($msg);
        }


        $result = $objMdlPamUser->delete(array('seller_id'=>$params['seller_id']));
        if(!$result)
        {
            $db->rollback();
            $msg = "删除失败";
            throw new \logicException($msg);
        }

        $db->commit();
        return true;
    }
}

