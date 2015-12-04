<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_member_rate extends topc_ctl_member {

   /*
     * 显示新增评价页面
     */
    public function createRate()
    {
        $tid = input::get('tid');

        $tradeFiltr = array(
            'tid' => $tid,
            'fields' => 'tid,user_id,buyer_rate,shop_id,status,created_time,end_time,orders.oid,anony,orders.user_id,orders.price,orders.item_id,orders.sku_id,orders.title,orders.pic_path,orders.aftersales_status',
        );
        $tradeInfo= app::get('topc')->rpcCall('trade.get', $tradeFiltr,'buyer');

        if( $tradeInfo['buyer_rate'] == '0' )
        {
            $tradeInfo['is_buyer_rate'] = true;
        }
        else
        {
            redirect::action('topc_ctl_member@index')->send();exit;
        }

        if( $tradeInfo['user_id'] != userAuth::id() )
        {
            redirect::action('topc_ctl_member@index')->send();exit;
        }

        $pagedata['tradeInfo'] = $tradeInfo;
        $pagedata['action'] = 'topc_ctl_member_rate@createRate';

        $this->action_view = "rate/add.html";
        return $this->output($pagedata);
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
            $result = app::get('topc')->rpcCall('rate.add', $params,'buyer');
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

        $url = url::action('topc_ctl_member_trade@tradeList');

        $msg = '评价提交成功';
        return $this->splash('success',$url,$msg,true);
    }

    //用户中心评价列表
    public function index()
    {
        $pagedata = $this->__getItemData();

        $pagedata['action'] = 'topc_ctl_member_rate@index';
        $this->action_view = "rate/index.html";
        return $this->output($pagedata);
    }

    //用户中心评价列表的数据页面
    public function ratelist()
    {
        $pagedata = $this->__getItemData();
        return  view::make('topc/member/rate/list.html', $pagedata);
    }

    private function __getItemData()
    {
        $current = input::get('pages',1);
        $params = ['role'=>'buyer','page_no'=>$current,'page_size'=>$this->limit,'fields'=>'*'];
        $filter = input::get();
        $pagedata['filter'] = $filter;

        if( input::get('content') )
        {
            $params['is_content'] = true;
        }
        if( input::get('picture') )
        {
            $params['is_pic'] = true;
        }

        if( input::get('is_reply') )
        {
            $params['is_reply'] = input::get('is_reply');
        }

        if( in_array(input::get('result'), ['good','bad', 'neutral']) )
        {
            $params['result'] = input::get('result');
        }
        $data = app::get('topc')->rpcCall('rate.list.get', $params,'buyer');
        $pagedata['rate']= $data['trade_rates'];
        foreach( $pagedata['rate'] as $k=>$row)
        {
            $userId[] = $row['user_id'];
            if( $row['rate_pic'] )
            {
                $pagedata['rate'][$k]['rate_pic'] = explode(',', $row['rate_pic']);
            }
        }

        //处理翻页数据
        $filter['pages'] = time();
        if($data['total_results']>0) $total = ceil($data['total_results']/$this->limit);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member_rate@index',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        return $pagedata;
    }

    /**
     * 设置评价匿名
     */
    public function setAnony()
    {
        $params['rate_id'] = input::get('rate_id');
        try
        {
            $params['user_id'] = userAuth::id();
            $result = app::get('topc')->rpcCall('rate.set.anony', $params);
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

        $url = url::action('topc_ctl_member_rate@index');

        $msg = '设置成功';
        return $this->splash('success',$url,$msg,true);
    }

    //删除评价
    public function doDelete()
    {
        $params['rate_id'] = input::get('rate_id');
        try
        {
            $result = app::get('topc')->rpcCall('rate.delete', $params,'buyer');
            $msg = '删除失败';
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

        $url = url::action('topc_ctl_member_rate@index');
        $msg = '删除成功';
        return $this->splash('success',$url,$msg,true);

    }

    public function edit()
    {
        $params['rate_id'] = input::get('rate_id',false);
        $params['role'] = 'buyer';
        $params['fields'] = '*';
        $data = app::get('topc')->rpcCall('rate.get', $params,'buyer');
        if( empty($data) || $data['is_lock'] )
        {
            redirect::action('topc_ctl_member_rate@index')->send();exit;
        }

        if( $data['rate_pic'] )
        {
            $data['rate_pic'] = explode(',',$data['rate_pic']);
        }
        $pagedata['rateInfo'] = $data;

        $this->action_view = "rate/edit.html";
        return $this->output($pagedata);
    }

    public function update()
    {
        $params['rate_id'] = input::get('rate_id');
        $params['result'] = input::get('result');
        $params['content'] = input::get('content');
        if( input::get('rate_pic') )
        {
            $params['rate_pic'] = implode(',',input::get('rate_pic'));
        }
        try
        {
            $result = app::get('topc')->rpcCall('rate.update', $params,'buyer');
        }
        catch(\LogicException $e)
        {
            $result = false;
            $msg = $e->getMessage();
        }

        if( !$result )
        {
            $msg = $msg ? $msg : '修改失败';
            return $this->splash('error',$url,$msg,true);
        }

        $url = url::action('topc_ctl_member_rate@index');
        $msg = '修改成功';
        return $this->splash('success',$url,$msg,true);

    }
}


