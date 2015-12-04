<?php
class sysitem_api_itemNatureProp{
    public $apiDescription = "获取指定商品的自然属性";
    public function getParams()
    {
        $return['params'] = array(
            'item_id'=>['type'=>'int','valid'=>'required','description'=>'商品id','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getItemNatutrProp($params)
    {
        $itemId = $params['item_id'];
        $item_nature_props = kernel::single('sysitem_item_info')->getItemNatureProp($itemId, true);
        return $item_nature_props;
    }
}
