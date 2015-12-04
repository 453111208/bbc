<?php
class sysuser_api_grade_list{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取会员当前等级列表';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
        );

        return $return;
    }

    public function gradeList($params)
    {
        $objMdlUserGrade = app::get('sysuser')->model('user_grade');
        $gradedata = $objMdlUserGrade->getList("grade_id,grade_name,grade_logo,experience,validity");

        return $gradedata;
    }

}
