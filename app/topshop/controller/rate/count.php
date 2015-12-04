<?php

class topshop_ctl_rate_count extends topshop_controller {


    //评价列表
    public function index()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('评价管理-商家评价概况');

        $params['shop_id'] = $this->shopId;
        $params['catDsrDiff'] = true;
        $params['countNum'] = true;
        $dsrData = app::get('topshop')->rpcCall('rate.dsr.get', $params);
        if( !$dsrData )
        {
            $countDsr['tally_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['tally_dsr']['percentage'] = '100%';
            $countDsr['attitude_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['attitude_dsr']['percentage'] = '100%';
            $countDsr['delivery_speed_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['delivery_speed_dsr']['percentage'] = '100%';
        }
        else
        {
            $countDsr['tally_dsr']['num'] = sprintf('%.1f',$dsrData['tally_dsr']);
            $countDsr['tally_dsr']['percentage'] = ($dsrData['tally_dsr']/5) * 100 .'%';
            $countDsr['attitude_dsr']['num'] = sprintf('%.1f',$dsrData['attitude_dsr']);
            $countDsr['attitude_dsr']['percentage'] = ($dsrData['attitude_dsr']/5) * 100 .'%';
            $countDsr['delivery_speed_dsr']['num'] = sprintf('%.1f',$dsrData['delivery_speed_dsr']);
            $countDsr['delivery_speed_dsr']['percentage'] = ($dsrData['delivery_speed_dsr']/5) * 100 .'%';
        }
        $pagedata['countDsr'] = $countDsr;
        $pagedata['countNum'] = $dsrData['countNum'];
        $pagedata['catDsrDiff'] = $dsrData['catDsrDiff'];

        $pagedata['count'] = app::get('topshop')->rpcCall('rate.count',array('shop_id' => $this->shopId));

        return $this->page('topshop/rate/count.html', $pagedata);
    }

}

