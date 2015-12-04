<?php
class desktop_ctl_cleanexpired extends desktop_controller{
    function index(){
        return $this->page('desktop/cleanexpired.html');
    }

    function clean_data(){
        kernel::single('base_cleandata')->clean();
        //退出登录
        $this->begin('javascript:Cookie.dispose("basicloginform_password");Cookie.dispose("basicloginform_autologin");location="'.url::route('shopadmin').'"');
        $this->user->login();
        $this->user->logout();

        pamAccount::logout();

        kernel::single('base_session')->destory();
        $this->end('true',app::get('desktop')->_('已成功退出系统,正在转向...'));
    }
}
