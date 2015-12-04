<?php
class syslogistics_api_dlycorp_getlist{
    public $apiDescription = "获取物流公司列表";
    public function getParams()
    {
        $return['params'] = array(
            'corp_id' =>['type'=>'string','valid'=>'', 'description'=>'物流公司编号id','default'=>'','example'=>'1'],
            'page_no' => ['type'=>'int','valid'=>'','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'','description'=>'分页每页条数(1<=size<=1000)','example'=>'','default'=>'500'],
            'order_by' => ['type'=>'int','valid'=>'','description'=>'排序方式','example'=>'','default'=>' order_sort asc'],
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

        //分页使用
        $pageSize = $params['page_size'] ? $params['page_size'] : 500;
        $pageNo = $params['page_no'] ? $params['page_no'] : 1;
        $max = 1000000;
        if($pageSize >= 1 && $pageSize < 1000 && $pageNo >=1 && $pageNo < 500 && $pageSize*$pageNo < $max)
        {
            $limit = $pageSize;
            $page = ($pageNo-1)*$limit;
        }

        $orderBy = $params['orderBy'];
        if(!$params['orderBy'])
        {
            $orderBy = "order_sort asc";
        }

        $objDataDlycorp = kernel::single('syslogistics_data_dlycorp');
        $pagedata = $objDataDlycorp->fetchDlycorp($row,$filter,$page,$limit);
        return $pagedata;
    }
}
