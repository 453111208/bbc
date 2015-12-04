<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class syspromotion_tasks_coupon extends base_task_abstract implements base_interface_task{

    // 每个队列执行100条订单信息
    var $limit = 100;
    public function exec($params=null)
    {

        // $filter = array(
        //     'canuse_start_time|lthan'=>time(),
        // );
        // $objLibCoupon = kernel::single('syspromotion_coupon');
        // $objLibCoupon->update(array('is_valid'), $filter);
    }
}
