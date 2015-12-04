<?php

class sysuser_check{

    public function checkDelete($userIds)
    {
        $uIds = $userIds;
        if(is_array($userIds))
        {
            $uIds = implode(',',$userIds);
        }
        $filter['user_id'] = $uIds;
        $filter['status'] = 'WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS';
        $filter['fields'] = 'tid,user_id';
        $tradeCheck = app::get('sysuser')->rpcCall('trade.get.list',$filter);
        if($tradeCheck['count'] > 0)
        {
			throw new \LogicException(app::get('sysuser')->_('该会员有订单未处理'));
            return false;
        }

        $pointCheck = $this->pointCheck(array('user_id'=>$userIds));
        if($pointCheck)
        {
			throw new \LogicException(app::get('sysuser')->_('该会员有未使用的积分'));
            return false;
        }

        return true;
    }

    public function pointCheck($filter)
    {
        $noDel = array();
        $objMdlPoints = app::get('sysuser')->model('user_points');
        $points = $objMdlPoints->getList('point_count,user_id',$filter);
        if($points)
        {
            foreach($points as $key=>$val)
            {
                if($val['point_count'] > 0)
                {
                    $noDel[] = $val['user_id'];
                }
            }
        }
        return $noDel;
    }
}
