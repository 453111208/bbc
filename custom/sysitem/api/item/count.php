<?php
class sysitem_api_item_count{
    public $apiDescription = "统计商品数量";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'企业id','example'=>'2','default'=>''],
            'status' => ['type'=>'string','valid'=>'','description'=>'商品状态','example'=>'2','default'=>''],
        );

        return $return;
    }
    public function itemCount($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        if($params['status'])
        {
            $filter['approve_status'] = $params['status'];
        }
        $objMdlItemStatus = app::get('sysitem')->model('item_status');
        $count = $objMdlItemStatus->count($filter);
        return $count;
    }
}
