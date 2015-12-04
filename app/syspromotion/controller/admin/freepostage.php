<?php
class syspromotion_ctl_admin_freepostage extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_freepostage',array(
            'title' => app::get('syspromotion')->_('免邮列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}