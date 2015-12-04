<?php

class systrade_ctl_admin_complaints extends desktop_controller {

    public function index()
    {
        return $this->finder('systrade_mdl_order_complaints',array(
            'use_buildin_filter'=>true,
            'title' => app::get('systrade')->_('订单投诉列表'),
            'use_buildin_delete'=>true,
        ));
    }

    public function edit($complaintsId)
    {
        $pagedata = app::get('systrade')->model('order_complaints')->getRow('*',['complaints_id'=>$complaintsId]);
        if( $pagedata['image_url'] )
        {
            $pagedata['image_url'] = explode(',', $pagedata['image_url']);
        }

        return $this->page('systrade/complaints/edit.html', $pagedata);
    }

    public function doComplaints()
    {
        $this->begin("?app=systrade&ctl=admin_complaints&act=index");

        $params['complaints_id'] = input::get('complaints_id');
        $params['memo'] = input::get('memo');
        $params['status'] = input::get('status');

        try
        {
            app::get('systrade')->rpcCall('trade.order.complaints.process',$params);
            $this->adminlog("处理投诉[单号:{$params['complaints_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("处理投诉[单号:{$params['complaints_id']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    public function _views()
    {
        $subMenu = array(
            0=>array(
                'label'=>app::get('systrade')->_('全部'),
                'optional'=>false,
            ),
            1=>array(
                'label'=>app::get('systrade')->_('待处理'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'WAIT_SYS_AGREE',
                ),
                'addon'=>app::get('systrade')->model('order_complaints')->count(array('status'=>'WAIT_SYS_AGREE')),
            ),
            3=>array(
                'label'=>app::get('systrade')->_('已完成'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'FINISHED',
                ),
            ),
            4=>array(
                'label'=>app::get('systrade')->_('已关闭'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'CLOSED',
                ),
            ),
            5=>array(
                'label'=>app::get('systrade')->_('买家撤销'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'BUYER_CLOSED',
                ),
            ),
        );
        return $subMenu;
    }

}

