<?php
class sysrate_api_consultation_delete{
    public $apiDescription = "删除咨询、回复";
    public function getParams()
    {
        $result['params'] = array(
            'id' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'id'],
            'user_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'咨询人ID'],
            'shop_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
        );
        return $result;
    }
    public function deleteConsultation($params)
    {
        $id = explode(',',$params['id']);
        if(count($id) == 1)
        {
            $id = $params['id'];
        }
        if($params['user_id'])
        {
            $type = "consultation";
        }
        elseif($params['shop_id'])
        {
            $type = "reply";
        }
        $objConsultation = kernel::single('sysrate_data_consultation');
        $result = $objConsultation->doDelete($id,$type);
        return $result;

    }
}
