<?php
class syslogistics_api_tradeDelivery{
    public $apiDescription = "订单发货（ erp）";
    public function getParams()
    {
        $return['params'] = array(
            //基础信息
            'delivery_id' =>['type'=>'string','valid'=>'', 'description'=>'发货单流水编号','default'=>'','example'=>'1'],
            'tid' =>['type'=>'string','valid'=>'required', 'description'=>'订单号','default'=>'','example'=>'1'],
            'seller_id' =>['type'=>'int','valid'=>'required', 'description'=>'卖家id','default'=>'','example'=>'1'],
            'user_id' =>['type'=>'int','valid'=>'required', 'description'=>'买家id','default'=>'','example'=>'1'],
            'shop_id' =>['type'=>'int','valid'=>'required', 'description'=>'订单所属店铺','default'=>'','example'=>'1'],

            //收货地址
            //'receiver_name' =>['type'=>'string','valid'=>'required', 'description'=>'收货人名称','default'=>'','example'=>'1'],
            //'receiver_state' =>['type'=>'string','valid'=>'required', 'description'=>'收货人所在省','default'=>'','example'=>'1'],
            //'receiver_city' =>['type'=>'string','valid'=>'required', 'description'=>'收货人所在市','default'=>'','example'=>'1'],
            //'receiver_district' =>['type'=>'string','valid'=>'required', 'description'=>'收货人所在地区','default'=>'','example'=>'1'],
            //'receiver_address' =>['type'=>'string','valid'=>'required', 'description'=>'收货人详细地址','default'=>'','example'=>'1'],
            //'receiver_zip' =>['type'=>'string','valid'=>'required', 'description'=>'收货人邮编','default'=>'','example'=>'1'],
            //'receiver_mobile' =>['type'=>'string','valid'=>'', 'description'=>'收货人手机号(和电话任一不能为空)','default'=>'','example'=>'1'],
            //'receiver_phone' =>['type'=>'string','valid'=>'', 'description'=>'收货人电话','default'=>'','example'=>'1'],

            //配送方式及物流信息
            'post_fee' =>['type'=>'string','valid'=>'required', 'description'=>'运费','default'=>'','example'=>'1'],
            'template_name' =>['type'=>'string','valid'=>'required', 'description'=>'运费模板名称','default'=>'','example'=>'1'],
            'logi_no' =>['type'=>'string','valid'=>'required', 'description'=>'运单号','default'=>'','example'=>'1'],
            'corp_code' =>['type'=>'string','valid'=>'required', 'description'=>'物流公司编码','default'=>'','example'=>'1'],

            //子订单信息(发货的商品信息)
            'items' =>['type'=>'json','valid'=>'required', 'description'=>'发货单明细,json格式','default'=>'','example'=>'1'],
            'memo' =>['type'=>'string','valid'=>'', 'description'=>'备注','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function tradeDelivery($params)
    {
        $item = json_decode($params['items'],true);
        unset($params['items']);
        $item = $this->__checkItems($item);
        if(!$item)
        {
            throw new LogicException('发货明细不存在');
        }
        $params['items'] = $item;

        $objMdlDlytmpl = app::get('syslogistics')->model('dlytmpl');
        $objMdlDlyCorp = app::get('syslogistics')->model('dlycorp');
        //根据配送方式模板名称获取模板信息
        $tmpl = $objMdlDlytmpl->getRow('corp_id,template_id',array('name' => $params['template_name']));
        //根据物流公司编号查询物流公司信息
        if($tmpl)
        {
            $corp = $objMdlDlyCorp->getRow('corp_name,corp_code,corp_id',array('corp_id'=>$tmpl['corp_id'],'corp_code'=>$params['corp_code']));
        }

        if(!$corp)
        {
            throw new LogicException('发货物理公司不存在');
        }

        $params['logi_id'] = $corp['corp_id'];
        $params['template_id'] = $tmpl['template_id'];
        $params['logi_name'] = $corp['corp_name'];
        $params['corp_code'] = $corp['corp_code'];

        $objLogisticsDelivery = kernel::single('syslogistics_data_delivery');
        $result = $objLogisticsDelivery->doDelivery($params);
        return $result;
    }

    private function __checkItems($item)
    {
        if(!is_array($item))
        {
            throw new LogicException('item参数格式不正确');
        }
        $deliveryDetailValidate = array(
            'oid' =>'required',
            'sku_id' =>'required|numeric',
            'num' =>'required|numeric',
            'title' =>'required',
            'bn' =>'required',
        );
        foreach($item as $k => $val)
        {
            $validator = validator::make($val, $deliveryDetailValidate);
            if( $validator->fails() )
            {
                $errors = json_decode( $validator->messages(), 1 );
                foreach( $errors as $error )
                {
                    throw new LogicException( $error[0] );
                }
            }
            $newItem[$k]['oid']       =  $val['oid'];
            $newItem[$k]['sku_bn']    =  $val['bn'];
            $newItem[$k]['sku_id']    =  $val['sku_id'];
            $newItem[$k]['sku_title'] =  $val['title'];
            $newItem[$k]['number']    =  $val['num'];
        }
        return $newItem;
    }
}

