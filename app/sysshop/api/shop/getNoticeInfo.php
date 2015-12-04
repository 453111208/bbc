<?php
class sysshop_api_shop_getNoticeInfo{

    public $apiDescription = "获取店铺单条通知";
    public function getParams()
    {
        $return['params'] = array(
            'notice_id' => ['type'=>'string','valid'=>'required','description'=>'店铺id','default'=>'','example'=>''],
            'fields'=> ['type'=>'field_list','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getNoticeInfo($params)
    {
        $shopNoticeLib = kernel::single('sysshop_data_shopnotice');
        if($params['fields']=='')
        {
            $params['fields'] = '*';
        }
        $result = $shopNoticeLib->getNoticeInfo($params);
        return $result;
    }
}
