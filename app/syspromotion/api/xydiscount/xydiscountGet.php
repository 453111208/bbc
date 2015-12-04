<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条X件Y折促销数据
 */
final class syspromotion_api_xydiscount_xydiscountGet {

    public $apiDescription = '获取单条X件Y折促销数据';

    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'xydiscount_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'X件Y折促销id'],
            'xydiscount_itemList' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'X件Y折促销的商品'],
        );

        return $return;
    }

    /**
     *  获取单条X件Y折促销信息
     * @param  array $params 筛选条件数组
     * @return array         返回一条X件Y折促销信息
     */
    public function xydiscountGet($params)
    {
        $xydiscountInfo = kernel::single('syspromotion_xydiscount')->getXydiscount($params['xydiscount_id']);
        $xydiscountInfo['valid'] = $this->__checkValid($xydiscountInfo);
        if($params['xydiscount_itemList'])
        {
            $xydiscountItems = kernel::single('syspromotion_xydiscount')->getXydiscountItems($params['xydiscount_id']);
            $xydiscountInfo['itemsList'] = $xydiscountItems;
        }

        return $xydiscountInfo;
    }

    // 检查X件Y折是否可用
    private function __checkValid(&$xydiscountInfo)
    {
        $now = time();
        if( ($xydiscountInfo['xydiscount_status']=='agree') && ($xydiscountInfo['start_time']>$now) && ($xydiscountInfo['end_time']>$now) )
        {
            return true;
        }
        return false;
    }

}

