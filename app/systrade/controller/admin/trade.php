<?php
class systrade_ctl_admin_trade extends desktop_controller{

    public function index()
    {
        return $this->finder('systrade_mdl_trade',array(
            'use_buildin_filter'=>true,
            'use_view_tab'=>true,
            'title' => app::get('systrade')->_('交易列表'),
            'actions' => array(
                array(
                    'label' => app::get('systrade')->_('批量取消交易'),
                    'submit' => '?app=systrade&ctl=admin_trade&act=doCancel',
                    'target'=>'dialog::{title:\''.app::get('systrade')->_('批量交易取消').'\',width:500,height:350}',
                ),
            ),
            'use_buildin_delete'=>false,
        ));
    }

    public function doCancel($tid)
    {
        if(!$tid)
        {
            $tids = input::get('tid');
            $tid = implode(',',$tids);
        }
        $pagedata['tid'] = $tid;
        return $this->page('systrade/admin/cancel.html', $pagedata);
    }

    public function cancelTrade()
    {
        $this->begin("?app=systrade&ctl=admin_trade&act=index");
        $cancelData = input::get('data');
        $params['filter']['tid'] = $cancelData['tid'];
        $params['data']['status'] = 'TRADE_CLOSED_BY_SYSTEM';
        $params['data']['end_time'] = time();
        $params['data']['cancel_reason'] = $cancelData['cancel_reason'];
        try
        {
            kernel::single('systrade_data_trade_cancel')->generate($params);
            $this->adminlog("取消订单[单号:{$cancelData['tid']}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("取消订单[单号:{$cancelData['tid']}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    public function doDelete()
    {
        $this->begin("?app=systrade&ctl=admin_trade&act=index");
        $params = input::get('tid');
        try
        {
            kernel::single('systrade_data_trade_delete')->generate($params);
            $this->adminlog("删除订单[单号:{json_encode($params)}]", 1);
        }
        catch(Exception $e)
        {
            $this->adminlog("删除订单[单号:{json_encode($params)}]", 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true);
    }

    /**
     * 桌面订单相信汇总显示
     * @param null
     * @return null
     */
    public function _views()
    {
        $mdl_aftersales = app::get('systrade')->model('trade');
        $sub_menu = array(
            0=>array('label'=>app::get('systrade')->_('全部'),'optional'=>false,'filter'=>array('disabled'=>0)),
            1=>array('label'=>app::get('systrade')->_('等待付款'),'optional'=>false,'filter'=>array('status'=>'WAIT_BUYER_PAY','disabled'=>0)),
            2=>array('label'=>app::get('systrade')->_('已付款'),'optional'=>false,'filter'=>array('status'=>'WAIT_SELLER_SEND_GOODS','disabled'=>0)),
            3=>array('label'=>app::get('systrade')->_('已发货'),'optional'=>false,'filter'=>array('status'=>'WAIT_BUYER_CONFIRM_GOODS','disabled'=>0)),
            4=>array('label'=>app::get('systrade')->_('已完成'),'optional'=>false,'filter'=>array('status'=>'TRADE_FINISHED','disabled'=>0)),
            5=>array('label'=>app::get('systrade')->_('已关闭'),'optional'=>false,'filter'=>array('status'=>array('TRADE_CLOSED_BY_SYSTEM', 'TRADE_CLOSED'),'disabled'=>0)),
        );

        if(isset($_GET['optional_view'])) $sub_menu[$_GET['optional_view']]['optional'] = false;
        //昨日新增
        $yesterday_newtrade = array(
                    'status'=>'WAIT_BUYER_PAY',
                    'created_time|sthan'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'),
                    'created_time|bthan'=>strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00')
                );
        $yesterday_new = $mdl_aftersales->count($yesterday_newtrade);
        $sub_menu[6] = array('label'=>app::get('systrade')->_('昨日新增'),'optional'=>true,'filter'=>$yesterday_newtrade,'addon'=>$yesterday_new,'href'=>'index.php?app=systrade&ctl=admin_trade&act=index&view=6&view_from=dashboard');
        //昨日已付款
        $yesterday_readytrade = array(
                    'status'=>'WAIT_BUYER_PAY',
                    'created_time|sthan'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'),
                    'created_time|bthan'=>strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00')
                );
        $yesterday_ready = $mdl_aftersales->count($yesterday_readytrade);
        $sub_menu[7] = array('label'=>app::get('systrade')->_('昨日已付款'),'optional'=>true,'filter'=>$yesterday_readytrade,'addon'=>$yesterday_ready,'href'=>'index.php?app=systrade&ctl=admin_trade&act=index&view=7&view_from=dashboard');
        //昨日已发货
        $yesterday_alreadytrade = array(
                    'status'=>'WAIT_BUYER_PAY',
                    'created_time|sthan'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'),
                    'created_time|bthan'=>strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00')
                );
        $yesterday_already = $mdl_aftersales->count($yesterday_alreadytrade);
        $sub_menu[8] = array('label'=>app::get('systrade')->_('昨日已发货'),'optional'=>true,'filter'=>$yesterday_alreadytrade,'addon'=>$yesterday_already,'href'=>'index.php?app=systrade&ctl=admin_trade&act=index&view=8&view_from=dashboard');
        //昨日已完成
        $yesterday_completetrade = array(
                    'status'=>'WAIT_BUYER_PAY',
                    'created_time|sthan'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'),
                    'created_time|bthan'=>strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00')
                );
        $yesterday_complete = $mdl_aftersales->count($yesterday_completetrade);
        $sub_menu[9] = array('label'=>app::get('systrade')->_('昨日已完成'),'optional'=>true,'filter'=>$yesterday_completetrade,'addon'=>$yesterday_complete,'href'=>'index.php?app=systrade&ctl=admin_trade&act=index&view=9&view_from=dashboard');

        foreach($sub_menu as $k=>$v)
        {
            if($v['optional']==false)
            {
                $show_menu[$k] = $v;
                if(is_array($v['filter']))
                {
                    $v['filter'] = array_merge(array(),$v['filter']);
                }
                else
                {
                    $v['filter'] = array();
                }
                $show_menu[$k]['filter'] = $v['filter']?$v['filter']:null;
                if($k==$_GET['view'])
                {
                    $show_menu[$k]['newcount'] = true;
                    $show_menu[$k]['addon'] = $mdl_aftersales->count($v['filter']);
                }
                $show_menu[$k]['href'] = '?app=systrade&ctl=admin_trade&act=index&view='.($k).(isset($_GET['optional_view'])?'&optional_view='.$_GET['optional_view'].'&view_from=dashboard':'');
            }
            elseif(($_GET['view_from']=='dashboard')&&$k==$_GET['view'])
            {
                $show_menu[$k] = $v;
            }
        }
        return $show_menu;
    }
}
