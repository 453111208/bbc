<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 应用优惠券促销
 * promotion.coupon.get
 */
final class syspromotion_api_coupon_couponApply {


    public $apiDescription = '应用优惠券促销';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_code' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'促销表id'],
            'cartItemsInfo' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺有效的item_id，totalprice信息'],
        );

        return $return;
    }

    /**
     *  应用优惠券促销
     * @param  array $params 筛选条件数组
     * @return array         返回应用信息
     */
    public function couponApply($params)
    {
        $data = array(
            'coupon_code' => $params['coupon_code'],
            'cartItemsInfo' => $params['cartItemsInfo'],
            'user_id' => $params['oauth']['account_id'],
        );
        $info = kernel::single('syspromotion_solutions_coupon')->apply($data);

        return $info;
    }

}

