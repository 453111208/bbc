<?php
class topc_ctl_member_experience extends topc_ctl_member{

    public function experience()
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

        $pagedata['grade'] = app::get('topc')->rpcCall('user.grade.fullinfo','buyer');

        $data = app::get('topc')->rpcCall('user.experienceGet',$params,'buyer');
        //总页数(数据总数除每页数量)
        $pagedata['userexp'] = $data['datalist']['user'];
        $pagedata['experiencedata'] = $data['datalist']['exp'];
        if($data['totalnum'] > 0) $total = ceil($data['totalnum']/$pageSize);
        $pagedata['count'] = $data['totalnum'];
        $filter['pages'] = time();
        $pagedata['pagers'] = array(
            'link'=>url::action('topc_ctl_member_experience@experience',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );

        return $this->page('topc/member/experience.html', $pagedata);
    }


}
