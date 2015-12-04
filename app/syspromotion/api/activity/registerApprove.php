<?php
/**
 * 报名审核接口
 * promotion.activity.register.approve
 */
class syspromotion_api_activity_registerApprove{

    public $apiDescription = "报名审核";

    public function getParams()
    {
        $data['params'] = array(
            'activity_id' => ['type'=>'int', 'valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'活动id'],
            'shop_id' => ['type'=>'int', 'valid'=>'required|int', 'default'=>'', 'example'=>'', 'description'=>'店铺id'],
            'status' => ['type'=>'string', 'valid'=>'in:agree,refuse', 'default'=>'', 'example'=>'', 'description'=>'审核状态'],
            'reason' => ['type'=>'string', 'valid'=>'required_if:status,refuse', 'default'=>'', 'example'=>'', 'description'=>'驳回原因'],
        );
        return $data;
    }

    public function registerApprove($params)
    {
        $objMdlRegister = app::get('syspromotion')->model('activity_register');
        $objMdlItem = app::get('syspromotion')->model('activity_item');
        $objMdlactivity = app::get('syspromotion')->model('activity');
        $filter = array('activity_id'=>$params['activity_id'], 'shop_id'=>$params['shop_id']);
        $registerInfo = $objMdlRegister->getRow('*', $filter);

        $activityInfo = $objMdlactivity->getRow('release_time', array('activity_id'=>$params['activity_id']));
        $nowTime = time();
        

        // 审批通过
        if($params['status'] == 'agree')
        {
            if($registerInfo['verify_status'] == 'agree') return true;
            $filter = array('activity_id'=>$params['activity_id'], 'shop_id'=>$params['shop_id']);
            $db = app::get('syspromotion')->database();
            $db->beginTransaction();
            try
            {
                if($nowTime > $activityInfo['release_time'])
                {
                    throw \LogicException('发布时间已过，不可以对其活动进行操作！');
                }
                if( !$objMdlRegister->update(array('verify_status'=>'agree'), $filter) )
                {
                    throw \LogicException('活动保存失败');
                }
                if( !$objMdlItem->update(array('verify_status'=>'agree'), $filter) )
                {
                    throw \LogicException('活动保存失败');
                }
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw $e;
            }
        }

        // 审批驳回
        if($params['status'] == 'refuse')
        {
            if($registerInfo['verify_status'] == 'refuse') return true;
            $filter = array('activity_id'=>$params['activity_id'], 'shop_id'=>$params['shop_id']);
            $db = app::get('syspromotion')->database();
            $db->beginTransaction();
            try
            {
                if($nowTime > $activityInfo['release_time'])
                {
                    throw \LogicException('发布时间已过，不可以对其活动进行操作！');
                }
                if( !$objMdlRegister->update(array('verify_status'=>'refuse', 'refuse_reason'=>$params['reason']), $filter) )
                {
                    throw \LogicException('活动保存失败');
                }
                if( !$objMdlItem->update(array('verify_status'=>'refuse'), $filter) )
                {
                    throw \LogicException('活动保存失败');
                }
                $db->commit();
                return true;
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw $e;
            }
        }

        return true;
    }
}
