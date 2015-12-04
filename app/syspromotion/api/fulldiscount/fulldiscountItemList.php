<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满减促销列表
 */
final class syspromotion_api_fulldiscount_fulldiscountItemList{

    public $apiDescription = '获取多条满折促销商品列表';

    public function getParams()
    {
        $return['params'] = array(
            'fulldiscount_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'满折促销id'],
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序，默认created_time asc'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
        );

        return $return;
    }

    /**
     * 获取满减促销列表
     */
    public function fulldiscountItemList($params)
    {
        if($params['fulldiscount_id']=='')
        {
            return false;
        }
        $objMdlFulldiscountItem = app::get('syspromotion')->model('fulldiscount_item');
        $objMdlFulldiscount = app::get('syspromotion')->model('fulldiscount');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $fulldiscountInfo = $objMdlFulldiscount->getRow('*',array('fulldiscount_id'=>$params['fulldiscount_id']));
        if($fulldiscountInfo['fulldiscount_status']=='agree')
        {
            /*if($fulldiscountInfo['use_bound']==0)
            {
                $fulldiscountItem = app::get('syspromotion')->rpcCall('item.search',array('shop_id'=>$fulldiscountInfo['shop_id'],'fields'=>$params['fields']));

            }
            elseif($fulldiscountInfo['use_bound']==1)
            {*/
                $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' fulldiscount_id DESC';
                $filter = array('fulldiscount_id'=>$params['fulldiscount_id']);
                $fulldiscountItemList = $objMdlFulldiscountItem->getList($params['fields'],$filter,$params['page_no'], $params['page_size'], $orderBy);
                $count = $objMdlFulldiscountItem->count($filter);
                $fulldiscountItem = array(
                    'list'=>$fulldiscountItemList,
                    'total_found'=>$count,
                );
            //}
            $fulldiscountItem['promotionInfo'] = $fulldiscountInfo;
        }
        else
        {
            return false;
        }
        //echo '<pre>';print_r($fullminusItem);exit();
        return $fulldiscountItem;
    }


}

