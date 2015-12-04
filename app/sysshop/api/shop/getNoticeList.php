<?php
class sysshop_api_shop_getNoticeList{

    public $apiDescription = "获取店铺通知";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺id','default'=>'','example'=>''],
            'notice_type' => ['type'=>'string','valid'=>'','description'=>'店铺id','default'=>'','example'=>''],
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1','default'=>'','example'=>''],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认20条','default'=>'','example'=>''],
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getNoticeList($params)
    {
        $shopNoticeLib = kernel::single('sysshop_data_shopnotice');
        $params['shop_id'] = array($params['shop_id'],0);
        
        $result = $shopNoticeLib->getNoticeList($params);
        return $result;
    }
}
