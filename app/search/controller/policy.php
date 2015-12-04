<?php

class search_ctl_policy extends desktop_controller {

    public function index()
    {
        if( $policy = app::get('search')->getConf('search_server_policy') )
        {
            $obj = kernel::single($policy);
            if( !$obj->status($msg) )
            {
                app::get('search')->setConf('search_server_policy','');
            }
        }
        return $this->finder(
            'search_mdl_policy', array(
                'title' =>  app::get('search')->_('搜索引擎管理'),
                'base_filter' => array(),
                'use_buildin_set_tag' => false,
                'use_buildin_export' => false,
                'use_buildin_selectrow'=>false,
            )
        );
    }

    //开启搜索
    public function setDefault()
    {
        $this->begin('?app=search&ctl=policy&act=index');
        $method = $_GET['method'];
        $name = $_GET['name'];
        if($method == 'open')
        {
            $obj = kernel::single($name);
            if( !$obj->status($msg) )
            {
                $this->end(false, $this->app->_('连接异常，请先确认是否连接'));
            }
            app::get('search')->setConf('search_server_policy',$name);
        }
        $this->adminlog("修改默认搜索引擎", 1);
        $this->end(true, $this->app->_('保存成功'));
    }
}

