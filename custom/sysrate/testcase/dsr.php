<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class dsr extends PHPUnit_Framework_TestCase {

    public function testInsertDsr()
    {
        for($i=1001; $i<=2000; $i++)
        {
            $data = array();
            //$data['cat_id'] = $catId[rand(1,6)];
            $data['tid'] = $i;
            $userId = rand(1,9999);
            $shopId = rand(1,4);
            $data['tally_score'] = rand(1,5);
            $data['attitude_score'] = rand(1,5);
            $data['delivery_speed_score'] = rand(1,5);
            $data['logistics_service_score'] = rand(1,5);
            $data['created_time'] = time('-4 day');
            $data['modified_time'] = time('-4 day');

            kernel::single('sysrate_shopScore')->add($i, $shopId, $userId, $data);
            echo '第'.$i.'条'."\n";
        }
    }
}
