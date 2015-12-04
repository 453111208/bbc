<?php
class sysrate_api_countRate{
    public function getParams()
    {
        $params['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description' => '店铺id'],
        );
        return $params;
    }
    public function countRate($params)
    {
        $shopId = $params['shop_id'];
        $time = time();

        $countTime = array(
            'week' => [strtotime('-7 days'), $time],
            'month' => [strtotime('-30 days'), $time],
            'sixMonth' => [strtotime('-180 days'), $time],
            'sixMonthAgo' => [0, strtotime('-180 days')],
        );

        foreach($countTime as $key=>$row)
        {
            $data[$key] = $this->__countTimeRate($shopId, $row[0], $row[1]);
            if( in_array($key,['sixMonth','sixMonthAgo']) )
            {
                $data['total']['good'] += $data[$key]['good'];
                $data['total']['neutral'] += $data[$key]['neutral'];
                $data['total']['bad'] += $data[$key]['bad'];
                $data['total']['total'] += $data[$key]['total'];
            }
        }

        return $data;
    }

    /**
     * 获取指定时间的评价统计
     *
     * @param int $shopId 店铺ID
     * @param int $startTime 开始统计时间
     * @param int $endTime 结束统计时间
     */
    private function __countTimeRate($shopId, $startTime, $endTime)
    {
        $data = app::get('sysrate')->database()->executeQuery('SELECT count(*) as num,result FROM `sysrate_traderate` WHERE `shop_id`=? and created_time >=? and created_time < ? group by result', [$shopId, $startTime, $endTime])->fetchAll();
        foreach( (array)$data  as $row )
        {
            $return[$row['result']] = $row['num'];
            $return['total'] += $row['num'];
        }
        return $return;
    }

}
