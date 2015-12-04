<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满减促销列表
 */
final class syspromotion_api_xydiscount_xydiscountItemList{

    public $apiDescription = '获取多条xy促销商品列表';

    public function getParams()
    {
        $return['params'] = array(
            'xydiscount_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'xy促销id'],
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'xydiscount_id asc'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
        );

        return $return;
    }

    /**
     * 获取满减促销列表
     */
    public function xydiscountItemList($params)
    {
        if($params['xydiscount_id']=='')
        {
            return false;
        }
        $objMdlXydiscountItem = app::get('syspromotion')->model('xydiscount_item');
        $objMdlXydiscount = app::get('syspromotion')->model('xydiscount');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $xydiscountInfo = $objMdlXydiscount->getRow('*',array('xydiscount_id'=>$params['xydiscount_id']));
        if($xydiscountInfo['xydiscount_status']=='agree')
        {
            /*if($xydiscountInfo['use_bound']==0)
            {
                $xydiscountItem = app::get('syspromotion')->rpcCall('item.search',array('shop_id'=>$xydiscountInfo['shop_id'],'fields'=>$params['fields']));

            }
            elseif($xydiscountInfo['use_bound']==1)
            {*/
                $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' xydiscount_id DESC';
                $filter = array('xydiscount_id'=>$params['xydiscount_id']);
                $xydiscountItemList = $objMdlXydiscountItem->getList($params['fields'],$filter,$params['page_no'], $params['page_size'], $orderBy);
                $count = $objMdlXydiscountItem->count($filter);
                $xydiscountItem = array(
                    'list'=>$xydiscountItemList,
                    'total_found'=>$count,
                );
            //}
            $xydiscountItem['promotionInfo'] = $xydiscountInfo;
        }
        else
        {
            return false;
        }
        //echo '<pre>';print_r($fullminusItem);exit();
        return $xydiscountItem;
    }


}

