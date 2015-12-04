<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 更新商品的促销标签信息
 * item.promotiontag.delete
 */
class sysitem_api_itemPromotionTagDelete {

    /**
     * 接口作用说明
     */
    public $apiDescription = '删除商品的某个促销标签信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'item_ids' => ['type'=>'string', 'valid'=>'required', 'description'=>'商品ID字符串'],
            'promotion_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'促销id'],
        );

        return $return;
    }


    public function itemPromotionTagDelete($params)
    {
        $objMdlItemTagPromotion = app::get('sysitem')->model('item_tag_promotion');
        $itemIds = explode(',', $params['item_ids']);
        if(!$itemIds)
        {
            return false;
        }
        foreach($itemIds as $item_id)
        {
            $oldInfo = $objMdlItemTagPromotion->getRow('*', array('item_id'=>$item_id));
            $oldPromotionIds = explode(',', $oldInfo['promotion_ids']);
            $pkey = array_search($params['promotion_id'], $oldPromotionIds);
            if( $pkey!=='' )
            {
                unset($oldPromotionIds[$pkey]);
                $newInfo['promotion_ids'] = $oldPromotionIds ? implode(',', $oldPromotionIds) : '';
                $newInfo['item_id'] = $item_id;
                if(!$objMdlItemTagPromotion->save($newInfo))
                {
                    return false;
                }
            }
        }

        return true;
    }
}

