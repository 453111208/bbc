<?php
class sysuser_api_addUserNotifyItem {

    /**
     * 接口作用说明
     */
    public $apiDescription = '会员到货通知';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商家id'],
            'item_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商品id'],
            'sku_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'货品id'],
            'email' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'邮件'],
        );

        return $return;
    }

    public function addUserNotifyItem($params)
    {
        $objMdlUserItem =  app::get('sysuser')->model('user_item_notify');
        $params['createtime'] = time();
        $params['sendstatus'] = 'ready';
        $filter = array(
            'shop_id'=>$params['shop_id'],
            'item_id'=>$params['item_id'],
            'sku_id'=>$params['sku_id'],
            'email'=>$params['email'],
            'sendstatus'=>'ready',
        );
        $userItem = $objMdlUserItem->getRow('gnotify_id',$filter);
        if($userItem)
        {
            throw new \LogicException('您已经填过该商品的到货通知！');
        }
        return $objMdlUserItem->save($params);
    }
}
