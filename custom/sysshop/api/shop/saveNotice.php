<?php
class sysshop_api_shop_saveNotice{

    public $apiDescription = "保存企业通知";
    public function getParams()
    {
        $return['params'] = array(
            'notice_id' => ['type'=>'string','valid'=>'','description'=>'企业id','default'=>'','example'=>''],
            'notice_title' => ['type'=>'string','valid'=>'required','description'=>'企业通知标题','default'=>'','example'=>'http://img0.cn/aa.jpg'],
            'notice_content' => ['type'=>'string','valid'=>'required','description'=>'企业通知内容','default'=>'','example'=>""],
            'notice_type' => ['type'=>'string','valid'=>'required','description'=>'企业通知类型','default'=>'','example'=>''],
            'shop_id' => ['type'=>'string','valid'=>'','description'=>'企业id','default'=>'','example'=>''],
        );
        return $return;
    }
    public function saveNotice($params)
    {
        $shopNoticeLib = kernel::single('sysshop_data_shopnotice');
        $result = $shopNoticeLib->saveShopNotice($params);
        return $result;
    }
}
