<?php
class sysuser_ctl_admin_point extends desktop_controller{

    public function index()
    {
        $userId = input::get('user_id');
        return $this->finder('sysuser_mdl_user_point',array(
            'title' => app::get('sysuser')->_('会员积分明细'),
            'base_filter' => array('user_id' => $userId),
        ));
    }
}
