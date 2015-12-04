<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取商品的促销标签，及促销的主键promotion_id
 * item.promotiontag.get
 */
class sysitem_api_itemPromotionTagGet {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单个商品的促销信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'item_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'商品ID'],
        );

        return $return;
    }

    /**
     * 获取单个商品的促销信息
     */
    public function itemPromotionTagGet($params)
    {
        $objMdlItemTagPromotion = app::get('sysitem')->model('item_tag_promotion');
        if($result = $objMdlItemTagPromotion->getRow('*', array('item_id'=>$params['item_id'])))
        {
            return $result;
        }
        return false;
    }
}

