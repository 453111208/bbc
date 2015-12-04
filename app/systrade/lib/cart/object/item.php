<?php

class systrade_cart_object_item {

    //返回加入购物车的商品类型
    public function getItemType()
    {
        return 'item';//普通商品类型
    }

    //获取检查是否可以加入购物车排序，由小到大排序处理
    public function getCheckSort()
    {
        return 100;
    }

    /**
     * @brief 检查购买的商品是否可以购买
     *
     * @param array $params 加入购物车参数
     * @param array $itemData 加入购物车的基本商品数据
     * @param array $skuData 加入购物车的基本SKU数据
     *
     * @return bool
     */
    public function check($checkParams, $itemData, $skuData)
    {
        if( $checkParams['obj_type'] != 'item' ) return true;

        if($checkParams['totalQuantity'] <=0)
        {
            throw new \LogicException(app::get('systrade')->_("库存不能为零，最小库存为1"));
        }
        //有效库存（可售库存）
        $validQuantity = $skuData['store'] - $skuData['freez'];
        if( $validQuantity < $checkParams['totalQuantity'] )
        {
            throw new \LogicException(app::get('systrade')->_("库存不足, 最大库存为".$validQuantity));
        }
        return true;
    }
}

