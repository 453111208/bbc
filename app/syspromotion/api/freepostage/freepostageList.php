<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条免邮列表
 * promotion.freepostage.list
 */
final class syspromotion_api_freepostage_freepostageList {

    public $apiDescription = '获取多条免邮列表';

    public function getParams()
    {
        $return['params'] = array(
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认created_time asc'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'店铺ID,user_id和shop_id必填一个'],
            'freepostage_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'免邮id'],
            'freepostage_name' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'免邮名称'],
            'freepostage_status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'免邮状态'],
            'is_valid' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'可用免邮'],
            'is_cansend' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'可领取免邮'],
            'platform' => ['type'=>'int', 'valid'=>'in:pc,wap', 'default'=>'', 'example'=>'', 'description'=>'免邮适用平台'],
        );

        return $return;
    }

    /**
     * 获取免邮列表
     */
    public function freepostageList($params)
    {
        $objMdlCoupon = app::get('syspromotion')->model('freepostage');
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

        $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' freepostage_id DESC';
        $freepostageData = $objMdlCoupon->getList($params['fields'], $filter, $params['page_no'], $params['page_size'], $orderBy);
        $freepostageCount = $objMdlCoupon->count($filter);
        $result = array(
            'freepostages' => $freepostageData,
            'count' => $freepostageCount,
        );

        return $result;
    }

}

