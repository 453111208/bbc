<?php
class syspromotion_ctl_admin_fullminus extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_fullminus',array(
            'title' => app::get('syspromotion')->_('满减列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}