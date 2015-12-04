<?php
class sysrate_api_consultation_create{

    public $apiDescription = "商品咨询新增";
    public function getParams()
    {
        $result['params'] = array(
            'item_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'商品id'],
            'type' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询类型'],
            'content' => ['type'=>'string','valid'=>'required|max:200', 'default'=>'', 'example'=>'', 'description'=>'咨询内容'],
            'is_anonymity' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否匿名'],
            'user_id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人ID'],
            'user_name' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人'],
            'contack' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'联系方式'],
        );
        return $result;
    }

    public function create($params)
    {
        if($params['user_id'])
        {
            $params['author_id'] = $params['user_id'];
            unset($params['user_id']);
        }
        if($params['user_name'])
        {
            $params['author'] = $params['user_name'];
            unset($params['user_name']);
        }
        $params['consultation_type'] = $params['type'];
        unset($params['type']);

        $params['created_time'] = time();
        $params['modified_time'] = time();
        $objConsultation = kernel::single('sysrate_data_consultation');
        $result = $objConsultation->createConsultation($params);
        return $result;
    }
}

