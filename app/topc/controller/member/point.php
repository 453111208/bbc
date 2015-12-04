<?php
class topc_ctl_member_point extends topc_ctl_member {

   public function point()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = $this->limit;
        $current = $filter['pages'] ? $filter['pages'] : 1;

        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
        );

        $data = app::get('topc')->rpcCall('user.pointGet',$params,'buyer');
        //总页数(数据总数除每页数量)
        $pagedata['userpoint'] = $data['datalist']['user'];
        $pagedata['pointdata'] = $data['datalist']['point'];
        if($data['totalnum'] > 0) $total = ceil($data['totalnum']/$pageSize);
        $pagedata['count'] = $data['totalnum'];
        $filter['pages'] = time();
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member_point@point',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        $pagedata['action'] = 'topc_ctl_member_point@point';

        $this->action_view = "point.html";
        return $this->output($pagedata);
    }
}

