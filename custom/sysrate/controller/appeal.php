<?php

class sysrate_ctl_appeal extends desktop_controller {

    public $workground = 'sysuser.wrokground.user';

    public function index()
    {
        return $this->finder(
            'sysrate_mdl_appeal',
            array(
                'title'=>app::get('sysrate')->_('申诉列表'),
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
                'label'=>app::get('sysrate')->_('待处理'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'WAIT',
                ),
                'addon'=>app::get('sysrate')->model('appeal')->count(array('status'=>'WAIT')),
            ),
            2=>array(
                'label'=>app::get('sysrate')->_('已处理'),
                'optional'=>false,
                'filter'=>array(
                    'status|noequal'=>'WAIT',
                ),
            ),
        );
        return $subMenu;
    }

    public function checkView($appealId)
    {
        $appealData = app::get('sysrate')->model('appeal')->getRow('*',['appeal_id'=>$appealId]);
        if( !$appealData )
        {
            return '审核的申诉不存在';
        }

        if( $appealData['evidence_pic'] )
        {
            $appealData['evidence_pic'] = explode(',', $appealData['evidence_pic']);
        }

        if( $appealData['appeal_log']['evidence_pic'] )
        {
            $appealData['appeal_log']['evidence_pic'] = explode(',', $appealData['appeal_log']['evidence_pic']);
        }

        $pagedata['appealData'] =  $appealData;

        $rateData = app::get('sysrate')->model('traderate')->getRow('tid,oid,result,content,rate_pic,created_time',['rate_id'=>$appealData['rate_id'],'disabled'=>[0,1] ]);
        if($rateData['rate_pic'])
        {
            $rateData['rate_pic'] = explode(',', $rateData['rate_pic']);
        }

        $pagedata['rateData'] =  $rateData;

        // 订单状态标示对应表
        $this->tradeStatus = array(
            'WAIT_BUYER_PAY' => '已下单等待付款',
            'WAIT_SELLER_SEND_GOODS' => '已付款等待发货',
            'WAIT_BUYER_CONFIRM_GOODS' => '已发货等待确认收货',
            'TRADE_FINISHED' => '已完成',
            'TRADE_CLOSED' => '已关闭',
            'TRADE_CLOSED_BY_SYSTEM' => '已关闭'
        );
        $params = array(
            'tid' =>$rateData['tid'],
            'oid' =>$rateData['oid'],
            'fields' =>'tid,user_id,created_time,consign_time,status,shop_id,orders.bn,orders.sendnum,orders.num,orders.price,orders.total_fee,orders.oid,orders.title,orders.item_id',
        );
        $trade = app::get('sysrate')->rpcCall('trade.get', $params);
        $trade['status_des'] = $this->tradeStatus[$trade['status']];
        $pagedata['trade'] =  $trade;

        $pagedata['appeal_id'] = $appealId;

        return $this->page('sysrate/appeal/check.html', $pagedata);
    }

    public function check()
    {
        $this->begin("?app=sysrate&ctl=appeal&act=index");
        $params['appeal_id'] = input::get('appeal_id');
        $params['result'] = input::get('result');
        $params['reject_reason'] = input::get('reason');

        try
        {
            app::get('sysrate')->rpcCall('rate.appeal.check',$params);
            $this->adminlog("评价申诉审核[{$params['appeal_id']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("评价申诉审核[{$params['appeal_id']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }
}

