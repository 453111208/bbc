<?php
class sysuser_api_couponList {

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取用户优惠券列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     * user.coupon.list
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'page_no'   => ['type'=>'int',        'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1','default'=>'','example'=>''],
            'page_size' => ['type'=>'int',        'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认20条','default'=>'','example'=>''],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
            'orderBy'   => ['type'=>'string',     'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'排序','default'=>'','example'=>''],
            'user_id'   => ['type'=>'int',        'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'用户ID必填','default'=>'','example'=>''],
            'shop_id'   => ['type'=>'int',        'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'店铺ID','default'=>'','example'=>''],
            'is_valid'  => ['type'=>'int',        'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'获取是否有效的参数','default'=>'','example'=>''],
            'platform'  => ['type'=>'string',     'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'优惠券使用平台','default'=>'','example'=>''],
        );

        return $return;
    }

    public function couponList($params)
    {
        $objMdlUserCoupon = app::get('sysuser')->model('user_coupon');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }

        if($params['shop_id'])
        {
            $filter = array('user_id'=>$params['user_id'], 'shop_id'=>$params['shop_id']);
        }
        else
        {
            $filter = array('user_id'=>$params['user_id']);
        }
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

        if(isset($params['is_valid']))
        {
            $filter['is_valid'] = $params['is_valid'];
        }
        else
        {
            $filter['is_valid'] = array(0,1,2);
        }

        $orderBy  = $params['orderBy'] ? $params['orderBy'] : 'obtain_time DESC';
        $aData = $objMdlUserCoupon->getList($params['fields'], $filter, $params['page_no'],$params['page_size'], $orderBy);
        $couponData = $this->__getUserCouponList($aData);
        $itemCount = $objMdlUserCoupon->count($filter);
        $itemData = array(
                'coupons' => $couponData,
                'count' => $itemCount,
            );

        return $itemData;
    }


    private function __getUserCouponList($userCouponList)
    {
        $nowTime = time();
        foreach($userCouponList as &$v)
        {
            $apiData['user_id'] = $v['user_id'];
            $apiData['coupon_id'] = $v['coupon_id'];
            $couponRule = app::get('sysuser')->rpcCall('promotion.coupon.get', $apiData);
            //是否有效判断  jiapeng
            if( $nowTime > $couponRule['canuse_end_time'] )
            {
                $v['is_valid'] = 0;
            }
            $v['canuse_start_time'] = $couponRule['canuse_start_time'];
            $v['canuse_end_time'] = $couponRule['canuse_end_time'];
            $v['limit_money'] = $couponRule['limit_money'];
            $v['deduct_money'] = $couponRule['deduct_money'];
            $v['coupon_name'] = $couponRule['coupon_name'];
            $v['coupon_desc'] = $couponRule['coupon_desc'];
        }
        return $userCouponList;
    }

}
