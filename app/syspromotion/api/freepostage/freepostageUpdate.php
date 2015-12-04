<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新免邮信息
 */
final class syspromotion_api_freepostage_freepostageUpdate {

    public $apiDescription = '更新免邮信息';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'integer', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'integer', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'freepostage_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'免邮id'],
            'condition_type' => ['type'=>'string', 'valid'=>'in:money,quantity', 'default'=>'', 'example'=>'money或者quantity', 'description'=>'免邮规则类型'],
            'limit_money' => ['type'=>'string', 'valid'=>'required_if:condition_type,money', 'description'=>'按金额', 'default'=>'', 'example'=>''],
            'limit_quantity' => ['type'=>'string', 'valid'=>'required_if:condition_type,quantity|integer', 'description'=>'按数量', 'default'=>'', 'example'=>''],
        );

        return $return;
    }

    /**
     *  更新免邮信息
     * @param  array $apiData api数据
     * @return bool
     */
    public function freepostageUpdate($apiData)
    {
        return kernel::single('syspromotion_freepostage')->saveFreepostage($apiData);
    }

}

