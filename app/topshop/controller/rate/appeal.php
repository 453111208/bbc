<?php
class topshop_ctl_rate_appeal extends topshop_controller {

    //申诉列表页
    public function appealList()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('申诉管理-申诉列表');
        $pagedata = $this->__searchAppealData();
        return $this->page('topshop/rate/appeal/index.html', $pagedata);
    }

    public function search()
    {
        $pagedata = $this->__searchAppealData();
        return view::make('topshop/rate/appeal/list.html', $pagedata);
    }

    //申诉详情
    public function appeaInfo()
    {
        $this->contentHeaderTitle = app::get('topshop')->_('申诉管理-申诉详情');

        $pagedata['type'] = input::get('type');
        $pagedata['again'] = true;

        $fields = 'rate_id,item_id,item_title,item_pic,item_price,result,content,rate_pic,created_time,is_appeal,appeal_status,appeal.appeal_id,appeal.content,appeal.appeal_type,appeal.evidence_pic,appeal.reject_reason,appeal.appeal_log';
        $params = ['role'=>'seller','rate_id'=>input::get('rate_id',false),'fields'=>$fields];
        $data = app::get('topshop')->rpcCall('rate.get', $params,'seller');
        if( empty($data['appeal']) )
        {
            //跳转
        }

        if( $data['appeal']['evidence_pic'] )
        {
            $data['appeal']['evidence_pic'] = explode(',',$data['appeal']['evidence_pic']);
        }
        if($data['rate_pic'])
        {
            $data['rate_pic'] = explode(',',$data['rate_pic']);
        }
        $pagedata['rate'] = $data;

        return $this->page('topshop/rate/appeal/info.html', $pagedata);
    }

    //评价搜索
    private function __searchAppealData()
    {
        $current = input::get('pages',1);
        $fields = 'rate_id,tid,item_title,result,item_id,item_price,user_id,appeal_status,appeal_again,appeal_time,appeal.content,appeal.rate_id,appeal.evidence_pic';
        $params = ['role'=>'seller','page_no'=>$current,'page_size'=>10,'fields'=>$fields];
        $params['appeal_status'] = input::get('appeal_status','WAIT,REJECT,SUCCESS,CLOSE');
        if(  input::get('appeal_again','all') != 'all' )
        {
            $params['appeal_again'] = input::get('appeal_again');
        }

        if( input::get('item_title') )
        {
            $params['item_title'] = input::get('item_title');
        }

        if( input::get('appeal_time') )
        {
            $timeData = explode('-', input::get('appeal_time'));
            $startTime = explode('/',trim($timeData[0]));
            $endTime = explode('/',trim($timeData[1]));
            $appealStartTime = mktime(0,0,0,$startTime[1],$startTime[2],$startTime[0]);
            $appealEndTime = mktime(24,60,60,$endTime[1],$endTime[2],$endTime[0]);
            $params['appeal_start_time'] = $appealStartTime;
            $params['appeal_end_time'] = $appealEndTime;
        }

        $filter = input::get();
        $pagedata['filter'] = $filter;

        $data = app::get('topshop')->rpcCall('rate.list.get', $params,'seller');
        $pagedata['rate']= $data['trade_rates'];
        foreach( $pagedata['rate'] as $k=>$row)
        {
            $userId[] = $row['user_id'];

            if( $row['appeal']['evidence_pic'] )
            {
                $pagedata['rate'][$k]['appeal']['evidence_pic'] = explode(',', $row['appeal']['evidence_pic']);
            }
        }

        if( $userId )
        {
            $pagedata['userName'] = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => implode(',', $userId)], 'seller');
        }

        //处理翻页数据
        $filter = input::get();
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

    //评价申诉
    public function appeal()
    {
        $params['rate_id'] = input::get('rate_id');
        $params['is_again'] = input::get('is_again', false);
        $params['appeal_type'] = input::get('appeal_type');
        $params['content'] = input::get('appeal_content');
        if( input::get('evidence_pic') )
        {
            $params['evidence_pic'] = implode(',',input::get('evidence_pic'));
        }

        if( $params['is_again'] && !input::get('evidence_pic') )
        {
            $msg = '再次申诉，图片凭证必填';
            return $this->splash('error',$url,$msg,true);
        }

        try {
            $flag = app::get('topshop')->rpcCall('rate.appeal.add', $params,'seller');
            $status = $flag ? 'success' : 'error';
            $msg = $flag ? app::get('topshop')->_('申诉已提交，请耐心等待，我们将会在10个工作日内给予审核回复，谢谢！ ') : app::get('topshop')->_('申诉提交失败，请重新再试');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $status = 'error';
        }

        if( $params['is_again'] )
        {
            $url = url::action('topshop_ctl_rate_appeal@appeaInfo',['rate_id'=>$params['rate_id']]);
        }
        else
        {
            $url = url::action('topshop_ctl_rate@detail',['rate_id'=>$params['rate_id']]);
        }
        return $this->splash($status,$url,$msg,true);
    }
}


