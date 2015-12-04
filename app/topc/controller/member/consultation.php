<?php
class topc_ctl_member_consultation extends topc_ctl_member{

    public $limit = "10";
    public function index()
    {
        $pagedata = $this->__getGackData();
        $pagedata['action'] = 'topc_ctl_member_consultation@index';
        $this->action_view = "consultation/index.html";
        return $this->output($pagedata);
    }

    private function __getGackData()
    {
        $current = input::get('pages',1);
        $params = ['user_id'=>userAuth::id(),'page_no'=>$current,'page_size'=>$this->limit,'fields'=>'*'];
        $filter = input::get();
        $pagedata['filter'] = $filter;

        if( in_array(input::get('type'), ['item','store_delivery','payment', 'invoice']) )
        {
            $params['consultation_type'] = input::get('type');
        }

        //print_r($params); exit;
        $data = app::get('topc')->rpcCall('rate.gask.list', $params,'buyer');

        $pagedata['gask'] = $data['lists'];

        //处理翻页数据
        $filter['pages'] = time();
        if($data['total_results']>0) $total = ceil($data['total_results']/$this->limit);
        $current = $total < $current ? $total : $current;
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member_consultation@index',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        return $pagedata;
    }

    public function doDelete()
    {
        if(!input::get('id'))
        {
            return $this->splash('error',$url,"没有要删除的内容",true);
        }
        $params['id'] = implode(',',input::get('id'));
        $params['user_id'] = userAuth::id();
        try
        {
            $result = app::get('topc')->rpcCall('rate.gask.delete', $params,'buyer');
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

        $url = url::action('topc_ctl_member_consultation@index');
        $msg = '删除成功';
        return $this->splash('success',$url,$msg,true);

    }
}
