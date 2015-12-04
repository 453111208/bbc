<?php
class sysshop_api_enterapply_get{
    public $apiDescription = "获取入驻申请信息";
    public function getParams()
    {
        $return['params'] = array(
            'seller_id' => ['type'=>'int','valid'=>'required','description'=>'入驻申请编号','default'=>'','example'=>''],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'入驻信息字段，多个用逗号隔开','default'=>'','example'=>''],
        );
        return $return;
    }
    public function get($params)
    {
        $row = $params['fields'] ? $params['fields'] : "*";
        $filter['seller_id'] = $params['seller_id'];
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $data = $objMdlEnterapply->getRow($row,$filter);
        return $data;
    }
}
