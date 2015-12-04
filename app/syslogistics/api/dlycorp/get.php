<?php
class syslogistics_api_dlycorp_get{
    public $apiDescription = "获取物流公司列表";

    public function getParams()
    {
        $return['params'] = array(
            'corp_id' =>['type'=>'string','valid'=>'', 'description'=>'物流公司编号id','default'=>'','example'=>'1'],
            'fields' => ['type'=>'field_list','valid'=>'', 'description'=>'获取指定字段','default'=>'corp_id,corp_code,corp_name','example'=>'corp_id,corp_code,corp_name'],
        );
        return $return;
    }

    public function getList($params)
    {
        //默认无条件
        $filter = array();
        if($params['corp_id'])
        {
            $filter['corp_id'] = $params['corp_id'];
        }

        //默认查询字段
        $row = "corp_id,corp_code,corp_name";
        if($params['fields'])
        {
            $row = $params['fields'];
        }

        $objMdlDlycorp = app::get('syslogistics')->model('dlycorp');
        $pagedata = $objMdlDlycorp->getRow($row,$filter);
        return $pagedata;
    }
}
