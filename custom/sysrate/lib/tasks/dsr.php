<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysrate_tasks_dsr extends base_task_abstract implements base_interface_task {

    public function exec($params=null)
    {
        $objMdlDsr = app::get('sysrate')->model('score');

        $nowTime = time();

        //获取上次统计的时间
        $time = $this->__getCountTime();
        if( !$time )
        {
            //如果没有统计过，则对180天的数据进行统计 可以优化
            $oldTime = $nowTime - strtotime('-180 days');
            $newData = $objMdlDsr->getList('*', ['modified_time|between'=>array($oldTime,$nowTime),'disabled'=>0] );
            if( empty($newData) ) return true;
        }
        else
        {
            $countTime = $nowTime - $time['new'];
            //获取当天改变的店铺动态评分
            $newData = $objMdlDsr->getList('*', ['modified_time|between'=>array($time['new'],$nowTime)] );

            $oldTime = $time['old'] + $countTime;
            //获取180天前有效的店铺动态评分
            $oldData = $objMdlDsr->getList('*', ['modified_time|between'=>array($time['old'],$oldTime),'disabled'=>0] );
        }

        //设置该次统计的时间
        $this->__setCountTime($nowTime, $oldTime);

        if( $newData )
        {
            foreach( $newData  as $row )
            {
                //如果该动态评分已经失效
                if( $row['disabled'] )
                {
                    $catDsr[$row['cat_id']][$row['shop_id']]['tally_dsr'][$row['tally_score']] -= 1;
                    $catDsr[$row['cat_id']][$row['shop_id']]['attitude_dsr'][$row['attitude_score']] -= 1;
                    $catDsr[$row['cat_id']][$row['shop_id']]['delivery_speed_dsr'][$row['delivery_speed_score']] -= 1;
                }
                else//新增有效动态评分
                {
                    $catDsr[$row['cat_id']][$row['shop_id']]['tally_dsr'][$row['tally_score']] += 1;
                    $catDsr[$row['cat_id']][$row['shop_id']]['attitude_dsr'][$row['attitude_score']] += 1;
                    $catDsr[$row['cat_id']][$row['shop_id']]['delivery_speed_dsr'][$row['delivery_speed_score']] += 1;
                }
            }
        }

        if( $oldData )
        {
            foreach( $oldData as $value )
            {
                $catDsr[$row['cat_id']][$row['shop_id']]['tally_dsr'][$row['tally_score']] -= 1;
                $catDsr[$row['cat_id']][$row['shop_id']]['attitude_dsr'][$row['attitude_score']] -= 1;
                $catDsr[$row['cat_id']][$row['shop_id']]['delivery_speed_dsr'][$row['delivery_speed_score']] -= 1;
            }
        }

        if( $catDsr )
        {
            foreach( $catDsr as $catId=>$shopDsr )
            {
                $this->__updateShopDsr($catId, $shopDsr);
            }
        }

        return true;
    }

    private function __updateShopDsr($catId, $shopDsr)
    {
        $objLibDsr = kernel::single('sysrate_dsr');

        $catDsr = $objLibDsr->getCatDsr($catId);
        $maxCatDsr = $catDsr['max'];
        $minCatDsr = $catDsr['min'];

        foreach( $shopDsr as $shopId=>$dsrData )
        {
            $tmpDsr = $objLibDsr->updateDsr($shopId, $catId, $dsrData);

            //获取店铺评价项的动态评分
            $dsrColumn = ['tally_dsr','attitude_dsr','delivery_speed_dsr'];
            foreach( $dsrColumn as $col )
            {
                $dsrNumber = $objLibDsr->getDsrNumber($tmpDsr[$col]);

                if( $catDsr['max'][$col] )
                {
                    if( $dsrNumber >= $maxCatDsr[$col] )
                    {
                        $maxCatDsr[$col] = $catDsr['max'][$col] = $dsrNumber;
                    }
                }
                else
                {
                   $maxCatDsr[$col] = $catDsr['max'][$col] =  $dsrNumber;
                }

                if($catDsr['min'][$col])
                {
                    if( $dsrNumber <=  $minCatDsr[$col] )
                    {
                        $minCatDsr[$col] = $catDsr['min'][$col] = $dsrNumber;
                    }
                }
                else
                {
                   $minCatDsr[$col] = $catDsr['min'][$col] = $dsrNumber;
                }

                //i 对应的评分的分值
                for( $i=1; $i<=5; $i++ )
                {
                    $plus = $dsrData[$col][$i] ? $dsrData[$col][$i] : 0;
                    $catDsr['count'][$col][$i] = $catDsr['count'][$col][$i] + $plus;
                }
            }
        }

        return  $objLibDsr->setCatDsr($catId, $catDsr);
    }

    /**
     * 设置上一次统计的时间
     */
    private function __setCountTime($nowTime, $oldTime)
    {
        $time['new'] = $nowTime;
        $time['old'] = $oldTime;
        return base_kvstore::instance('sysrate_dsr')->store('dsrCountTime', $time);
    }

    /**
     * 获取上一次统计的时间
     */
    private function __getCountTime()
    {
        base_kvstore::instance('sysrate_dsr')->fetch('dsrCountTime', $time);
        return $time;
    }
}

