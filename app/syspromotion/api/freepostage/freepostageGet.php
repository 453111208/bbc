<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条免邮数据
 */
final class syspromotion_api_freepostage_freepostageGet {

    public $apiDescription = '获取单条免邮数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'freepostage_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'免邮id'],
            'freepostage_itemList' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'免邮的商品'],
        );

        return $return;
    }

    /**
     *  获取单条免邮信息
     * @param  array $params 筛选条件数组
     * @return array         返回一条免邮信息
     */
    public function freepostageGet($params)
    {
        $freepostageInfo = kernel::single('syspromotion_freepostage')->getFreepostage($params['freepostage_id']);
        $freepostageInfo['valid'] = $this->__checkValid($freepostageInfo);
        if($params['freepostage_itemList'])
        {
            $freepostageItems = kernel::single('syspromotion_freepostage')->getFreepostageItems($params['freepostage_id']);
            $freepostageInfo['itemsList'] = $freepostageItems;
        }
        return $freepostageInfo;
    }

    // 检查当前免邮是否可用
    private function __checkValid(&$freepostageInfo)
    {
        $now = time();
        if( ($freepostageInfo['freepostage_status']=='agree') && ($freepostageInfo['start_time']>$now) && ($freepostageInfo['end_time']>$now) )
        {
            return true;
        }
        return false;
    }

}

