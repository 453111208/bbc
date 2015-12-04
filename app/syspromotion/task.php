<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class syspromotion_task{

    public function post_install($options)
    {
        kernel::single('base_initial', 'syspromotion')->init();
    }

    public function post_update($dbver)
    {
        // 更新原来的促销关联id
        if($dbver['dbver'] < 0.2)
        {
            $db = app::get('syspromotion')->database();
            $xyList = $db->executeQuery('SELECT xydiscount_id,limit_number,discount FROM syspromotion_xydiscount where end_time>'.time())->fetchAll();
            foreach ($xyList as $key => $value)
            {
                $joinxydiscount = $value['limit_number'].'|'.$value['discount'];
                $db->executeUpdate('UPDATE syspromotion_xydiscount SET condition_value = ? WHERE xydiscount_id = ?', [$joinxydiscount, $value['xydiscount_id']]);
            }
        }
    }

}

