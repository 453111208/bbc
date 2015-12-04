<?php
class sysuser_api_grade_fullinfo{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员当前等级详细信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     * user.grade.fullinfo
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
        );

        return $return;
    }

    public function fullinfo($params)
    {
        $userId = $params['oauth']['account_id'];
        
        $filter['user_id'] = pamAccount::getAccountId();
        $objMdlUser = app::get('sysuser')->model('user');
        $objMdlUserGrade = app::get('sysuser')->model('user_grade');

        $userdata = $objMdlUser->getRow('experience,grade_id',$filter);

        $filter['grade_id'] = $userdata['grade_id'];
        $gradedata = $objMdlUserGrade->getList("grade_id,grade_name,grade_logo,experience,validity",'',0,-1,'experience ASC');
        foreach($gradedata as $key=>$value)
        {
            if($value['grade_id'] == $userdata['grade_id'])
            {
                $gradedata[$key]['current'] = "true";
                $userdata['gradename'] = $value['grade_name'];
                $currentLvExp = $value['experience'];
            }

            if( $value['grade_id'] != $userdata['grade_id'] && $value['experience'] < $userdata['experience'])
            {
                $gradedata[$key]['past'] = "true";
            }
            elseif($value['grade_id'] != $userdata['grade_id'])
            {
                $gradeval[] = $value['experience'];
            }
        }
        asort($gradeval);
        //下一个等级所需的经验值
        $nextExp = array_shift($gradeval);

        //当前经验值高出当前等级所需经验值的数量
        $a = $userdata['experience']-$currentLvExp;
        //当前等级的经验值与下一个等级的差距
        $b = $nextExp-$currentLvExp;

        //当前所有经验值与下一个等级的经验值的差距
        $lackExp = $nextExp-$userdata['experience'];
        $percentage = $a/$b*100;
        $userdata['lackExp'] = $lackExp < 0 ? 0 :$lackExp;
        $userdata['percentage'] = $percentage < 0 ? 0 : $percentage;
        $data['gradeList'] = $gradedata;
        if(!$userdata['gradename'])
        {
            $userdata['gradename'] = "注册会员";
        }
        $data['userlist'] = $userdata;
        return $data;
    }
}

