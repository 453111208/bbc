<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条促销信息详情
 * promotion.promotion.get
 */
final class syspromotion_api_promotionGet {

    public $apiDescription = '获取单条促销信息详情';

    public function getParams()
    {
        $return['params'] = array(
            'user_id'       => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id'       => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'promotion_id'  => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'促销表id'],
            'platform'      => ['type'=>'string', 'valid'=>'', 'default'=>'pc', 'example'=>'', 'description'=>'促销规则应用平台'],
        );

        return $return;
    }

    /**
     *  获取单条促销详细信息
     * @param  array $params 筛选条件数组
     * @return array         返回一条促销详情
     */
    public function promotionGet($params)
    {
        $filter['promotion_id'] = $params['promotion_id'];
        // 平台未选择则默认全选
        if( $params['platform'] == 'pc' )
        {
            $filter['used_platform'] = array('0', '1');
        }
        elseif( $params['platform'] == 'wap' )
        {
            $filter['used_platform'] = array('0', '2');
        }
        else
        {
            $filter['used_platform'] = array('0','1','2');
        }

        $promotionBasic = app::get('syspromotion')->model('promotions')->getRow('*', $filter);

        $now = time();
        // 这里只判断促销本身是否在生效，不包含具体规则导致的是否可用
        if( $now>$promotionBasic['start_time'] && $now<$promotionBasic['end_time'] && $promotionBasic['check_status']=='agree')
        {
            $promotionBasic['valid'] = true;
        }
        else
        {
            $promotionBasic['valid'] = false;
        }
        // if($params['has_detail'])
        // {
        //     $promotionBasic['detail'] = app::get('syspromotion')->model('fullminus')->getRow('*', array('fullminus_id'=>$promotionBasic['rel_promotion_id']) );
        // }
        return $promotionBasic;
    }

}

