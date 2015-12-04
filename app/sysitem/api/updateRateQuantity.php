<?php

class sysitem_api_updateRateQuantity {

    /**
     * 接口作用说明
     */
    public $apiDescription = '修改评论数量';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'item_id' => ['type'=>'int', 'valid'=>'required', 'description'=>'商品ID'],
            'rate_good_count' => ['type'=>'int', 'valid'=>'', 'description'=>'本次增加的好评次数'],
            'rate_neutral_count' => ['type'=>'int', 'valid'=>'', 'description'=>'本次增加的中评次数'],
            'rate_bad_count' => ['type'=>'int', 'valid'=>'', 'description'=>'本次增加的差评次数'],
        );

        return $return;
    }

    /**
     * 更新评论数量
     */
    public function update($params)
    {
        if( $params['rate_good_count'] )
        {
            $setSql[] = ' rate_good_count = rate_good_count + '.intval($params['rate_good_count']);
            $rateCount += $params['rate_good_count'];
        }

        if( $params['rate_neutral_count'] )
        {
            $setSql[] = ' rate_neutral_count = rate_neutral_count +'.intval($params['rate_neutral_count']);
            $rateCount += $params['rate_neutral_count'];
        }

        if( $params['rate_bad_count'] )
        {
            $setSql[] = ' rate_bad_count = rate_bad_count + '.intval($params['rate_bad_count']);
            $rateCount += $params['rate_bad_count'];
        }

        if( empty($setSql) ) return false;

        $setSql[] = ' rate_count = rate_count +'. intval($rateCount);

        $db = app::get('sysitem')->database();
        return $db->executeUpdate("UPDATE sysitem_item_count SET ".implode(',', $setSql)." WHERE item_id = ?", [$params['item_id']]);
    }
}

