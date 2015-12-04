<?php
class sysuser_grade{

    /**
     * @brief 保存会员等级
     *
     * @param $gradeData
     * @param $msg
     *
     * @return
     */
    public function saveGrade($gradeData,&$msg)
    {
        $this->_check($gradeData);

        $objMdlGrade = app::get('sysuser')->model('user_grade');
        if($objMdlGrade->count() <= 0 ) $gradeData['default_grade'] = 1;

        $result = $objMdlGrade->save($gradeData);
        if(!$result)
        {
            throw new \LogicException('保存失败');
            return false;
        }
        return true;
    }

    /**
     * @brief 根据成长值获取会员当前的等级id
     *
     * @param $experience
     *
     * @return
     */
    public function upgrade($experience)
    {
        $objMdlGrade = app::get('sysuser')->model('user_grade');
        $filter['experience|sthan'] = $experience;
        $result = $objMdlGrade->getList("experience,grade_id",$filter,0,-1,'experience desc');
        return $result[0]['grade_id'];
    }

    /**
     * @brief 检测会员等级信息的合法性
     *
     * @param $postdata
     *
     * @return
     */
    private function _check(&$postdata)
    {
        $objMdlGrade = app::get('sysuser')->model('user_grade');
        if($postdata['grade_name'] && !$postdata['grade_id'])
        {
            $count = $objMdlGrade->count();
            if($count >8)
            {
                throw new \LogicException('等级总数不能超过8个');
                return false;
            }

            $list = $objMdlGrade->getList('grade_id',array('grade_name'=>$postdata['grade_name']));
            if($list){
                throw new \LogicException('该等级名称已经存在');
                return false;
            }
        }

        if($postdata['default_grade'] == 1 && $postdata['experience'] !=0)
        {
            throw new \LogicException('该等级为默认等级，经验值必须为0');
            return false;
        }
        else
        {
            $isDefault = $objMdlGrade->getRow('grade_id,experience',array('default_grade'=>1));
            if(!$isDefault)
            {
                $postdata['default_grade'] = 1;
                $postdata['experience'] = 0;
            }
        }
        return true;
    }
}
