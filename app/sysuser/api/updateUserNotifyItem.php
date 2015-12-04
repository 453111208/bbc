<?php
class sysuser_api_updateUserNotifyItem {

    /**
     * 接口作用说明
     */
    public $apiDescription = '会员到货通知修改';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'gnotify_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'缺货ID'],
        );

        return $return;
    }

    public function updateUserNotifyItem($params)
    {
        $objMdlUserItem =  app::get('sysuser')->model('user_item_notify');
        $params['send_time'] = time();
        $params['sendstatus'] = 'send';
        return $objMdlUserItem->save($params);
    }
}
