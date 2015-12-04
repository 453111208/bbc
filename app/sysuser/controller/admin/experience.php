<?php
class sysuser_ctl_admin_experience extends desktop_controller{

    public function index()
    {
        $userId = input::get('user_id');
        return $this->finder('sysuser_mdl_user_experience',array(
            'title' => app::get('sysuser')->_('会员成长值明细'),
            'base_filter' => array('user_id' => $userId),
        ));
    }
}
