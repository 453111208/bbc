<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class sysstat_command_sysstatexec extends base_shell_prototype{

    var $command_exec = '根据条件执行商家统计';
    var $command_exec_options = array(
        'timeStart'=>array('title'=>'具体某个日期，如：2015-06-01'),
        'timeEnd'=>array('title'=>'具体某个日期，如：2015-06-02'),
    );
    function command_exec($timeStart,$timeEnd)
    {
        $nowTime = strtotime(date('Y-m-d'.'00:00:00',time()));
        if(strtotime($timeStart)>strtotime($timeEnd)||strtotime($timeStart)>$nowTime)
        {
            return false;
        }
        else
        {
            if($timeStart&&$timeEnd)
            {
                $count = ceil(((strtotime($timeEnd)-strtotime($timeStart)))/(3600*24));
                for ($i=0; $i <$count ; $i++)
                { 
                    $params = array(
                    'time_start'=>strtotime($timeStart.' 00:00:00')+$i*(3600*24),
                    'time_end'=>strtotime($timeStart.' 23:59:59')+$i*(3600*24),
                    'time_insert'=>strtotime($timeStart)+$i*(3600*24)
                    );
                    if(kernel::single('sysstat_shop_taskdata')->exec($params))
                    {
                        return true;
                    }
                }
            }
            else
            {
                $params = array(
                'time_start'=>strtotime($timeStart.' 00:00:00'),
                'time_end'=>strtotime($timeStart.' 23:59:59'),
                'time_insert'=>strtotime($timeStart)
                );
                if(kernel::single('sysstat_shop_taskdata')->exec($params))
                {
                    return true;
                }
            }
        }
    }

    var $command_execall = '强制重新计算商家所有的订单数据到统计表中';
    function command_execall()
    {
        $timeEnd = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
        $timeStart = strtotime('2015-01-01 00:00:00');
        $count = ceil(($timeEnd-$timeStart)/(3600*24));
        for ($i=0; $i <$count ; $i++)
        { 
            $params = array(
            'time_start'=>strtotime('2015-01-01 00:00:00')+$i*(3600*24),
            'time_end'=>strtotime('2015-01-01 23:59:59')+$i*(3600*24),
            'time_insert'=>strtotime('2015-01-01')+$i*(3600*24)
            );
            if(kernel::single('sysstat_shop_taskdata')->exec($params))
            {
                return true;
            }
        }
    }

}
