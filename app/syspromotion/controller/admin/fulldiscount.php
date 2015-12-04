<?php
class syspromotion_ctl_admin_fulldiscount extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_fulldiscount',array(
            'title' => app::get('syspromotion')->_('满折列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}