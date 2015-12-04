<?php
class sysuser_api_getAddrCount {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员目前地址数量和地址最大限制数量';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
        );

        return $return;
    }

    public function getAddrCount($apiData)
    {
        $userId = $apiData['user_id'];
        $objLibUserAddr =  kernel::single('sysuser_data_user_addrs');
        return $objLibUserAddr->getAddrCount($userId);
    }
}
