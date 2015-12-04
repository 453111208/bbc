<?php
class sysuser_api_addressSetDef {

    /**
     * 接口作用说明
     */
    public $apiDescription = '会员地址默认设置';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'addr_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'地址ID必填','default'=>'','example'=>''],
        );

        return $return;
    }

    public function addressSetDef($apiData)
    {
        $objLibUserAddr =  kernel::single('sysuser_data_user_addrs');
        return $objLibUserAddr->setDefAddr($apiData['user_id'],$apiData['addr_id']);
    }
}
