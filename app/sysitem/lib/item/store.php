<?php

class sysitem_item_store {

    public function updateStore($itemId = null, $skuId, $store)
    {
        //更新sku库存
        $skuStoreModel = app::get('sysitem')->model('sku_store');
        $filter = ['sku_id'=>$skuId];
        $skuStore = $skuStoreModel->getRow('*', $filter);
        $freez = $skuStore['freez'];
        $skuStore['store'] = $freez + $store;
        $skuStoreModel->save($skuStore);

        if(is_null($itemId))
        {
            $itemId = $skuStore['item_id'];
        }

        //更新item库存
        $filter = ['item_id'=>$itemId];
        $skuStores = $skuStoreModel->getList('store,freez', $filter);
        $store = 0;
        $freez = 0;
        foreach($skuStores as $skuStore)
        {
            $freez = $freez + $skuStore['freez'];
            $store = $store + $skuStore['store'];
        }
        $itemStoreModel = app::get('sysitem')->model('item_store');
        $itemStore = ['item_id'=>$itemId, 'store'=>$store, 'freez'=>$freez];
        $itemStoreModel->save($itemStore);

        return true;
    }

    public function updateStoreByBn($itemBn, $skuBn, $shopId, $store)
    {
        $ids = $this->__getIdsByBn($itemBn, $skuBn, $shopId);
        $itemId = $ids['item_id'];
        $skuId = $ids['sku_id'];
        return $this->updateStore($itemId, $skuId, $store);
    }

  //private function __getItemIdBySkuId($skuId)
  //{
  //    $ItemModel = app::get('sysitem')->model('sku');
  //    $filter = ['sku_id'=>$skuId];
  //    $sku = $ItemModel->getRow('item_id', $filter);
  //    $itemId = $sku['item_id'];

  //    return $itemId;
  //}
    //
    public function getIdByBn($itemBn=null, $skuBn, $shopId)
    {
        return $this->__getIdsByBn($itemBn, $skuBn, $shopId);
    }

    private function __getIdsByBn($itemBn = null, $skuBn, $shopId)
    {
        $ItemModel = app::get('sysitem')->model('item');
        $skuModel = app::get('sysitem')->model('sku');
        //获取item ID
        if(is_null($itemBn))
        {
            $filter = ['bn'=>$skuBn];
            $skuRes = $skuModel->getList('item_id', $filter);
            $itemIdFormat = array();
            foreach($skuRes as $sk)
            {
                $itemId = $sk['item_id'];
                $itemIdFormat[$itemId] = $itemId;
            }
            $filter = ['item_id|in'=>$itemIdFormat, 'shop_id'=>$shopId];
            $itemRes = $ItemModel->getList('item_id', $filter);

            $itemIdFormat = array();
            foreach($itemRes as $it)
            {
                $itemId = $it['item_id'];
                $itemIdFormat[$itemId] = $itemId;
            }

            $sku = $skuModel->getRow('item_id,sku_id', ['item_id|in'=>$itemIdFormat, 'bn'=>$skuBn] );
            return $sku;
        }
        else
        {
            $filter = ['bn'=>$itemBn, 'shop_id'=>$shopId];
            $item = $ItemModel->getRow('item_id', $filter);
            $itemId = $item['item_id'];
            $this->__checkNullId($itemId, '根据bn"' . $itemBn . '"没有找到相应的item');

            //获取sku ID
            $filter = ['bn'=>$skuBn, 'item_id'=>$itemId];
            $sku = $skuModel->getRow('sku_id', $filter);
            $skuId = $sku['sku_id'];
            $this->__checkNullId($skuId, '根据bn"' . $skuBn . '"没有找到相应的item');

            return ['item_id'=>$itemId, 'sku_id'=>$skuId];
        }
    }

    private function __checkNullId($id, $errMsg)
    {
        if( ! $id > 0 )
        {
            throw new LogicException($errMsg);
        }
    }

}

