<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysitem_trade_store
{

    public function get_goods_type()
    {
        return 'item';
    }

    /**
     * 下单减库存
     * 直接扣减库存(对应下单减库存),对应function recoverItemStore
     * @param  array  $arrParams sku库存信息
     * @return bool
     */
    public function minusItemStore($arrParams=array())
    {
        if (!$arrParams)
        {
            return false;
        }

        $isUpdated = false;
        $db = app::get('sysitem')->database();
        //更新货品库存
        try
        {
            $isUpdated = $db->executeUpdate('UPDATE sysitem_sku_store SET store = store - ? WHERE sku_id = ? AND store - ? >= 0', [$arrParams['quantity'], $arrParams['sku_id'], $arrParams['quantity']]);
            if( !$isUpdated ) return false;
        }
        catch( Exception $e)
        {
            if( !$isUpdated ) return false;
            return false;
        }

        //更新商品库存
        try
        {
            $isUpdated = $db->executeUpdate('UPDATE sysitem_item_store SET store = store - ? WHERE item_id = ? AND store - ? >= 0', [$arrParams['quantity'], $arrParams['item_id'], $arrParams['quantity']]);
            if( !$isUpdated ) return false;
        }
        catch( Exception $e )
        {
            return false;
        }

        return true;
    }

    /**
     * 取消订单恢复库存
     * 恢复扣减的库存(对应下单减库存),对应function minusItemStore
     * @param  array  $arrParams sku库存信息
     * @return bool
     */
    public function recoverItemStore($arrParams=array())
    {
        if (!$arrParams)
        {
            return false;
        }

        $isUpdated = false;

        $objMdlItemStore = app::get('sysitem')->model('item_store');
        $objMdlSkuStore = app::get('sysitem')->model('sku_store');
        $objMath = kernel::single('ectools_math');

        $skuInfo = $objMdlSkuStore->getRow('item_id,store', array('sku_id'=>$arrParams['sku_id']));
        $itemInfo = $objMdlItemStore->getRow('store', array('item_id'=>$skuInfo['item_id']));

        if (!is_null($skuInfo['store']))
        {
            $updateData = array(
                'store' => $objMath->number_plus(array($skuInfo['store'], $arrParams['quantity'])),
            );

            $isUpdated = $objMdlSkuStore->update($updateData, array('sku_id'=>$arrParams['sku_id']));
        }

        if ($isUpdated)
        {
            if (!is_null($itemInfo['store']))
            {
                $updateData = array(
                    'store' => $objMath->number_plus(array($itemInfo['store'], $arrParams['quantity'])),
                );

                if ($objMdlItemStore->update($updateData, array('item_id'=>$skuInfo['item_id'])))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 下单减库存情况下在付款成功时扣减库存和冻结库存，对应于有freez情况下
     * @param  array  $arrParams sku库存信息
     * @return bool
     */
    public function minusItemStoreAfterPay($arrParams=array())
    {
        if (!$arrParams)
        {
            return false;
        }

        $objMdlItem = app::get('sysitem')->model('item');
        $itemInfo = $objMdlItem->getRow('sub_stock', array('item_id'=>$arrParams['item_id']));
        if($itemInfo['sub_stock'])
        {
            return true;
        }

        $isUpdated = false;
        $objMdlSkuStore = app::get('sysitem')->model('sku_store');
        $objMdlItemStore = app::get('sysitem')->model('item_store');
        $skuInfo = $objMdlSkuStore->getRow('item_id,store,freez', array('sku_id'=>$arrParams['sku_id']));
        $itemInfo = $objMdlItemStore->getRow('store,freez', array('item_id'=>$skuInfo['item_id']));
        $objMath = kernel::single('ectools_math');

        if ( !is_null($skuInfo['store']) && !is_null($skuInfo['freez']) )
        {
            if ($objMath->number_minus(array($skuInfo['store'], $arrParams['quantity'])) < 0)
            {
                return false;
            }
            if ($objMath->number_minus(array($skuInfo['freez'], $arrParams['quantity'])) < 0)
            {
                return false;
            }

            $updateData = array(
                'freez' => $objMath->number_minus(array($skuInfo['freez'], $arrParams['quantity'])),
                'store' => $objMath->number_minus(array($skuInfo['store'], $arrParams['quantity'])),
            );
            $isUpdated = $objMdlSkuStore->update($updateData, array('sku_id'=>$arrParams['sku_id']));
        }

        if ($isUpdated)
        {
            $updateData = array(
                'freez' => $objMath->number_minus(array($itemInfo['freez'], $arrParams['quantity'])),
                'store' => $objMath->number_minus(array($itemInfo['store'], $arrParams['quantity'])),
            );

            if ($objMdlItemStore->update($updateData, array('item_id'=>$skuInfo['item_id'])))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }


    /**
     * 付款减库存时冻结库存
     * 冻结库存(对应付款减库存),对应 function unfreezeItemStore
     * @param  array  $arrParams sku库存信息
     * @return bool
     */
    public function freezeItemStore($arrParams=array())
    {
        if (!$arrParams)
        {
            return false;
        }

        $isFreeze = false;
        $objMdlItem = app::get('sysitem')->model('item');
        if (isset($arrParams['item_id']) && $arrParams['item_id'])
        {
            $isFreeze = $this->freez($arrParams['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }
        else
        {
            $objMdlSku = app::get('sysitem')->model('sku');
            $skuInfo = $objMdlSku->getRow('item_id', array('sku_id'=>$arrParams['sku_id']));

            $isFreeze = $this->freezeItemStore($skuInfo['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }

        return $isFreeze;
    }

    /**
     * 付款减库存情况下取消订单释放库存
     * 解冻库存(对应付款减库存),对应 function freezeItemStore
     * @param  array  $arrParams sku库存信息
     * @return bool
     */
    public function unfreezeItemStore($arrParams=array())
    {
        if (!$arrParams)
        {
            return false;
        }

        $isUnfreeze = false;
        if (isset($arrParams['item_id']) && $arrParams['item_id'])
        {
            $isUnfreeze = $this->unfreez($arrParams['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }
        else
        {
            $objMdlSku = app::get('sysitem')->model('sku');
            $skuInfo = $objMdlSku->getRow('item_id', array('sku_id' => $arrParams['sku_id']));

            $isUnfreeze = $this->unfreez($skuInfo['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }

        return $isUnfreeze;
    }

    public function check_freez($arrParams)
    {
        if (!$arrParams)
        {
            return true;
        }

        $isFreeze = true;
        if (isset($arrParams['item_id']) && $arrParams['item_id'])
        {
            $isFreeze = $this->checkFreez($arrParams['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }
        else
        {
            $objMdlSku = app::get('sysitem')->model('sku');
            $skuInfo = $objMdlSku->getRow('item_id', array('sku_id'=>$arrParams['sku_id']));

            $isFreeze = $this->checkFreez($skuInfo['item_id'], $arrParams['sku_id'], $arrParams['quantity']);
        }

        return $isFreeze;
    }

    /**
     * 检查货品是否可以冻结库存
     * @param string itemId
     * @param string skuId
     * @param string num
     * @return boolean true or false
     */
    public function checkFreez($itemId, $skuId, $num)
    {
        $objMdlSkuStore = app::get('sysitem')->model('sku_store');
        $skuStoreInfo = $objMdlSkuStore->getRow('freez,store', array('sku_id'=>$skuId));
        $objMath = kernel::single('ectools_math');

        if(is_null($skuStoreInfo['freez']) || $skuStoreInfo['freez'] === '')
        {
            $skuStoreInfo['freez'] = 0;
            $skuStoreInfo['freez'] = $objMath->number_plus(array($skuStoreInfo['freez'], $num));
            if ($skuStoreInfo['freez'] > $skuStoreInfo['store'])
            {
                return false;
            }
        }
        elseif($objMath->number_plus(array($skuStoreInfo['freez'], $num)) > $skuStoreInfo['store'] )
        {
            return false;
        }
        else
        {
            $skuStoreInfo['freez'] = $objMath->number_plus(array($skuStoreInfo['freez'], $num));
        }

        return true;
    }

    /**
     * 释放冻结的库存
     * @params string itemId
     * @params string skuId
     * @params string num
     */
    private function unfreez($itemId, $skuId, $num)
    {
        // 更新sku_store表的冻结库存
        $objMdlSkuStore = app::get('sysitem')->model('sku_store');
        $skuStoreInfo = $objMdlSkuStore->getRow('freez', array('sku_id'=>$skuId));
        $objMath = kernel::single('ectools_math');

        if(is_null($skuStoreInfo['freez']))
        {
            $skuStoreInfo['freez'] = 0;
        }
        elseif($num < $skuStoreInfo['freez'])
        {
            $skuStoreInfo['freez'] = $objMath->number_minus(array($skuStoreInfo['freez'], $num));
        }
        elseif($num >= $skuStoreInfo['freez'])
        {
            $skuStoreInfo['freez'] = 0;
        }
        $skuStoreInfo['sku_id'] = $skuId;
        $skuStoreInfo['item_id'] = $itemId;
        if( !$objMdlSkuStore->save($skuStoreInfo) )
        {
            return false;
        }

        // 更新item_store表的冻结库存
        $objMdlItemStore = app::get('sysitem')->model('item_store');
        $itemStoreInfo = $objMdlItemStore->getRow('freez', array('item_id'=>$itemId));
        $objMath = kernel::single('ectools_math');
        if(is_null($itemStoreInfo['freez']))
        {
            $itemStoreInfo['freez'] = 0;
        }
        elseif($num < $itemStoreInfo['freez'])
        {
            $itemStoreInfo['freez'] = $objMath->number_minus(array($itemStoreInfo['freez'], $num));
        }
        elseif($num >= $itemStoreInfo['freez'])
        {
            $itemStoreInfo['freez'] = 0;
        }
        $itemStoreInfo['item_id'] = $itemId;
        if( !$objMdlItemStore->save($itemStoreInfo) )
        {
            return false;
        }
        return true;


    }

    /**
     * 冻结产品的库存
     * @params string item_id
     * @params string skuId
     * @params string num
     */
    private function freez($itemId, $skuId, $num)
    {
        // 修改sku_store表的冻结库存
        $db = app::get('sysitem')->model('sku_store')->database();
        $sqlStr = "UPDATE sysitem_sku_store SET freez=ifnull(freez,0)+? WHERE sku_id=? AND item_id=? AND store>=?+ifnull(freez,0)";
        if ($db->executeUpdate($sqlStr, [$num, $skuId, $itemId, $num])==0){
            return false;
        }
        // 修改商品主item_store表的冻结库存
        $db = app::get('sysitem')->model('item_store')->database();
        $sqlStr = "UPDATE sysitem_item_store SET freez=ifnull(freez,0)+? WHERE  item_id=? AND store>=?+ifnull(freez,0)";
        if ($db->executeUpdate($sqlStr, [$num, $itemId, $num])==0){
            return false;
        }
        return true;
    }

}
