<?php

class search_ctl_index extends desktop_controller {

    public function index()
    {
        if( !$policy = app::get('search')->getConf('search_server_policy') )
        {
            $this->begin();
            $msg = app::get('search')->_('请先在搜索引擎管理中开启一个搜索方式');
            $this->end(false,$msg);
        }
        if( $policy == 'search_policy_mysql' )
        {
            $this->begin();
            $msg = app::get('search')->_('mysql搜索不需要进行索引管理');
            $this->end(true,$msg);
        }
        return $this->finder(
            'search_mdl_index',
            array(
                'title' =>  app::get('search')->_('索引管理'),
                'base_filter' => array(),
                'use_buildin_set_tag' => false,
                'use_buildin_export' => false,
                'use_buildin_selectrow'=>false,
            )
        );
    }

    /**
     * @brief 根据索引名称配置索引搜索所需要的参数
     *
     * @param string $indexName
     *
     * @return bool
     */
    public function setting($indexName)
    {
        $policy = app::get('search')->getConf('search_server_policy');
        $obj = kernel::single($policy);

        if( $_POST )
        {
            $this->begin();
            $indexName = $_POST['indexName'];
            $params['ranker'] = $_POST['ranker'];
            $params['order_value'] = $_POST['order_value'];
            $params['order_type'] = $_POST['order_type'];
            $params['max_limit'] = intval($_POST['max_limit']);
            $flag = $obj->setIndexParams($indexName, $params);
            $this->adminlog("编辑搜索索引配置", 1);
            $this->end(true, $this->app->_('保存成功'));exit;
        }

        $pagedata = $obj->getIndexParams($indexName);
        $pagedata['indexName'] = $indexName;

        return $this->page('search/config/default.html', $pagedata);
    }
}

