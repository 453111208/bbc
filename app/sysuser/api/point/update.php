<?php
class sysuser_api_point_update{

    /**
     * 接口作用说明
     */
    public $apiDescription = '更新会员的积分总值';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'会员ID'],
            'type' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'积分记录类型("获得","消费")'],
            'num' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'积分数量'],
            'behavior' =>['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'积分行为'],
            'remark' =>['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'备注'],
        );

        return $return;
    }

    public function updateUserPoint($params)
    {
        $objMdlUserPoint = app::get('sysuser')->model('user_points');
        $objMdlUserPointLog = app::get('sysuser')->model('user_pointlog');

        $row = $objMdlUserPoint->getRow('point_count',array('user_id'=>$params['user_id']));
        switch($params['type'])
        {
        case "obtain":
            $params['point_count'] = ceil($row['point_count'] + $params['num']);
            break;
        case "consume":
            $params['point_count'] = ceil($row['point_count'] - $params['num']);
            break;
        }

        $paramsPoint['point'] = ceil($params['num']);
        $paramsPoint['behavior_type'] = $params['type'];
        $paramsPoint['behavior'] = $params['behavior'];
        $paramsPoint['remark'] = $params['remark'];
        $paramsPoint['user_id'] = $params['user_id'];
        $paramsPoint['modified_time'] = $params['modified_time'] = time();

        unset($params['type'],$params['num'],$params['behavior'],$params['remark']);
        if($params['point_count'] < 0) $params['point_count'] = 0;


        $db = app::get('sysuser')->database();
        $db->beginTransaction();
        try
        {
            if(!$objMdlUserPoint->save($params))
            {
                throw new \LogicException('会员保存失败');
            }

            if(!$objMdlUserPointLog->save($paramsPoint))
            {
                throw new \LogicException('会员积分保存失败');
            }

            $db->commit();

        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return true;
    }

}
