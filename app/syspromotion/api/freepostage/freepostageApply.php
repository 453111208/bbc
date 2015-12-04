<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 免邮促销规则应用
 * promotion.fullfreepostage.apply
 */
final class syspromotion_api_freepostage_freepostageApply {

    public $apiDescription = '免邮促销规则应用';

    public function getParams()
    {
        $return['params'] = array(
            'freepostage_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'促销表id'],
            'promotion_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'促销关联表id'],
            'forPromotionTotalPrice' => ['type'=>'float', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'符合应用促销的商品总价'],
            'forPromotionTotalQuantity' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'符合应用促销的商品总数量'],
        );

        return $return;
    }

    /**
     *  免邮促销规则应用
     * @param  array $params 筛选条件数组
     * @return array         返回一条促销详情
     */
    public function freepostageApply($params)
    {
        $data = array(
            'user_id' => $params['oauth']['account_id'],
            'freepostage_id' => $params['freepostage_id'],
            'promotion_id' => $params['promotion_id'],
            'forPromotionTotalPrice' => $params['forPromotionTotalPrice'],
            'forPromotionTotalQuantity' => $params['forPromotionTotalQuantity'],
        );
        $result = kernel::single('syspromotion_solutions_freepostage')->apply($data);

        return $result;
    }


}

