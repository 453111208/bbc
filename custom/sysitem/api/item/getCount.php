<?php
class sysitem_api_item_getCount{

    public $apiDescription = "获取商品统计数据";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required','description'=>'商品编号','example'=>'2','default'=>''],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'要获取的统计字段','example'=>'','default'=>''],
        );
        return $return;
    }
    public function get($params)
    {
        $fields = $params['fields'] ? $params['fields'] : "*";
        $filter['item_id'] = $params['item_id'];
        $objMdlItemCount = app::get('sysitem')->model('item_count');
        $itemInfoCount = $objMdlItemCount->getList($fields, $filter);
        return array_bind_key($itemInfoCount, 'item_id');
    }
}
