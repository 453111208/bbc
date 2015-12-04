<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_member_coupon extends topc_ctl_member {

    public function couponList()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'user_id'=>userAuth::id(),
        );
        $couponListData = app::get('topc')->rpcCall('user.coupon.list', $params, 'buyer');

        $count = $couponListData['count'];
        $couponList = $couponListData['coupons'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member_coupon@couponList',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['couponList']= $couponList;
        $pagedata['count'] = $count;
        $pagedata['action'] = 'topc_ctl_member_coupon@couponList';


        $this->action_view = "coupon/list.html";
        return $this->output($pagedata);
    }

}
