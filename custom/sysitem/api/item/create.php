<?php
class sysitem_api_item_create {

    public $apiDescription = "商品添加";

    public function getParams()
    {
        $return['params'] = array();
        return $return;
    }

    public function itemCreate($params)
    {
        $result = kernel::single('sysitem_data_item')->add($params);
        return $result;
    }
}
