<?php

class importexport_ctl_admin_system extends importexport_controller{

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * 显示导入导出存储配置参数文件页面
     */
    public function setting()
    {
        $storage = $this->storage_policy();
        $pagedata['policy'] = $storage['policy']; //存储类型
        $pagedata['params'] = $storage['params']; //页面调用参数
        $pagedata[$storage['var_server_params']] = $this->get_storage_params();//配置参数
        return $this->page($storage['view']['html'], $pagedata);
    }

    /**
     * 保存导入导出存储方式配置参数
     */
    public function save(){
        $this->begin();
        if( $this->set_storage_params($_POST) ){
            $this->adminlog("编辑导入导出存储方式配置", 1);
            $this->end(true,app::get('importexport')->_('保存成功'));
        }else{
            $this->adminlog("导入导出存储方式配置", 0);
            $this->end(false,app::get('importexport')->_('保存失败'));
        }
    }

    public function check(){
        $this->set_storage_params($_GET);
        $policyObj = kernel::single('importexport_policy');
        $this->begin();
        try{
            $ret = $policyObj->check();
        }
        catch( Exception $e)
        {
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        if($ret === true){
            $this->end(true,app::get('importexport')->_('检查通过'));
        }else{
            $this->end(false,$ret);
        }
    }


}
