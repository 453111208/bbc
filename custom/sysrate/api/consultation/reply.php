<?php
class sysrate_api_consultation_reply{
    public $apiDescription = "咨询回复";
    public function getParams()
    {
        $result['params'] = array(
            'id' => ['type'=>'int','valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'被回复的咨询id'],
            'content' => ['type'=>'string','valid'=>'required|max:300', 'default'=>'', 'example'=>'', 'description'=>'咨询内容'],
            'author_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人ID'],
            'author' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人'],
            'shop_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
        );
        return $result;
    }

    public function doReply($params)
    {
        $objConsultation = kernel::single('sysrate_data_consultation');
        $result = $objConsultation->doReply($params);
        return $result;
    }
}
