<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysstat_desktop_widgets_workcount implements desktop_interface_widget
{
    var $order = 1;
    function __construct()
    {
        $this->app = app::get('sysstat');
    }


    function get_title(){

        return app::get('sysstat')->_("待处理事项");

    }

    function get_html()
    {

        $mdlEnterapply = app::get('sysshop')->model('enterapply');
        $mdlTradeStatics = app::get('sysstat')->model('trade_statics');
        //待审核商家
        $applayShop = $mdlEnterapply->count(array('status'=>'active'));
        //待开启店铺
        $applyActive = $mdlEnterapply->count(array('status'=>'successful'));

        $filter = array(
            'createtime|sthan'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'),
            'createtime|bthan'=>strtotime(date("Y-m-d", time()-86400*8) . ' 00:00:00')
        );
        $tradeList =  $mdlTradeStatics->getList('ready_trade,ready_send_trade,already_send_trade,complete_trade',$filter);
        foreach ($tradeList as $key => $value)
        {
            $trade['newTrade'] += $value['ready_trade'];
            $trade['newReadyTrade'] += $value['ready_send_trade'];
            $trade['newAlreadyTrade'] += $value['already_send_trade'];
            $trade['newCompleteTrade'] += $value['complete_trade'];
        }
        $pagedata['applayShop'] = intval($applayShop);
        $pagedata['applyActive'] = intval($applyActive);
        $pagedata['newTrade'] = intval($trade['newTrade']);
        $pagedata['newReadyTrade'] = intval($trade['newReadyTrade']);
        $pagedata['newAlreadyTrade'] = intval($trade['newAlreadyTrade']);
        $pagedata['newCompleteTrade'] = intval($trade['newCompleteTrade']);
        return view::make('sysstat/desktop/widgets/workcount.html', $pagedata)->render();
    }
    public function get_className()
    {
        return " valigntop";
    }
    public function get_width()
    {
        return "l-2";
    }

}