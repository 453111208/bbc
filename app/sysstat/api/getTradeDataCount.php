<?php
class sysstat_api_getTradeDataCount{
    public $apiDescription = "根据时间获取商家订单统计信息";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'店铺id'],
            'createtime' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'数据记录时间，格式时间戳'],
            'type' => ['type'=>'string','valid'=>'', 'default'=>'nequal', 'example'=>'nequal','description'=>'根据时间查询类型，nequal、bthan、sthan 等等'],
        );
        return $return;
    }
    public function getTradeInfo($params)
    {
        $filter['shop_id'] = $params['shop_id'];
        $type = $params['type'] ? $params['type'] : 'nequal';
        if($params['createtime'])
        {
            $filter['createtime|'.$type] = $params['createtime'];
        }
        $tradeStaticMdl = app::get('sysstat')->model('trade_statics');
        $result = $tradeStaticMdl->getList('*',$filter);
        return $result;
    }
}
