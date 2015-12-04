<?php

class sysrate_ctl_traderate extends desktop_controller {

    public $workground = 'sysuser.wrokground.user';

    public function index()
    {
        return $this->finder(
            'sysrate_mdl_traderate',
            array(
                'title'=>app::get('sysrate')->_('评论列表'),
            )
        );
    }

    public function _views(){
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysrate')->_('全部'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysrate')->_('好评'),
                'optional'=>false,
                'filter'=>array(
                    'result'=>'good',
                ),

            ),
            2=>array(
                'label'=>app::get('sysrate')->_('中评'),
                'optional'=>false,
                'filter'=>array(
                    'result'=>'neutral',
                ),
            ),
            3=>array(
                'label'=>app::get('sysrate')->_('差评'),
                'optional'=>false,
                'filter'=>array(
                    'result'=>'bad',
                ),
            ),
        );
        return $subMenu;
    }
}

