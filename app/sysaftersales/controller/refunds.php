<?php

class sysaftersales_ctl_refunds extends desktop_controller {

    public $workground = 'sysaftersales.workground.aftersale;';

    public function index()
    {
        return $this->finder(
            'sysaftersales_mdl_refunds',
            array(
                'title'=>app::get('sysaftersales')->_('申请退款列表'),
                 'use_buildin_delete'=>false,
            )
        );
    }

    public function rejectView($refundsId)
    {
        if( !$refundsId )
        {
            $refundsId = input::get();
        }
        $data = app::get('sysaftersales')->model('refunds')->getRow('aftersales_bn,refunds_id,oid', array('refunds_id'=>$refundsId));
        $pagedata['aftersalesBn'] = $data['aftersales_bn'];
        $pagedata['refundsId'] = $data['refunds_id'];
        return $this->page('sysaftersales/reject.html', $pagedata);
    }

    public function doTeject()
    {
        $this->begin("?app=sysaftersales&ctl=refunds&act=index");

        $postdata = input::get('data');
        if( empty($postdata['explanation']) )
        {
            $this->end(false,'取消原因必填');
        }
        //$params['confirm_from'] = 'admin';
        try
        {
            app::get('sysaftersales')->rpcCall('aftersales.refunds.reject',$postdata);
            $this->adminlog("平台拒绝商家退款[aftersales_bn:{$postdata['aftersales_bn']}]", 1);
        }
        catch(\LogicException $e)
        {
            $this->adminlog("平台拒绝商家退款[aftersales_bn:{$postdata['aftersales_bn']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end('true');
    }

    public function refundsPay($aftersalesBn)
    {
        $this->begin("?app=sysaftersales&ctl=refunds&act=index");
        $data = app::get('sysaftersales')->model('refunds')->getRow('*', array('aftersales_bn'=>$aftersalesBn));
        $pagedata['user']['id'] = kernel::single('desktop_user')->get_id();
        $pagedata['user']['name'] = kernel::single('desktop_user')->get_login_name();
        if( $data['status'] != '0' )
        {
            $this->end('false','不需要处理');
        }
        $user = app::get('sysaftersales')->rpcCall('user.get.account.name',array('user_id'=>$data['user_id']),'buyer');
        $data['user_name'] = $user[$data['user_id']];
        $pagedata['data'] = $data;
        return $this->page('sysaftersales/refunds.html', $pagedata);
    }

    public function dorefund()
    {
        $postdata = input::get('data');
        $postdata['refundsData'] = json_encode(input::get('refundsData'));
        $this->begin("?app=sysaftersales&ctl=refunds&act=index");
        try
        {
            app::get('sysaftersales')->rpcCall('aftersales.refunds.restore',$postdata);
            $this->adminlog("处理退款[refunds_id:{$postdata['refunds_id']}]", 1);
        }
        catch(\LogicException $e)
        {
            $this->adminlog("处理退款[refunds_id:{$postdata['refunds_id']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }

        $this->end('true');
    }
}


