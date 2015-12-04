<?php

class sysshop_api_account_sellerUpdate {

    public $apiDescription = "修改子帐号信息";

    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'required','description'=>'角色id','default'=>'','example'=>'1'],
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>'1'],
            'role_id' => ['type'=>'string','valid'=>'required','description'=>'子帐号绑定角色ID','default'=>'','example'=>'1'],
            'name' => ['type'=>'string','valid'=>'required','description'=>'姓名','default'=>'','example'=>'李二'],
            'mobile' => ['type'=>'string','valid'=>'required','description'=>'手机号','default'=>'','example'=>'13918765432'],
            'email' => ['type'=>'string','valid'=>'required','description'=>'邮箱','default'=>'','example'=>'example@shopex.cn'],
        );

        return $return;
    }

    public function update($params)
    {

        if( !$params['role_id'] || $params['role_id'] == '0' )
        {
            throw new \LogicException('请选择角色');
        }

        $objMdlSeller = app::get('sysshop')->model('seller');
        $mobileData = $objMdlSeller->getRow('mobile,seller_id',['mobile'=>$params['mobile']]);
        if( $mobileData['mobile'] && $mobileData['seller_id'] != $params['seller_id']  )
        {
            throw new \LogicException('联系手机已被使用，请重新更换一个');
        }

        $emailData = $objMdlSeller->getRow('email,seller_id',['email'=>$params['email']]);
        if( $emailData['email'] && $emailData['seller_id'] != $params['seller_id']  )
        {
            throw new \LogicException('联系邮箱已被使用，请重新更换一个');
        }

        $filter = [
            'seller_id'=>$params['seller_id'],
            'seller_type'=>'1',
            'shop_id'=>$params['shop_id']
        ];

        $data = [
            'role_id'=>$params['role_id'],
            'name'=>$params['name'],
            'mobile'=>$params['mobile'],
            'email'=>$params['email'],
            'modified_time'=>time(),
        ];

        $objMdlSeller->update($data, $filter);

        return true;
    }
}

