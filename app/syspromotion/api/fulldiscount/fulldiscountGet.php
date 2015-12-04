<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条满折促销数据
 */
final class syspromotion_api_fulldiscount_fulldiscountGet {

    public $apiDescription = '获取单条满折促销数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销id'],
            'fulldiscount_itemList' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销的商品'],
        );

        return $return;
    }

    /**
     *  获取单条满折促销信息
     * @param  array $params 筛选条件数组
     * @return array         返回一条满折促销信息
     */
    public function fulldiscountGet($params)
    {
        $fulldiscountInfo = kernel::single('syspromotion_fulldiscount')->getFulldiscount($params['fulldiscount_id']);
        $fulldiscountInfo['valid'] = $this->__checkValid($fulldiscountInfo);
        if($params['fulldiscount_itemList'])
        {
            $fulldiscountItems = kernel::single('syspromotion_fulldiscount')->getFulldiscountItems($params['fulldiscount_id']);
            $fulldiscountInfo['itemsList'] = $fulldiscountItems;
        }

        return $fulldiscountInfo;
    }

    // 检查满折是否可用
    private function __checkValid(&$fulldiscountInfo)
    {
        $now = time();
        if( ($fulldiscountInfo['fulldiscount_status']=='agree') && ($fulldiscountInfo['start_time']>$now) && ($fulldiscountInfo['end_time']>$now) )
        {
            return true;
        }
        return false;
    }

}

