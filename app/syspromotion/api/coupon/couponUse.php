<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 使用优惠券促销
 * promotion.coupon.get
 */
final class syspromotion_api_coupon_couponUse {

    public $apiDescription = '使用优惠券促销';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_code' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠券编码'],
            'mode' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'购物模式'],
            'platform' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'平台'],
        );

        return $return;
    }

    /**
     *  使用优惠券促销，看能否使用
     * @param  array $params 筛选条件数组
     * @return array         返回应用信息
     */
    public function couponUse($params)
    {
        $filter['user_id'] = $params['oauth']['account_id'];
        $filter['coupon_code'] = $params['coupon_code'];
        $filter['mode'] = $params['mode'];
        $filter['platform'] = in_array($params['platform'], array('pc', 'wap')) ? $params['platform'] : 'pc';

        return kernel::single('syspromotion_solutions_coupon')->useCoupon($filter);
    }

}

