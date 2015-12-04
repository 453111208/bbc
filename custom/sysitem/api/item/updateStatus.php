<?php
class sysitem_api_item_updateStatus{
    public $apiDescription = "商品上下架修改";
    public function getParams($params)
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required|int','description'=>'商品id，多个id用，隔开','example'=>'2,3,5,6','default'=>''],
            'shop_id' => ['type'=>'int','valid'=>'required|int','description'=>'企业id','example'=>'','default'=>''],
            'approve_status' => ['type'=>'string','valid'=>'required','description'=>'商品上架状态','example'=>'','default'=>''],
        );
        return $return;
    }
    public function updateStatus($params)
    {
        $itemId = $params['item_id'];
        $status = $params['approve_status'];
        $result = kernel::single('sysitem_data_item')->setSaleStatus(intval($itemId), $status);
        return $result;
    }
}
