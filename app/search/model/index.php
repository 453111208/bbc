<?php

class search_mdl_index extends dbeav_model {

    public function get_schema(){
        $schema = array (
            'columns' => array (
                'index_name' => array (
                    'type' => 'varchar(100)',
                    'pkey' => true,
                    'label' => app::get('search')->_('索引名称'),
                    'width' => 150,
                    'order' => 100,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
                'index_type' => array (
                    'type' => 'varchar(50)',
                    'label' => app::get('search')->_('索引类型'),
                    'width' => 100,
                    'order' => 200,
                    'in_list' => true,
                    'default_in_list' => true,
                ),
            ),
            'idColumn' => 'index_name',
            'in_list' => array (
                0 => 'index_name',
                1 => 'index_type',
            ),
            'default_in_list' => array (
                0 => 'index_name',
                1 => 'index_type',
            ),
        );
        return $schema;
    }

    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null)
    {
        $server = app::get('search')->getConf('search_server_policy');
        $obj = kernel::single($server);
        $data = $obj->getIndex('show tables');
        return $data;
    }

    public function count($filter=null)
    {
        $server = app::get('search')->getConf('search_server_policy');
        $obj = kernel::single($server);
        $data = $obj->getIndex('show tables');
        return count($data);
    }
}//End Class

