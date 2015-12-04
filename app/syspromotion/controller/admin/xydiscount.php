<?php
class syspromotion_ctl_admin_xydiscount extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_xydiscount',array(
            'title' => app::get('syspromotion')->_('X件Y折列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}