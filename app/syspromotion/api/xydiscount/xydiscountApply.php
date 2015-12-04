<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * X件Y折促销规则应用
 * promotion.xydiscount.apply
 */
final class syspromotion_api_xydiscount_xydiscountApply {

    public $apiDescription = 'X件Y折促销规则应用';

    public function getParams()
    {
        $return['params'] = array(
            'xydiscount_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'X件Y折促销表id'],
            'promotion_id' => ['type'=>'int', 'valid'=>'required|integer', 'default'=>'', 'example'=>'', 'description'=>'促销关联表id'],
            'forPromotionTotalPrice' => ['type'=>'float', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'符合应用促销的商品总价'],
            'forPromotionTotalQuantity' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'符合应用促销的商品总数量'],
        );

        return $return;
    }

    /**
     *  X件Y折促销规则应用
     * @param  array $params 筛选条件数组
     * @return array         返回一条促销详情
     */
    public function xydiscountApply($params)
    {
        $data = array(
            'user_id' => $params['oauth']['account_id'],
            'xydiscount_id'=>$params['xydiscount_id'],
            'promotion_id' => $params['promotion_id'],
            'forPromotionTotalPrice' => $params['forPromotionTotalPrice'],
            'forPromotionTotalQuantity' => $params['forPromotionTotalQuantity'],
        );
        $discount_price = kernel::single('syspromotion_solutions_xydiscount')->apply($data);

        return $discount_price;
    }


}

