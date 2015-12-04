<?php
class sysuser_api_addCollectItem {

    /**
     * 接口作用说明
     */
    public $apiDescription = '添加商品收藏';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'item_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商品ID必填','default'=>'','example'=>''],
            'objectType' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'类型如：goods','default'=>'','example'=>''],
        );

        return $return;
    }

    public function addItemCollect($apiData)
    {
        return kernel::single('sysuser_data_user_fav')->addFav($apiData['user_id'],$apiData['objectType'],$apiData['item_id']);
    }
}
