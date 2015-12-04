<?php
class sysrate_api_consultation_update{
    public $apiDescription = "更新咨询、回复";
    public function getParams()
    {
        $return['params'] = array(
            'id' => ['type'=>'int','valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'id'],
            'shop_id' => ['type'=>'int','valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>' 所属店铺'],
            'display' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'是否显示'],
        );
        return $return;
    }
    public function update($params)
    {
        $id = $params['id'];
        $status = $params['display'];
        $objConsultation = kernel::single('sysrate_data_consultation');
        $result = $objConsultation->doDisplay($id,$status);
        return $result;
    }
}
