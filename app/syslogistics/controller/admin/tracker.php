<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syslogistics_ctl_admin_tracker extends desktop_controller {

    public $workground = 'syslogistics.workground.logistics';

    public function index()
    {
        if( $_POST )
        {
            app::get('syslogistics')->setConf('syslogistics.order.tracking',$_POST['syslogistics_order_tracking']);
            $pagedata['syslogistics_order_tracking'] = $_POST['syslogistics_order_tracking'];

            app::get('syslogistics')->setConf('syslogistics.order.hqepay',$_POST['hqepay']);
            $pagedata['hqepay'] = $_POST['hqepay'];
        }
        else
        {
            $pagedata['syslogistics_order_tracking'] = app::get('syslogistics')->getConf('syslogistics.order.tracking');
            $pagedata['hqepay'] = app::get('syslogistics')->getConf('syslogistics.order.hqepay');
        }
        return $this->page('syslogistics/admin/tracker_setting.html', $pagedata);
    }

}
