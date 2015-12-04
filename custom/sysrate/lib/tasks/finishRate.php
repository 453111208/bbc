<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysrate_tasks_finishRate extends base_task_abstract implements base_interface_task {


    public function exec($params=null)
    {
        //15天内没有评价，自动好评
        $filter['buyer_rate'] = 0;
        $filter['disabled'] = 0;
        $filter['status'] = 'TRADE_FINISHED';
        $filter['end_time|lthan'] = strtotime('-15 days');

        $data = app::get('systrade')->model('trade')->getList('tid', $filter);
        if( empty($data) ) return true;

        foreach($data as $row)
        {
        }
    }
}

