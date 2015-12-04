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
class sysitem_api_itemPromotionTagUpdate {

    /**
     * 接口作用说明,目前不包括优惠券促销
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
    更新商品的关联促销（不包括满减促销）
    每次只更新其中的一条，例如商品同时有满减促销，满赠促销，每次只处理其中一个促销，如只处理满减促销

    promotion_ids字段，“促销id，促销id”用逗号连接
    "$promotion_id1,$promotion_id2...."

    @params  单个促销的传入参数数组，例如满减 array(
                'item_id' => $item_id,
                'promotion_id' => $promotion_id,
            )
    */
    public function itemPromotionTagUpdate($params)
    {
        $objMdlItemTagPromotion = app::get('sysitem')->model('item_tag_promotion');
        $oldInfo = $objMdlItemTagPromotion->getRow('*',array('item_id'=>$params['item_id']));

        // 将新的单个促销主键连接成"促销id，促销id",插入字段内如，’promotion_id1,promotion_id2.....‘
        if($oldInfo['promotion_ids'])
        {
            // 原来已经有数据的情况下，将新的促销的promotion_id的值追加到 ",promotion_ids"
            $newPromotionWithId = ','.$params['promotion_id'];
            $nowPromotionIdsStr = $oldInfo['promotion_ids'] . $newPromotionWithId;
            $nowPromotionIdsArray = explode(',', $nowPromotionIdsStr);
            // 如果原来有这个 "促销id" 值，则去重
            $newPromotionIdsArray = array_unique($nowPromotionIdsArray);
            // 重新以逗号链接，’promotion_id1,promotion_id2.....‘
            $newPromotionIdsString = implode(',', $newPromotionIdsArray);
        }
        else
        {
            // 原来不存在此单个促销promotion_id信息，第一次则直接组建这个促销的id的字符串
            $newPromotionIdsString = $params['promotion_id'];
        }
        // 重新组织单个促销如(满减)的数组信息，
        $oldInfo['promotion_ids'] = $newPromotionIdsString;

        $oldInfo['item_id'] = $params['item_id'];

        if(!$objMdlItemTagPromotion->save($oldInfo))
        {
            return false;
        }
        return true;
    }
}

