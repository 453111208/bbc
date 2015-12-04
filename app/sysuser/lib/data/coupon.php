<?php
class sysuser_data_coupon{

    function getUserCouponList($apiData)
    {
        $filter = array();
        if($apiData['shop_id'])
        {
            $filter['shop_id'] = $apiData['shop_id'];
        }
        $filter['user_id'] = $apiData['user_id'];
        $filter['is_valid'] = $apiData['is_valid'];
        $userCouponList = app::get('sysuser')->model('user_coupon')->getList('*',$filter);
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
        }
        return $userCouponList;
    }

    // 会员领取优惠券
    public function getCouponCode($coupon_id, $user_id){
        $objMdlUserCoupon = app::get('sysuser')->model('user_coupon');
        $couponNum = $objMdlUserCoupon->count(array('coupon_id'=>$coupon_id, 'user_id'=>$user_id));
        $oldQuantity = $couponNum ? $couponNum : 0;
        $userInfo = kernel::single('sysuser_passport')->memInfo($user_id);
        $apiData = array(
            'gen_quantity' => 1,
            'old_quantity' => $oldQuantity,
            'coupon_id' => $coupon_id,
            'grade_id' => $userInfo['grade_id'],
        );
        $db = app::get('sysuser')->database();
        $transaction_status = $db->beginTransaction();
        try
        {
            if($couponInfo = app::get('sysuser')->rpcCall('promotion.coupon.gencode', $apiData))
            {
                $userCoupon['coupon_id'] = $coupon_id;
                $userCoupon['coupon_code'] = $couponInfo['coupon_code'];
                $userCoupon['user_id'] = $user_id;
                $userCoupon['obtain_desc'] = '免费领取';
                $userCoupon['shop_id'] = $couponInfo['shop_id'];
                $userCoupon['obtain_time'] = time();
                $userCoupon['used_platform'] = $couponInfo['used_platform'];
                if( !app::get('sysuser')->model('user_coupon')->save($userCoupon) )
                {
                    throw new \LogicException('优惠券保存失败');
                }
                $db->commit($transaction_status);
                return $couponInfo;
            }
            else
            {
                throw new \LogicException('生成优惠券号码失败');
            }
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

}
