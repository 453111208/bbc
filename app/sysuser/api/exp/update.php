<?php
class sysuser_api_exp_update{

    /**
     * 接口作用说明
     */
    public $apiDescription = '更新会员的成长总值';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'会员ID'],
            'type' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'行为类型("增加","减少")'],
            'num' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'成长值数量'],
            'behavior' =>['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'行为描述'],
            'remark' =>['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'备注'],

        );

        return $return;
    }

    public function updateUserExp($params)
    {
        $objMdlUser = app::get('sysuser')->model('user');
        $objMdlUserExp = app::get('sysuser')->model('user_experience');

        $row = $objMdlUser->getRow('experience',array('user_id'=>$params['user_id']));
        switch($params['type'])
        {
        case "obtain":
            $params['experience'] = ceil($row['experience'] + $params['num']);
            break;
        case "consume":
            $params['experience'] = ceil($row['experience'] - $params['num']);
            break;
        }

        $paramsExp['experience'] = ceil($params['num']);
        $paramsExp['behavior_type'] = $params['type'];
        $paramsExp['behavior'] = $params['behavior'];
        $paramsExp['remark'] = $params['remark'];
        $paramsExp['user_id'] = $params['user_id'];
        $paramsExp['modified_time'] = time();

        unset($params['type'],$params['num'],$params['behavior'],$params['remark']);

        if($params['experience'] < 0) $params['experience'] = 0;

        $objGrade = kernel::single('sysuser_grade');
        $params['grade_id'] = $objGrade->upgrade($params['experience']);

        $db = app::get('sysuser')->database();
        $db->beginTransaction();

        try
        {
            $result = $objMdlUser->save($params);
            if(!$result)
            {
                throw new \LogicException('会员保存失败');
            }
            $result = $objMdlUserExp->save($paramsExp);
            if(!$result)
            {
                throw new \LogicException('会员经验值保存失败');
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }

        return $result;
    }
}
