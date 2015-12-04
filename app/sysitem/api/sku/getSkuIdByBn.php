<?php
class sysitem_api_sku_getSkuIdByBn {
    public $apiDescription = "根据sku_bn反查sku_id";
    public function getParams()
    {
        $return['params'] = array(
            'item_bn'=>['type'=>'string','valid'=>'','description'=>'商品编号bn','default'=>'','example'=>''],
            'sku_bn'=>['type'=>'string','valid'=>'required','description'=>'货品编号bn','default'=>'','example'=>''],
            'shop_id'=>['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>''],
        );
        return $return;
    }

    public function getIds($params)
    {
        $itemBn = $params['item_bn'] ? $params['item_bn'] : null;
        $skuBn  = $params['sku_bn'];
        $shopId = $params['shop_id'];

        $ids = kernel::single('sysitem_item_store')->getIdByBn($itemBn, $skuBn, $shopId);

        return $ids;
    }
}
