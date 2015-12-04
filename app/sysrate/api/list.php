<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 */
class sysrate_api_list {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取评论列表';

    public function getParams()
    {
        $return['params'] = array(
            'role' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'调用角色'],

            //条件
            'item_id' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'评价的商品ID'],
            'item_title' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'评价的商品名称'],

            'rate_start_time' => ['type'=>'time','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉开始时间'],
            'rate_end_time' => ['type'=>'time','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉结束时间'],

            'is_content' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否有内容，true内容 false 全部'],
            'is_pic' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否有晒单 true 有晒图 false全部'],
            'is_reply' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否回复'],
            'result' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'评价结果'],
            'appeal_again' => ['type'=>'bool','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'再次申诉'],
            'appeal_start_time' => ['type'=>'time','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉开始时间'],
            'appeal_end_time' => ['type'=>'time','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉结束时间'],
            'appeal_status' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'申诉结果'],

            //分页参数
            'page_no' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认modified_time desc'],

            //返回字段
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'需要返回的字段'],
        );

        //如果参数fields中存在orders，则表示需要获取子订单的数据结构
        $return['extendsFields'] = ['appeal'];

        return $return;
    }

    public function getData($params)
    {
        $filter = $this->__filter($params);

        $data['trade_rates'] = array();
        $countTotal = app::get('sysrate')->model('traderate')->count($filter);
        if( $countTotal )
        {
            $pageTotal = ceil($countTotal/$params['page_size']);
            $page =  $params['page_no'] ? $params['page_no'] : 1;
            $limit = $params['page_size'] ? $params['page_size'] : 10;
            $currentPage = $pageTotal < $page ? $totalPage : $page;
            $offset = ($currentPage-1) * $limit;

            $orderBy = $params['orderBy'] ? $params['orderBy'] : 'modified_time desc';
            $data['trade_rates'] = app::get('sysrate')->model('traderate')->getList($params['fields']['rows'], $filter, $offset, $limit, $orderBy);

            if( isset($params['fields']['extends']['appeal']) )
            {
                foreach( (array)$data['trade_rates']  as $info)
                {
                    if( $info['appeal_status'] != 'NO_APPEAL' )
                    {
                        $rateIds[] = $info['rate_id'];
                    }
                }

                if( !empty($rateIds) )
                {
                    $appealData = app::get('sysrate')->model('appeal')->getList($params['fields']['extends']['appeal'], ['rate_id'=>$rateIds]);
                    $appealData = array_bind_key($appealData,'rate_id');
                }

                foreach((array)$data['trade_rates'] as $key=>$row )
                {
                    $data['trade_rates'][$key]['appeal'] = $appealData[$row['rate_id']] ? $appealData[$row['rate_id']] : [];
                }
            }
        }
        $data['total_results'] = $countTotal;
        return $data;
    }

    private function __filter($params)
    {
        $accountId = $params['oauth']['account_id'];

        if( $params['role'] == 'buyer' )
        {
            $filter['user_id'] = $accountId;
        }
        elseif( $params['role'] == 'seller' )
        {
            $shopId = app::get('sysrate')->rpcCall('shop.get.loginId',array('seller_id'=>$accountId),'seller');
            $filter['shop_id'] = $shopId;
        }

        if( $params['is_content'] )
        {
            $filter['content|noequal'] = '';
        }

        if( $params['is_pic'] )
        {
            $filter['rate_pic|noequal'] = '';
        }

        if( $params['appeal_status'] )
        {
            $filter['appeal_status'] = explode(',',$params['appeal_status']);
        }

        $filterFields = ['result','item_title','item_id','appeal_again'];
        foreach( $filterFields as $value )
        {
            if( isset($params[$value]) )
            {
                $filter[$value] = $params[$value];
            }
        }

        if( $params['appeal_start_time'] )
        {
            $filter['appeal_time|bthan'] = $params['appeal_start_time'];
        }

        if( $params['appeal_end_time'] )
        {
            $filter['appeal_time|sthan'] = $params['appeal_end_time'];
        }

        if( $params['rate_start_time'] )
        {
            $filter['created_time|bthan'] = $params['rate_start_time'];
        }

        if( $params['rate_end_time'] )
        {
            $filter['created_time|sthan'] = $params['rate_end_time'];
        }

        if( $params['is_reply'] )
        {
            $filter['is_reply'] = 1;
        }

        return $filter;
    }

}
