<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_ctl_debug extends desktop_controller{

    function index() {
        $this->path[] = array('text'=>app::get('desktop')->_('数据备份'));
        if($time = app::get('shopex')->getConf("system.last_backup")){
            $pagedata['time'] = date('Y-m-d H:i:s',$time);
        }
        $pagedata['debug'] = 'current';
        kernel::single("desktop_ctl_data")->index();
        return $this->page('desktop/system/debug/clear.html', $pagedata);
    }

    function cleardata(){
        $filter['uname'] = $_POST['uname'];
        $filter['password'] = $_POST['password'];

        if( !$filter['uname'] || !$filter['password'] ) $this->error_splash();

        if (!$arr = $this->login($filter)) $this->error_splash();
        $arr = $this->app->model('users')->getRow('super',array('user_id'=>$arr['account_id']));

        if( $arr['super'] ) $this->clear();
        else $this->error_splash();
        $this->adminlog("删除体验数据[{$filter['uname']}]", 1);
    }

    private function error_splash( $flag=false,$msg='用户名密码错误',$url=false ) {
        $this->begin($url);
        $this->end( $flag, $msg );
    }

    private function login( $filter ) {

        $user_data['login_name'] = $filter['uname'];
        $user_data['account_type'] = pamAccount::getAuthType('desktop');
        $user_data['disabled'] = 0;

        $arr = app::get('desktop')->model('account')->getRow('account_id,login_password',$user_data);
        if (!$arr)  return false;

        $checkPwd = pam_encrypt::check($filter['password'], $arr['login_password']);
        if(!$checkPwd) return false;

        return $arr;
    }

    private function clear() {
        foreach( kernel::servicelist("desktop_debug_clean_data") as $object )
        {
             if( method_exists($object,'clean') )
                 $object->clean();
         }
        $this->error_splash( true, '数据清理成功!' );
    }



}
