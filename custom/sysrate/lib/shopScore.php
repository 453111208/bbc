<?php

class sysrate_shopScore {

    public function __construct()
    {
        $this->objMdlscore = app::get('sysrate')->model('score');
        $this->objMdlDsr = app::get('sysrate')->model('dsr');
    }

    /**
     * 新增一个完成订单的动态评分
     *
     * @param int $tid 动态评分的订单ID
     */
    public function add($tid, $shopId, $userId, $data)
    {
        //检查店铺动态评分提交的数据是否合法
        $this->__checkScoreData($tid, $data);

        //todo@wei 根据店铺ID 获取店铺入驻的类目API
        $objMdlShopRelCat =  app::get('sysshop')->model('shop_rel_lv1cat');
        $catInfo = $objMdlShopRelCat->getRow('cat_id',array('shop_id'=>$shopId));
        $params['shop_id'] = $shopId;
        $params['fields'] = 'shop_type';
        $shopInfo = app::get('sysshop')->rpcCall('shop.get.detail',$params);
        if($shopInfo['shop']['shop_type']=='self')
        {
            $scoreInsert['cat_id'] = 0;
        }
        else
        {
            $scoreInsert['cat_id'] = $catInfo['cat_id'];
        }
        $scoreInsert['tid'] = $tid;
        $scoreInsert['user_id'] = $userId;
        $scoreInsert['shop_id'] = $shopId;
        $scoreInsert['tally_score'] = $data['tally_score'];
        $scoreInsert['attitude_score'] = $data['attitude_score'];
        $scoreInsert['delivery_speed_score'] = $data['delivery_speed_score'];
        $scoreInsert['logistics_service_score'] = $data['logistics_service_score'];
        $scoreInsert['created_time'] = time();
        $scoreInsert['modified_time'] = time();

        $this->__setInvalidScore($scoreInsert);

        return $this->objMdlscore->insert($scoreInsert);
    }

    /**
     * 设置无效商家动态评分
     */
    private function __setInvalidScore($scoreInsert)
    {
        //当前月内，同一用户对同一商家进行动态评分，有效评分为3次，覆盖方式进行计算
        $filter = array(
            'user_id'=>$scoreInsert['user_id'],
            'shop_id'=>$scoreInsert['shop_id'],
            'modified_time|bthan'=>mktime(0,0,0,date('m'),0,date('Y')),
            'disabled' => 0,
        );
        $scoreData = $this->objMdlscore->getList('*', $filter, 0, 3, 'modified_time desc');
        if( count($scoreData) == 3 )
        {
            //将取出的三条的最后一条数据评价设置为无效
            $this->objMdlscore->update(['modified_time'=>time(),'disabled'=>1],['tid'=>$scoreData[2]['tid']]);
        }

        return true;
    }

    /**
     * 检查店铺动态评分数据是否合法
     */
    private function __checkScoreData($tid, $data)
    {
        $num = $this->objMdlscore->count(['tid'=>$tid]);
        if( !empty($num) )
        {
            throw new \LogicException(app::get('sysrate')->_('订单已评价'));
        }

        $params['tally_score'] = $data['tally_score'];
        $params['attitude_score'] = $data['attitude_score'];
        $params['delivery_speed_score'] = $data['delivery_speed_score'];
        $params['logistics_service_score'] = $data['logistics_service_score'];
        foreach( (array)$params as $score )
        {
            if( !$score || $score < 1 || $score > 5 )
            {
                throw new \LogicException(app::get('sysrate')->_('请选择店铺动态评分'));
            }
        }
        return true;
    }

}

