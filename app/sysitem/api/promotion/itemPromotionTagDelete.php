<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 删除商品的关联促销信息
 * item.promotion.deleteTag
 */
class sysitem_api_promotion_itemPromotionTagDelete {

    public $apiDescription = '删除商品的某个促销信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'promotion_id' => ['type'=>'int', 'valid'=>'required|integer', 'description'=>'促销id'],
        );

        return $return;
    }


    public function itemPromotionTagDelete($params)
    {
        $objMdlItemPromotionTag = app::get('sysitem')->model('item_promotion');
        return $objMdlItemPromotionTag->delete(array('promotion_id'=>$params['promotion_id']));
    }
}

