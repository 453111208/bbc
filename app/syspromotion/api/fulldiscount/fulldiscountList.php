<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满折促销列表
 */
final class syspromotion_api_fulldiscount_fulldiscountList {

    public $apiDescription = '获取多条满折促销列表';

    public function getParams()
    {
        $return['params'] = array(
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认created_time asc'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID'],
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销id'],
            'fulldiscount_name' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销名称'],
            'fulldiscount_status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销状态'],
            'is_valid' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'是否生效的满折促销'],
            'platform' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'满折促销适用平台'],
        );

        return $return;
    }


    /**
     * 获取满折促销列表
     */
    public function fulldiscountList($params)
    {
        $objMdlFulldiscount = app::get('syspromotion')->model('fulldiscount');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $filter = array('shop_id'=>$params['shop_id']);

        // 平台未选择则默认全选
        if( $params['platform'] == 'pc' )
        {
            $filter['used_platform'] = array('0', '1');
        }
        elseif( $params['platform'] == 'wap' )
        {
            $filter['used_platform'] = array('0', '2');
        }
        else
        {
            $filter['used_platform'] = array('0','1','2');
        }
        // 获取有效可使用的折扣
        if($params['is_valid'])
        {
            $filter['start_time|lthan'] = time();
            $filter['end_time|than'] = time();
        }

        $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' fulldiscount_id DESC';
        $fulldiscountData = $objMdlFulldiscount->getList($params['fields'], $filter, $params['page_no'], $params['page_size'], $orderBy);
        $fulldiscountTotal = $objMdlFulldiscount->count($filter);
        $result = array(
            'data' => $fulldiscountData,
            'total' => $fulldiscountTotal,
        );

        return $result;
    }


}

