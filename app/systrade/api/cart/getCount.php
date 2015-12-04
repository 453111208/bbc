<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class systrade_api_cart_getCount
{
    public $apiDescription = "统计购物车商品数量";
    public function getParams()
    {
        $return['params'] = array(
            ['user_id'] => ['type'=>'int','valid'=>'required','description'=>'会员id','default'=>'','example'=>'3'],
        );
        return $return;
    }
    public function getCount($params)
    {
        $userId = $params['user_id'];
        return kernel::single('systrade_data_cart', $userId)->countCart();
    }
}

