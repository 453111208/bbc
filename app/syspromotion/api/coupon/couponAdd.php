<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 添加优惠券数据
 */
final class syspromotion_api_coupon_couponAdd {

    public $apiDescription = '添加优惠券数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'description'=>'会员ID,user_id和shop_id必填一个', 'default'=>'', 'example'=>''],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个', 'default'=>'', 'example'=>''],
            'coupon_id' => ['type'=>'int', 'valid'=>'', 'description'=>'优惠券id', 'default'=>'', 'example'=>''],
            'coupon_name' => ['type'=>'string', 'valid'=>'', 'description'=>'优惠券名称', 'default'=>'', 'example'=>''],
            'coupon_status' => ['type'=>'int', 'valid'=>'', 'description'=>'优惠券状态', 'default'=>'', 'example'=>''],
            'page_no' => ['type'=>'int', 'valid'=>'', 'description'=>'分页当前页数,默认为1', 'default'=>'', 'example'=>''],
            'page_size' => ['type'=>'int', 'valid'=>'', 'description'=>'每页数据条数,默认10条', 'default'=>'', 'example'=>''],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'description'=>'排序，默认created_time asc', 'default'=>'', 'example'=>''],
        );

        return $return;
    }

    /**
     *  添加优惠券数据
     * @param  array $apiData 优惠券各种值
     * @return array         返回一条优惠券信息
     */
    public function couponAdd($apiData)
    {
        return kernel::single('syspromotion_coupon')->saveCoupon($apiData);
    }

}

