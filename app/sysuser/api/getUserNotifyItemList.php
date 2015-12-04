<?php
class sysuser_api_getUserNotifyItemList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '会员到货通知列表';

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
            'sendstatus' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'发送状态'],
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认20条'],
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段'],
        );

        return $return;
    }

    public function getUserNotifyItemList($params)
    {
        $objMdlUserItem =  app::get('sysuser')->model('user_item_notify');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $filter = array(
            'item_id'=>$params['item_id'],
            'shop_id'=>$params['shop_id'],
            'sendstatus'=>$params['sendstatus'],
        );

        $pageSize = $params['page_size'] ? $params['page_size'] : 100;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $limit = $pageSize;
        $page = ($pageNo-1)*$limit;
        $aData = $objMdlUserItem->getList($params['fields'], $filter, $page,$limit, $orderBy);
        foreach ($aData as $key => $value)
        {
            $aData[$key]['vcode'] = url::action('topc_ctl_item@index',array('item_id'=>$value['item_id']));
        }
        return $aData;
    }
}
