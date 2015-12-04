<?php
class sysuser_api_addUserAdress {

    /**
     * 接口作用说明
     */
    public $apiDescription = '会员地址添加';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'addr_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品ID必填','default'=>'','example'=>''],
            'area' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'地区','default'=>'','example'=>''],
            'addr' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'地址','default'=>'','example'=>''],
            'zip' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'邮编','default'=>'','example'=>''],
            'name' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'会员名称','default'=>'','example'=>''],
            'mobile' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'电话','default'=>'','example'=>''],
            'def_addr' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否是设为默认','default'=>'','example'=>''],
        );

        return $return;
    }

    public function addUserAdress($apiData)
    {
        $objLibUserAddr =  kernel::single('sysuser_data_user_addrs');
        return $objLibUserAddr->saveAddrs($apiData);
    }
}
