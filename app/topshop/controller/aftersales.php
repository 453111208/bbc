<?php
class topshop_ctl_aftersales extends topshop_controller {

    public function index()
    {
        $params['shop_id'] = $this->shopId;
        $params['page_no'] = input::get('pages',1);
        $params['fields'] = 'aftersales_bn,aftersales_type,shop_id,created_time,oid,tid,num,progress,status,sku';

                $pagedata['refund_type'] = array(
            'ONLY_REFUND' => app::get('topshop')->_('仅退款'),
            'REFUND_GOODS' => app::get('topshop')->_('退货退款'),
            'EXCHANGING_GOODS' => app::get('topshop')->_('换货'),
        );
        $pagedata['progress'] = array(
            '0' => app::get('topshop')->_('待处理'),
            '1' => app::get('topshop')->_('待回寄'),
            '2' => app::get('topshop')->_('待确认收货'),
            '4' => app::get('topshop')->_('商家已处理'),//换货的时候可以直接在商家处理结束
            '3' => app::get('topshop')->_('商家已驳回'),
            '5' => app::get('topshop')->_('待平台处理'),
            '7' => app::get('topshop')->_('平台已处理'),//退款，退货则需要平台确实退款
            '6' => app::get('topshop')->_('平台已驳回'),
        ) ;

        $result = app::get('topshop')->rpcCall('aftersales.list.get', $params,'seller');
        $pagedata['list'] = $result;


        return $this->page('topshop/aftersales/list.html', $pagedata);
    }

    public function detail()
    {
        $params['aftersales_bn'] = input::get('bn');
        $params['shop_id'] = $this->shopId;
        $tradeFields = 'trade.status,trade.receiver_name,trade.user_id,trade.post_fee,trade.receiver_state,trade.receiver_city,trade.created_time,trade.receiver_district,trade.receiver_address,trade.receiver_mobile,trade.receiver_phone';
        $params['fields'] = 'aftersales_bn,shop_id,aftersales_type,sendback_data,sendconfirm_data,shop_explanation,admin_explanation,user_id,reason,evidence_pic,created_time,oid,tid,num,progress,status,sku,'.$tradeFields;
        try{
            $result = app::get('topshop')->rpcCall('aftersales.get', $params,'seller');
        }
        catch(Exception $e)
        {
            redirect::action('topshop_ctl_aftersales@index')->send();exit;
        }
        $result['evidence_pic'] = $result['evidence_pic'] ? explode(',',$result['evidence_pic']) : null;
        $result['sendback_data'] = $result['sendback_data'] ? unserialize($result['sendback_data']) : null;
        $result['sendconfirm_data'] = $result['sendconfirm_data'] ? unserialize($result['sendconfirm_data']) : null;

        if( $result['user_id'] )
        {
             $userName = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => $result['user_id']], 'seller');
             $pagedata['userName'] = $userName[$result['user_id']];
        }

        if( $result['sendback_data']['corp_code']  && $result['sendback_data']['corp_code'] != "other")
        {
            $logiData = explode('-',$result['sendback_data']['corp_code']);
            $result['sendback_data']['corp_code'] = $logiData[0];
            $result['sendback_data']['logi_name'] = $logiData[1];
            if( $result['sendback_data']['logi_no'] && $result['sendback_data']['corp_code'] )
            {
                try{
                    $tracker = app::get('topshop')->rpcCall('logistics.tracking.get.hqepay',array('logi_no'=>$result['sendback_data']['logi_no'],'corp_code'=>$result['sendback_data']['corp_code']));
                }catch(Exception $e){
                }
                $pagedata['tracker'] = $tracker;
            }
        }

        if( $result['sendconfirm_data']['corp_code'] && $result['sendconfirm_data']['corp_code'] != "other")
        {
            $logiData = explode('-',$result['sendconfirm_data']['corp_code']);
            $result['sendconfirm_data']['corp_code'] = $logiData[0];
            $result['sendconfirm_data']['logi_name'] = $logiData[1];
            if( $result['sendconfirm_data']['logi_no'] && $result['sendconfirm_data']['corp_code'] )
            {
                try{
                    $tracker = app::get('topshop')->rpcCall('logistics.tracking.get.hqepay',array('logi_no'=>$result['sendconfirm_data']['logi_no'],'corp_code'=>$result['sendconfirm_data']['corp_code']));
                }catch(Exception $e){
                }
                $pagedata['sendTracker'] = $tracker;
            }
        }

        //快递公司代码
        $params['fields'] ="corp_code,corp_name";
        $corpData = app::get('topshop')->rpcCall('logistics.dlycorp.get.list',$params);
        $pagedata['corpData'] = $corpData['data'];

        $pagedata['info'] = $result;

        return $this->page('topshop/aftersales/detail.html', $pagedata);
    }

    public function search()
    {
        $params = input::get();
        $pagedata['filter'] = $params;
        $this->__checkParams($params);
        $params['shop_id'] = $this->shopId;
        $params['page_no'] = input::get('pages',1);
        $params['fields'] = 'aftersales_bn,aftersales_type,shop_id,created_time,oid,tid,num,progress,status,sku';
        $result = app::get('topshop')->rpcCall('aftersales.list.get', $params, 'seller');
        $pagedata['list'] = $result;
        return view::make('topshop/aftersales/item.html', $pagedata);
    }

    private function __checkParams(&$params)
    {
        foreach($params as $key=>$value)
        {
            if(!$value) unset($params[$key]);
            if($key == "progress" && $value == "all") unset($params['progress']);

            if($key == "created_time")
            {
                $times = explode('-',$value);
                if(array_filter($times))
                {
                    $params['created_time']= json_encode($times);
                }
            }
        }
    }

    public function sendConfirm()
    {
        $postdata = input::get();
        $postdata['shop_id'] = $this->shopId;

        if($postdata['corp_code'] == "other" && !$postdata['logi_name'])
        {
            return $this->splash('error',"","其他物流公司不能为空",true);
        }
        if(!$postdata['logi_no']) return $this->splash('error',"","运单号不可为空",true);
        //if(!$postdata['mobile']) return $this->splash('error',"","收货人手机不可为空",true);
        //if(!$postdata['receiver_address']) return $this->splash('error',"","收货地址不可为空",true);

        try
        {
            $result = app::get('topshop')->rpcCall('aftersales.send.confirm',$postdata,'seller');
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',$url,$msg,true);
        }

        $url = url::action('topshop_ctl_aftersales@detail', array('bn'=>$postdata['aftersales_bn']));
        $msg = '操作成功';
        return $this->splash('success',$url,$msg,true);
    }

    /**
     * 审核售后申请
     */
    public function verification()
    {

        $postdata = input::get();
        $postdata['shop_id'] = $this->shopId;
        $url = url::action('topshop_ctl_aftersales@detail', array('bn'=>$postdata['aftersales_bn']));
        try
        {
            $result = app::get('topshop')->rpcCall('aftersales.check',$postdata,'seller');
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',$url,$msg,true);
        }
        return $this->splash('success',$url,'操作成功',true);
    }
}
