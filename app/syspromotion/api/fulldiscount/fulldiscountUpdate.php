<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新满折促销数据
 */
final class syspromotion_api_fulldiscount_fulldiscountUpdate {

    public $apiDescription = '更新满折促销数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销id'],
        );

        return $return;
    }

    /**
     *  编辑满折促销
     * @param  array $apiData api数据
     * @return bool
     */
    public function fulldiscountUpdate($apiData)
    {
        return kernel::single('syspromotion_fulldiscount')->saveFulldiscount($apiData);
    }

}

