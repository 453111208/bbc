<?php

class sysaftersales_ctl_list extends desktop_controller {

    public $workground = 'sysaftersales.workground.aftersale;';

    public function index()
    {
        return $this->finder(
            'sysaftersales_mdl_aftersales',
            array(
                'title'=>app::get('sysaftersales')->_('售后申请列表'),
            )
        );
    }
}
