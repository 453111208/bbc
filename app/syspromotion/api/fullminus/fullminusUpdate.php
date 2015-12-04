<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新满减促销数据
 */
final class syspromotion_api_fullminus_fullminusUpdate {

    public $apiDescription = '更新满减促销数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'fullminus_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'满减促销id'],
            'fullminus_name' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'满减促销名称'],
            'fullminus_status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满减促销状态'],
        );


        return $return;
    }

    /**
     *  编辑满减促销
     * @param  array $apiData api数据
     * @return bool
     */
    public function fullminusUpdate($apiData)
    {
        return kernel::single('syspromotion_fullminus')->saveFullminus($apiData);
    }

}

