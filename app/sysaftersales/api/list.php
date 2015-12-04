<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取单条售后申请数据
 */
class sysaftersales_api_list {

     /**
     * 接口作用说明
     */
   public $apiDescription = '获取售后列表';

   public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'', 'description'=>'会员ID,user_id和shop_id必填一个'],
            'shop_id' => ['type'=>'int','valid'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'tid' => ['type'=>'string','valid'=>'', 'description'=>'订单编号'],
            'title' => ['type'=>'string','valid'=>'', 'description'=>'商品名称'],
            'created_time' => ['type'=>'json_encode','valid'=>'', 'description'=>'申请时间范围'],
            'aftersales_bn' => ['type'=>'int','valid'=>'', 'description'=>'退换货编号'],
            'aftersales_type' => ['type'=>'string','valid'=>'', 'description'=>'退换货类型'],
            'progress' => ['type'=>'int','valid'=>'', 'description'=>'退换货处理进度'],
            'page_no' => ['type'=>'int','valid'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string','valid'=>'', 'description'=>'排序，默认modified_time desc'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'description'=>'获取单条售后需要返回的字段'],
        );

        $return['extendsFields'] = ['sku'];

        return $return;
    }

    /**
     * 获取单条申请售后服务信息
     */
    public function getData($params)
    {
        $filterFields = ['title','user_id','shop_id','tid','created_time','aftersales_bn','aftersales_type','progress'];
        foreach( $filterFields as $value )
        {
            if( isset($params[$value]) )
            {
                $filter[$value] = $params[$value];
            }
        }
        if($filter['created_time'])
        {
            $times = json_decode($filter['created_time'],true);
            if(array_filter($times))
            {
                $filter['created_time|bthan'] = strtotime($times['0']);
                $filter['created_time|sthan'] = strtotime($times['1'])+86400;
                unset($filter['created_time']);
            }
        }
        $page = ($params['page_no'] >= 1 ) ? $params['page_no'] : 1;
        $limit = $params['page_size'] ? $params['page_size'] : 10;
        if(!$params['orderBy'])
        {
            $params['orderBy'] = "created_time desc";
        }
        return kernel::single('sysaftersales_data')->getAftersalesList($params['fields'], $filter, $page, $limit, $params['orderBy']);
    }

}

