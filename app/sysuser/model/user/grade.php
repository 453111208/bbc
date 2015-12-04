<?php
class sysuser_mdl_user_grade extends dbeav_model{

    public function delete($filter,$subSdf = 'delete')
    {
        $info = $this->getList('grade_name,default_grade',$filter);
        foreach($info as $value)
        {
            if($value['default_grade'] == 1)
            {
                throw new \LogicException("[".$value['grade_name']."]是系统等级，不可删除");
                return false;
            }

        }
        return parent::delete($filter);
    }
}
