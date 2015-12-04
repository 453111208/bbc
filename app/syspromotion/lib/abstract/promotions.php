<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

abstract class syspromotion_abstract_promotions 
{
    public $allPromotionType = array('fullminus', 'fulldiscount', 'freepostage', 'xydiscount');

    protected $currentPromotion;

    /**
     * 设置当前促销
     * @param string 促销类型
     * @return object 本类对象
     */
    public function set_promotions($promotionType) 
    {
        $this->currentPromotion = $promotionType;
        return $this;
    }//End Function


    // 将添加的促销存入促销关联表
    public function savePromotions($proData){
        $objMdlPromotions = app::get('syspromotion')->model('promotions');
        // 如果原来此促销已经存在则更新原数据而不是新添加
        $filter = array(
            'promotion_type' => $proData['promotion_type'],
            'rel_promotion_id' => $proData['rel_promotion_id'],
        );
        if( $row = $objMdlPromotions->getRow('promotion_id', $filter) )
        {
            $data['promotion_id'] = $row['promotion_id'];
        }
        $data['rel_promotion_id'] = $proData['rel_promotion_id'];
        $data['shop_id']          = $proData['shop_id'];
        // $data['promotion_type']   = $this->currentPromotion;
        $data['promotion_type']   = $proData['promotion_type'];
        $data['promotion_name']   = $proData['promotion_name'];
        $data['promotion_tag']    = $proData['promotion_tag'];
        $data['promotion_desc']   = $proData['promotion_desc'];
        $data['used_platform']    = $proData['used_platform'];
        $data['start_time']       = $proData['start_time'];
        $data['end_time']         = $proData['end_time'];
        $data['created_time']     = $proData['created_time'];
        $data['check_status']     = $proData['check_status'];
        if($objMdlPromotions->save($data))
        {
            return $data['promotion_id'];
        }
        else
        {
            return false;
        }
    }

    // 删除促销时删除对应的促销关联表的促销信息
    public function deletePromotions($proData)
    {
        if( $proData['rel_promotion_id'] && in_array($proData['promotion_type'], $this->allPromotionType) )
        {
            $filter['rel_promotion_id'] = intval($proData['rel_promotion_id']);
            $filter['promotion_type'] = $proData['promotion_type'];
            return app::get('syspromotion')->model('promotions')->delete($filter);
        }
        return false;
    }

}
