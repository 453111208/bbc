<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除单条X件Y折促销信息
 */
final class syspromotion_api_xydiscount_xydiscountDelete {

    public $apiDescription = '删除单条X件Y折促销信息';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID必填'],
            'xydiscount_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'X件Y折促销ID必填'],
        );

        return $return;
    }

    /**
     * 根据X件Y折促销ID删除X件Y折促销
     * @param  array $xydiscountId X件Y折促销id
     * @return bool
     */
    public function xydiscountDelete($params)
    {
        return kernel::single('syspromotion_xydiscount')->deleteXydiscount($params);
    }

}

