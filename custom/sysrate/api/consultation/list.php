<?php
class sysrate_api_consultation_list{
    public $apiDescription = "获取咨询列表";
    public function getParams()
    {
        $result['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'商品id'],
            'shop_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
            'user_id' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'消费者id'],
            'type' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'咨询类型'],
            'is_reply' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否回复'],
            //分页参数
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认modified_time desc'],

            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要返回的字段'],
        );
        return $result;
    }
    public function getData($params)
    {
        if(isset($params['item_id']))
        {
            $filter['item_id'] = intval($params['item_id']);
            $filter['is_display'] = 'true';
            $reply['is_display'] = 'true';
        }

        if(isset($params['shop_id']))
        {
            $filter['shop_id'] = intval($params['shop_id']);
            $reply['is_display'] = array('true','false');
        }

        if(isset($params['user_id']))
        {
            $filter['author_id'] = intval($params['user_id']);
            $reply['is_display'] = 'true';
        }

        $filter['be_reply_id'] = 0;

        if(isset($params['is_reply']))
        {
            $filter['is_reply'] = $params['is_reply'] ? $params['is_reply'] : 0 ;
        }

        if(!$filter['item_id'] && !$filter['shop_id'] && !$filter['author_id'])
        {
            throw new LogicException('参数item_id、shop_id、user_id 至少有一项必填');
        }

        if($params['type'])
        {
            $filter['consultation_type'] = $params['type'];
        }

        $row = $params['fields'] ? $params['fields'] : "*";

        $objMdlConsultation = app::get('sysrate')->model('consultation');
        $countTotal = $objMdlConsultation->count($filter);

        if($countTotal)
        {
            $pageTotal = ceil($countTotal/$params['page_size']);
        }
        $page =  $params['page_no'] ? $params['page_no'] : 1;
        $limit = $params['page_size'] ? $params['page_size'] : 10;
        $currentPage = $pageTotal < $page ? $pageTotal : $page;
        $offset = ($currentPage-1) * $limit;

        $orderBy = $params['orderBy'] ? $params['orderBy'] : 'created_time desc';

        $filter['reply'] = $reply;
        $objConsultation = kernel::single('sysrate_data_consultation');
        $listdata = $objConsultation->getConsultation($row,$filter,$offset,$limit,$orderBy);
        return array('total_results' => $countTotal,'lists' => $listdata);
    }
}
