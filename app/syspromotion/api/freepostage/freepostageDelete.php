<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除单条免邮信息
 */
final class syspromotion_api_freepostage_freepostageDelete {

    public $apiDescription = '删除单条免邮信息';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID必填'],
            'freepostage_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'免邮ID必填'],
        );

        return $return;
    }

    /**
     * 根据免邮ID删除免邮
     * @param  array $freepostageId 免邮id
     * @return bool
     */
    public function freepostageDelete($params)
    {
        return kernel::single('syspromotion_freepostage')->deleteFreepostage($params);
    }

}

