<?php class sysuser_data_user_points{


	/**
	 * 处理会员过期积分
	 *
	 * @param int $userId
	 * @return bool
	 */
    public function pointExpiredCount($userId=null)
    {
        $expiredMonth = app::get('sysconf')->getConf('point.expired.month');
        $expiredMonth = $expiredMonth ? $expiredMonth : 12;
        $expiredTime = strtotime(date('Y-'.$expiredMonth.'-01 23:59:59')." +1 month -1 day");
        //error_log(date('Y-m-d H:i:s',$expiredTime)."------\n",3,DATA_DIR."/bbb.log");
        if(time() >= $expiredTime)
        {
            $objMdlUserPoints = app::get('sysuser')->model('user_points');
            $objMdlUserPointlog = app::get('sysuser')->model('user_pointlog');
            $userPoints = $objMdlUserPoints->getRow('user_id,point_count,expired_point',array('user_id'=>$userId));
            $userPoints['expired_point'] = $userPoints['point_count'] = $userPoints['point_count']-$userPoints['expired_point'];
            $userPoints['modified_time'] = time();
            $db = app::get('sysuser')->database();
            $db->beginTransaction();
            try
            {
                $result = $objMdlUserPoints->save($userPoints);
                $result = $objMdlUserPointlog->delete(array('user_id'=>$userId,'modified_time|sthan'=>$expiredTime));
            }
            catch(\LogicException $e)
            {
                $db->rollback();
                $msg = $e->getMessage();
                logger::info('point_expired:'.$msg);
                return false;
            }
            $db->commit();
            return true;
        }
        return true;
    }
}
