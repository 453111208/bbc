<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topm_ctl_member_rate extends topm_ctl_member {

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 显示新增评价页面
     */
    public function createRate()
    {
        $tid = input::get('tid');

        $tradeFiltr = array(
            'tid' => $tid,
            'fields' => 'tid,user_id,buyer_rate,shop_id,status,created_time,end_time,orders.oid,anony,orders.user_id,orders.price,orders.item_id,orders.sku_id,orders.title,orders.pic_path,orders.num',
        );
        $tradeInfo= app::get('topm')->rpcCall('trade.get', $tradeFiltr);

        if( $tradeInfo['buyer_rate'] == '0' )
        {
            $tradeInfo['is_buyer_rate'] = true;
        }
        else
        {
            redirect::action('topm_ctl_member@index')->send();exit;
        }

        if( $tradeInfo['user_id'] != userAuth::id() )
        {
            redirect::action('topm_ctl_member@index')->send();exit;
        }

        $pagedata['tradeInfo'] = $tradeInfo;

        $this->action_view = "topm/member/rate/add.html";
        return $this->page($this->action_view, $pagedata);
    }

    //创建评价
    public function doCreateRate()
    {
        $params['tid'] = input::get('tid');
        $params['tally_score'] = input::get('tally_score');
        $params['attitude_score'] = input::get('attitude_score');
        $params['delivery_speed_score'] = input::get('delivery_speed_score');
        $params['logistics_service_score'] = input::get('logistics_service_score');

        $anony = input::get('anony');
        foreach( input::get('rate_data') as $key=>$row )
        {
            $rateData[$key] = $row;
            if( $row['rate_pic'] )
            {
                $rateData[$key]['rate_pic'] = implode(',', $row['rate_pic']);
            }
            $rateData[$key]['anony'] = ($anony == 'true') ? 1 : 0;
        }
        $params['rate_data'] = json_encode($rateData);
        try
        {
            $result = app::get('topm')->rpcCall('rate.add', $params, 'buyer');
        }
        catch(\LogicException $e)
        {
            $result = false;
            $msg = $e->getMessage();
        }

        if( !$result )
        {
            return $this->splash('error',$url,$msg,true);
        }

        $url = url::action('topm_ctl_member_trade@index');

        $msg = '评价提交成功';
        return $this->splash('success',$url,$msg,true);
    }
}
