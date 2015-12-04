<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满减促销列表
 */
final class syspromotion_api_coupon_couponItemList{

    public $apiDescription = '获取指定优惠券促销商品列表';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠卷促销id'],
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'coupon_id asc'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
        );

        return $return;
    }

    /**
     * 获取满减促销列表
     */
    public function couponItemList($params)
    {
        if($params['coupon_id']=='')
        {
            return false;
        }
        $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
        $objMdlCoupon = app::get('syspromotion')->model('coupon');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $couponInfo = $objMdlCoupon->getRow('*',array('coupon_id'=>$params['coupon_id']));
        if($couponInfo['coupon_status']=='agree')
        {
            /*if($couponInfo['use_bound']==0)
            {
                $couponItem = app::get('syspromotion')->rpcCall('item.search',array('shop_id'=>$couponInfo['shop_id'],'fields'=>$params['fields']));

            }
            elseif($couponInfo['use_bound']==1)
            {*/
                $orderBy  = $params['orderBy'] ? $params['orderBy'] : ' coupon_id DESC';
                $filter = array('coupon_id'=>$params['coupon_id']);
                $couponItemList = $objMdlCouponItem->getList($params['fields'],$filter,$params['page_no'], $params['page_size'], $orderBy);
                $count = $objMdlCouponItem->count($filter);
                $couponItem = array(
                    'list'=>$couponItemList,
                    'total_found'=>$count,
                );
            //}
            $couponItem['promotionInfo'] = $couponInfo;
        }
        else
        {
            return false;
        }
        //echo '<pre>';print_r($fullminusItem);exit();
        return $couponItem;
    }


}

