<?php
class sysitem_api_item_getSkuList{
    public $apiDescription = "获取指定商品的货品列表";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required','description'=>'商品id','example'=>'2','default'=>''],
        );
        return $return;
    }
    public function getList($params)
    {
        $itemId = explode(',',$params['item_id']);
        $productData = kernel::single('sysitem_item_info')->getItemSkus($itemId,'*');
        return $productData;
    }
}
