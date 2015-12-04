<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysstat_desktop_widgets_stats implements desktop_interface_widget
{
    var $order = 1;
    function __construct()
    {
        $this->app = app::get('sysstat');
    }


    function get_title(){

        return app::get('sysstat')->_("平台业务概览");

    }

    function get_html()
    {

        $mdlTrade = $this->app->model('trade_statics');
        $mdlStatMember = $this->app->model('statmember');
         //昨日的订单量
        $filter = array('createtime|nequal'=>strtotime(date("Y-m-d", time()-86400) . ' 00:00:00'));
        $yesterdayList = $mdlTrade->getRow('*',$filter);
        $statMemberList = $mdlStatMember->getRow('*',$filter);

        $pagedata['yesterday_order'] = intval($yesterdayList['new_trade']);
        $pagedata['yesterday_payed'] = intval($yesterdayList['complete_trade']+$yesterdayList['already_send_trade']+$yesterdayList['ready_send_trade']);
        $pagedata['yesterday_already'] = intval($yesterdayList['already_send_trade']);
        $pagedata['yesterday_complete'] = intval($yesterdayList['complete_trade']);
        $pagedata['yesterday_user'] = intval($statMemberList['newuser']);
        $pagedata['accountuser'] = intval($statMemberList['accountuser']);
        $pagedata['yesterday_seller'] = intval($statMemberList['sellernum']);
        $pagedata['accountseller'] = intval($statMemberList['accountseller']);
        $pagedata['yesterday_shopnum'] = intval($statMemberList['shopnum']);
        $pagedata['shopaccount'] = intval($statMemberList['shopaccount']);
        return view::make('sysstat/desktop/widgets/stats.html', $pagedata)->render();

    }
    public function get_className()
    {
        return " valigntop";
    }
    public function get_width()
    {
        return "l-1";
    }

}
