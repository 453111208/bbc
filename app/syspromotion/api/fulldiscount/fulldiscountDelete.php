<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除单条满折促销信息
 */
final class syspromotion_api_fulldiscount_fulldiscountDelete {

    public $apiDescription = '删除单条满折促销信息';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID必填'],
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'满折促销ID必填'],
        );

        return $return;
    }

    /**
     * 根据满折促销ID删除满折促销
     * @param  array $fulldiscountId 满折促销id
     * @return bool
     */
    public function fulldiscountDelete($params)
    {
        return kernel::single('syspromotion_fulldiscount')->deleteFulldiscount($params);
    }

}

