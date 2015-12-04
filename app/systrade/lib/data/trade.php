<?php
class systrade_data_trade{

    /**
     * @brief 获取订单列表数据
     *
     * @param array $filter=array('rows'=>'','filter'=>'','start'=>'','limit'=>'','orderBy'=>'')
     *
     * @return array
     */
    public function getTradeList($params,$IfChild=true,$childNum = 100)
    {
        $rows    = $params['rows']    ? $params['rows']    : '*';
        $filter  = $params['filter']  ? $params['filter']  : null;
        $start   = $params['start']   ? $params['start']   : 0;
        $limit   = $params['limit']   ? $params['limit']   : -1;
        $orderBy = $params['orderBy'] ? $params['orderBy'] : 'created_time DESC';

        $objMdlTrade = app::get('systrade')->model('trade');
        $objMdlOrder = app::get('systrade')->model('order');
        $tradeLists = $objMdlTrade->getList($rows,$filter,$start,$limit,$orderBy);

        #如果不需要子订单信息，直接返回主订单信息
        if(!$IfChild)  return $tradeLists;

        #查询子订单信息
        foreach($tradeLists as $key=>$value)
        {
            $tradeLists[$key]['order'] = $objMdlOrder->getList('*',array('tid'=>$value['tid']),0,$childNum);
        }
        return $tradeLists;
    }

    /**
     * @brief 获取指定订单信息
     *
     * @param string $rows 字段名
     * @param array $filter 查询条件
     *
     * @return array
     */
    public function getTradeInfo($rows,$filter)
    {
        //$rows    = $params['rows']    ? $params['rows']    : '*';
        //$filter  = $params['filter']  ? $params['filter']  : null;

        //这里原本没有这个的，因为如果这里不加tid的话，且rows不带tid，下面会把所有order全拖出来
        if( $rows != '*' && !strpos($rows, 'tid')) $rows .= ',tid';

        $objMdlTrade = app::get('systrade')->model('trade');
        $objMdlOrder = app::get('systrade')->model('order');
        $tradeList = $objMdlTrade->getRow($rows,$filter);
        if($tradeList)
        {
            $tradeList['order'] = $objMdlOrder->getList('*',array('tid'=>$tradeList['tid']));
        }
        return $tradeList;
    }


    /**
        * @brief 修改主订单trade表
        *
        * @param array $params  需要修改的数据
        * @param array $filter  条件
        *
        * @return bool
     */
    public function updateTrade($params)
    {
        $filter = $params['filter'];
        $data   = $params['data'];
        $objMdlTrade = app::get('systrade')->model('trade');
        $result = $objMdlTrade->update($data,$filter);
        if(!$result)
        {
            $msg = app::get('systrade')->_("订单修改失败");
            throw new \LogicException($msg);
            return false;
        }
        return $result;
    }

    /**
        * @brief 保存订单信息
        *
        * @param $params 数据
        *
        * @return bool
     */
    public function saveTrade($params)
    {
        $objMdlTrade = app::get('systrade')->model('trade');
        $result = $objMdlTrade->save($params);
        if(!$result)
        {
            $msg = app::get('systrade')->_("订单保存失败");
            throw new \LogicException($msg);
            return false;
        }
        return $result;
    }

    public function countTrade($filter)
    {
        $objMdlTrade = app::get('systrade')->model('trade');
        $count = $objMdlTrade->count($filter);
        return $count;
    }

    public function create($params)
    {
        echo json_encode(1);exit;
    }

}


