<?php
class sysuser_api_getAddrInfo{
    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员地址详情';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填'],
            'addr_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'收货地址id'],
            'def_addr' => ['type'=>'bool','valid'=>'', 'default'=>'0', 'example'=>'', 'description'=>'是否为默认收货地址'],
            'fields' => ['type'=>'field_list','valid'=>'', 'default'=>'*', 'example'=>'', 'description'=>'所需字段'],
        );

        return $return;
    }

    public function getAddrInfo($apiData)
    {
        $filter['user_id'] = intval($apiData['user_id']);
        if($apiData['addr_id'])
        {
            $filter['addr_id'] = intval($apiData['addr_id']);
        }
        if($apiData['def_addr'])
        {
            $filter['def_addr'] = $apiData['def_addr'];
        }
        $rows = $apiData['fields'];
        if(!$rows)
        {
            $rows = "*";
        }
		$userMdlAddr = app::get('sysuser')->model('user_addrs');

		$addrInfo = $userMdlAddr->getRow($rows,$filter);
        return $addrInfo;
    }

}
