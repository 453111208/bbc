<?php
class sysuser_api_getAddrList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员地址列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填'],
            'fields' => ['type'=>'field_list','valid'=>'', 'default'=>'*', 'example'=>'', 'description'=>'所需字段'],
        );

        return $return;
    }

    public function getAddrList($apiData)
    {
        $userId = $apiData['user_id'];
        $rows = $apiData['fields'];
        if(!$rows)
        {
            $rows = "*";
        }
		$userMdlAddr = app::get('sysuser')->model('user_addrs');

		$result['list'] = $userMdlAddr->getList($rows,array('user_id'=>$userId));
		$result['count']['nowcount'] = $userMdlAddr->count(array('user_id'=>$userId));
        $return['count']['maxcount'] = $userMdlAddr->addrLimit;
        return $result;
    }
}
