<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新商品的关联促销信息
 * item.promotiontag.update
 */
class sysitem_api_promotion_itemPromotionTagAdd {

    /**
     * 接口作用说明,购物车内促销，购物车内促销是排斥的
     */
    public $apiDescription = '更新商品的关联促销信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'item_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'商品ID'],
            'promotion_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'促销id'],
        );

        return $return;
    }

    /*
    更新促销的关联id
    @params  单个促销的传入参数数组，例如满减 array(
                'item_id' => $item_id,
                'promotion_id' => $promotion_id,
            )
    */
    public function itemPromotionTagAdd($params)
    {
        $objMdlItemPromotionTag = app::get('sysitem')->model('item_promotion');
        $data = array(
            'item_id'=>$params['item_id'],
            'promotion_id'=>$params['promotion_id'],
        );
        if(!$objMdlItemPromotionTag->save($data))
        {
            return false;
        }
        return true;
    }
}

