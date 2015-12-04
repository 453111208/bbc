<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除单条优惠券信息
 */
final class sysuser_api_couponDelete {

    public $apiDescription = '删除会员单条优惠券';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_code' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券CODE必填'],
        );

        return $return;
    }

    /**
     * 根据优惠券ID删除优惠券
     * @param  array $couponId 优惠券id
     * @return bool
     */
    public function couponDelete($params)
    {
        $filter = array('coupon_code'=>$params['coupon_code'], 'user_id'=>$params['oauth']['account_id']);
        return app::get('sysuser')->model('user_coupon')->delete($filter);
    }

}

