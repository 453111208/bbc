<?php
class sysrate_mdl_traderate extends dbeav_model{

    public $defaultOrder = array('modified_time','DESC');

    public function _filter($filter,$tableAlias=null,$baseWhere=null)
    {

        if( is_array($filter) && !$filter['disabled'] )//默认只取出有效的评价，删除评价是将此字段修改为1
        {
            $filter['disabled'] = 0;
        }

        $filter = parent::_filter($filter,$tableAlias,$baseWhere);
        return $filter;
    }
}

