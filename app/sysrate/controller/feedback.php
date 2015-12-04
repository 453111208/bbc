<?php

class sysrate_ctl_feedback extends desktop_controller {

    public $workground = 'sysshop.workground.shoptype';

    public function index()
    {
        return $this->finder(
            'sysrate_mdl_feedback',
            array(
                'title'=>app::get('sysrate')->_('意见反馈列表'),
            )
        );
    }

    public function showFeedback($id)
    {
        $pagedata = $this->__getFeedbackData($id);
        return $this->page('sysrate/feedback/index.html', $pagedata);
    }

    public function doClosed()
    {
        $id = input::get('id');

        $this->begin("?app=sysrate&ctl=feedback&act=index");

        if( $id )
        {
            app::get('sysrate')->model('feedback')->update(array('status'=>'closed','memo'=>input::get('memo')), array('id'=>$id));
            $this->adminlog("意见反馈处理[{$params['id']}]", 1);
        }
        else
        {
            $this->adminlog("意见反馈处理[{$params['id']}]", 0);
            $this->end(false,'操作失败');
        }

        $this->end(true,'操作成功');
    }

    private function __getFeedbackData($id)
    {
        $data = array();
        if( $id )
        {
            $data = app::get('sysrate')->model('feedback')->getRow('*', array('id'=>$id));
        }
        return $data;
    }

    public function _views(){
        $subMenu = array(
            0=>array(
                'label'=>app::get('sysrate')->_('全部'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('sysrate')->_('待处理'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'active',
                ),
                'addon'=>app::get('sysrate')->model('feedback')->count(array('status'=>'active')),
            ),
            2=>array(
                'label'=>app::get('sysrate')->_('已处理'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'closed',
                ),
            ),
        );
        return $subMenu;
    }
}

