<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 添加满折促销数据
 */
final class syspromotion_api_fulldiscount_fulldiscountAdd {

    public $apiDescription = '添加满折促销数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销id'],
            'fulldiscount_name' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销名称'],
            'fulldiscount_status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销状态'],
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认created_time asc'],
        );

        return $return;
    }

    /**
     *  添加满折促销数据
     * @param  array $apiData 满折促销各种值
     * @return
     */
    public function fulldiscountAdd($apiData)
    {
        return kernel::single('syspromotion_fulldiscount')->saveFulldiscount($apiData);
    }

}

