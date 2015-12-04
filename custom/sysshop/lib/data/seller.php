<?php
class sysshop_data_seller{
    public function getRelShop($params)
    {
        $rows    = $params['rows']    ? $params['rows']    : '*';
        $filter  = $params['filter']  ? $params['filter']  : null;
        $objShopRelSeller = app::get('sysshop')->model('shop_rel_seller');
        $shopRelSeller = $objShopRelSeller->getList($rows,$filter);
        return $shopRelSeller;
    }

    //添加自营用户
    public function saveSelf($selfData)
    {
        if(empty($selfData['shop_id']))
        {
            throw new \LogicException(app::get('sysshop')->_('请先去添加自营企业!'));
        }
        $selfData['license'] = 'on';
        $selfData['seller_type'] = '0';
        shopAuth::checkSignupAccount( trim($selfData['login_account']) );
        shopAuth::checkPassport( $selfData['login_password'],$selfData['psw_confirm'] );
        shopAuth::checkSignup($selfData);
        $selfAccountSeller = $this->__preAccountSeller($selfData);
        $accountShopModel = app::get('sysshop')->model('account');
        $selfUserModel = app::get('sysshop')->model('seller');
        if($selfUserModel->getRow('*',array('shop_id'=>$selfData['shop_id'])))
        {
            throw new \LogicException(app::get('sysshop')->_('该自营企业已在使用！'));
        }
        $db = app::get('sysshop')->database();
        $db->beginTransaction();
        try
        {
            $sellerId = $accountShopModel->insert($selfAccountSeller);
            if( !$sellerId )
            {
                throw new \LogicException(app::get('sysshop')->_('添加失败'));
            }

            $sellerData = $this->__preSeller($sellerId, $selfData);

            if( !$selfUserModel->insert($sellerData) )
            {
                throw new \LogicException(app::get('sysshop')->_('添加失败'));
            }
            $db->commit();

        }
        catch(\Excessive $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    private function __preAccountSeller($data)
    {
        $pamShopData['login_account'] = trim($data['login_account']);
        $pamShopData['createtime'] = $data['createtime'] ? $data['createtime'] : time();
        $pamShopData['modified_time'] = $data['modified_time'] ? $data['modified_time'] : time();

        $loginPassword = pam_encrypt::make(trim($data['login_password']));
        $pamShopData['login_password'] = $loginPassword;

        return $pamShopData;
    }

    private function __preSeller($sellerId, $data)
    {
        $sellerData['seller_id'] = intval($sellerId);
        $sellerData['seller_type'] = !empty($data['seller_type']) ? $data['seller_type'] : '0';
        $sellerData['shop_id'] = $data['shop_id'];
        $sellerData['name'] = $data['name'];
        $sellerData['mobile'] = $data['mobile'];
        $sellerData['email'] = $data['email'];
        $sellerData['modified_time'] = time();
        return $sellerData;
    }
}
