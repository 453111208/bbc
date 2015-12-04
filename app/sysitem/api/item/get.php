<?php
class sysitem_api_item_get{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取单个商品的详细信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required','description'=>'商品编号','example'=>'2','default'=>''],
            'shop_id' => ['type'=>'int','valid'=>'','description'=>'店铺id','example'=>'2','default'=>''],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'要获取的商品字段集','example'=>'title,item_store.store,item_status.approve_status','default'=>''],
        );

        $return['extendsFields'] = ['item_desc','item_count','item_store','item_status','sku','item_nature','spec_index','promotion'];
        return $return;
    }

    public function get($params)
    {
        $filter['item_id'] = $params['item_id'];
        if($params['shop_id'])
        {
            $filter['shop_id'] = $params['shop_id'];
        }

        $row = $params['fields']['rows'];
        $extends = $params['fields']['extends'];

        $objLibIteminfo = kernel::single('sysitem_item_info');
        $iteminfo = $objLibIteminfo->getItemInfo($filter, $row, $extends);
        return $iteminfo;
    }
}
