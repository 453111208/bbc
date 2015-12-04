<?php

class sysitem_api_updateSoldQuantity {

    /**
     * 接口作用说明
     */
    public $apiDescription = '修改商品销量';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'item_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'商品ID'],
            'num'     => ['type'=>'int', 'valid'=>'required', 'description'=>'本次增加的商品销量'],
        );

        return $return;
    }

    /**
     * 更新销量
     */
    public function updateSoldQuantity($params)
    {
        $db = app::get('sysitem')->database();
        return $db->executeUpdate('UPDATE sysitem_item_count SET sold_quantity = sold_quantity + ? WHERE item_id = ?', [$params['num'], $params['item_id']]);
    }
}

