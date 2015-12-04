<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满减促销列表
 */
final class syspromotion_api_fullminus_fullminusItemList{

    public $apiDescription = '获取多条满减促销商品列表';

    public function getParams()
    {
        $return['params'] = array(
            'fullminus_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'满减促销id'],
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
    public function fullminusItemList($params)
    {
        if($params['fullminus_id']=='')
        {
            return false;
        }
        $objMdlFullminusItem = app::get('syspromotion')->model('fullminus_item');
        $objMdlFullminus = app::get('syspromotion')->model('fullminus');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $fullminusInfo = $objMdlFullminus->getRow('*',array('fullminus_id'=>$params['fullminus_id']));
        if($fullminusInfo['fullminus_status']=='agree')
        {
            /*if($fullminusInfo['use_bound']==0)
            {
                $fullminusItem = app::get('syspromotion')->rpcCall('item.search',array('shop_id'=>$fullminusInfo['shop_id'],'fields'=>$params['fields']));

            }
            elseif($fullminusInfo['use_bound']==1)
            {*/
                $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' fullminus_id DESC';
                $filter = array('fullminus_id'=>$params['fullminus_id']);
                $fullminusItemList = $objMdlFullminusItem->getList($params['fields'],$filter,$params['page_no'], $params['page_size'], $orderBy);
                $count = $objMdlFullminusItem->count($filter);
                $fullminusItem = array(
                    'list'=>$fullminusItemList,
                    'total_found'=>$count,
                );
            //}
            $fullminusItem['promotionInfo'] = $fullminusInfo;
        }
        else
        {
            return false;
        }
        //echo '<pre>';print_r($fullminusItem);exit();
        return $fullminusItem;
    }


}

