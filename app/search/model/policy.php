<?php

class search_mdl_policy extends dbeav_model {

    public function count($filter=null)
    {
        $setting = config::get('search.server');
        return count($setting);
    }

    /**
     * @brief 获取表名称
     *
     * @param bool $real 是否返回表的全名
     *
     * @return string
     */
    public function table_name($real=false)
    {
        if($real)
        {
            return $this->app->app_id.'_policy';
        }
        else
        {
            return 'policy';
        }
    }

    public function get_schema()
    {
        $schema = array (
            'columns' => array (
                'app_id' => array (
                    'type' => 'varchar(50)',
                    'label' => app::get('search')->_('搜索提供类'),
                ),
            ),
        );
        return $schema;
    }

    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null)
    {
        $setting = config::get('search.server');
        foreach( (array)$setting as $key=>$row )
        {
            $data[$key] = array(
                'app_id' => $row,
            );
        }
        return $data;
    }
}//End Class

