<?php
class topm_ctl_member_point extends topm_ctl_member{

    public function point()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = $this->limit;
        $current = ($filter['pages'] >=1 || $filter['pages'] <= 100) ? $filter['pages'] : 1;

        $params = array(
            'page_no' => $current,
            'page_size' => $pageSize,
            'orderBy' => 'modified_time desc'
        );
        $data = app::get('topm')->rpcCall('user.pointGet',$params);

        //总页数(数据总数除每页数量)
        $pagedata['userpoint'] = $data['datalist']['user'];
        $pagedata['pointdata'] = $data['datalist']['point'];
        if($data['totalnum'] > 0) $total = ceil($data['totalnum']/$pageSize);
        $pagedata['count'] = $data['totalnum'];
        $filter['pages'] = time();
        $pagedata['pagers'] = array(
            'link'=>url::action('topm_ctl_member_point@point',$filter),
            'current'=>$current,
            'total'=>$total,
        );
        $pagedata['title'] = "我的积分";
        return $this->page('topm/member/point/index.html',$pagedata);
    }
}
