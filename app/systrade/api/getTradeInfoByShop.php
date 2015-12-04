<?php

class systrade_api_getTradeInfoByShop {

    /**
     * 接口作用说明
     */
    public $apiDescription = '(商家)获取单笔交易信息';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单编号'],
            'oid' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'子订单编号，返回指定子订单编号的orders数据结构'],
            'fields'=> ['type'=>'field_list','valid'=>'required', 'default'=>'', 'example'=>'*,orders.*', 'description'=>'获取单个订单需要返回的字段'],
        );

        //如果参数fields中存在orders，则表示需要获取子订单的数据结构
        $return['extendsFields'] = ['orders'];

        return $return;
    }

    /**
     * 获取单笔交易数据
     *
     * @param array $params 接口传入参数
     * @return array
     */
    public function getData($params, $oauth)
    {
        if(isset($oauth['shop_id']))
        {
            $filter['shop_id'] = $oauth['shop_id'];
        }
        else
        {
            if($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "member" )
            {
                $filter['user_id'] = $params['oauth']['account_id'];
            }
            elseif($params['oauth']['account_id'] && $params['oauth']['auth_type'] == "shop")
            {
                $sellerId = $params['oauth']['account_id'];
                $filter['shop_id'] = app::get('systrade')->rpcCall('shop.get.loginId',array('seller_id'=>$this->sellerId),'seller');
            }
        }

        if( $params['oid'] )
        {
            $params['oid'] = explode(',',$params['oid']);
        }
        $tradeInfo = kernel::single('systrade_getTradeData')->getTradeInfo($params['fields'], $params['tid'], $params['oid'], $filter);
        if($tradeInfo['dlytmpl_id'])
        {
            $dlytmpl = app::get('systrade')->rpcCall('logistics.dlytmpl.get', ['template_id'=>$tradeInfo['dlytmpl_id'], 'fields'=>'corp_id,name']);
            $corptmpl = app::get('systrade')->rpcCall('logistics.dlycorp.get', ['corp_id'=>$dlytmpl['corp_id'], 'fields'=>'corp_code,corp_name']);
            $tradeInfo['dlytmpl_name'] = $dlytmpl['name'];
            $tradeInfo['corptmpl_name'] = $corptmpl['corp_name'];
            $tradeInfo['corptmpl_code'] = $corptmpl['corp_code'];
        }

        //这里判断货到付款
        //现在判断货到付款在订单里没有字段标示，所以采用两个字段相结合，就是支付为线下支付且订单状态是待支付的时候，就判断为货到付款
        if($tradeInfo['pay_type'] == "offline" && $tradeInfo['type'] == "WAIT_SELLER_SEND_GOODS")
        {
            $tradeInfo['is_cod'] = "true";
        }
        else
        {
            $tradeInfo['is_cod'] = "false";
        }

        $tradeInfo = $this->__paramsToString($tradeInfo);
        return $tradeInfo;
    }

    private function __paramsToString($from)
    {
        $to = array();
        if( is_array($from) )
        {
            foreach($from as $k=>$v)
            {
                $to[$k] = $this->__paramsToString($v);
            }
        }
        else
        {
            return (string)$from;
        }
        return $to;


    }
}
