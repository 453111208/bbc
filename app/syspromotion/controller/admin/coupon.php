<?php
class syspromotion_ctl_admin_coupon extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_coupon',array(
            'title' => app::get('syspromotion')->_('优惠券列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}