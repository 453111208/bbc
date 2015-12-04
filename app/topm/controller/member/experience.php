<?php
class topm_ctl_member_experience extends topm_ctl_member{

    public function experience()
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

        $pagedata['grade'] = app::get('topm')->rpcCall('user.grade.fullinfo', [], 'buyer');

        $data = app::get('topm')->rpcCall('user.experienceGet',$params);
        //总页数(数据总数除每页数量)
        $pagedata['userexp'] = $data['datalist']['user'];
        $pagedata['experiencedata'] = $data['datalist']['exp'];
        if($data['totalnum'] > 0) $total = ceil($data['totalnum']/$pageSize);
        $pagedata['count'] = $data['totalnum'];
        $filter['pages'] = time();
        $pagedata['pagers'] = array(
            'link'=>url::action('topm_ctl_member_experience@experience',$filter),
            'current'=>$current,
            'total'=>$total,
        );
        $pagedata['title'] = "我的成长值";
        return $this->page('topm/member/experience/index.html',$pagedata);
    }

    public function grade()
    {

        $grade = app::get('topm')->rpcCall('user.grade.fullinfo','','buyer');
        foreach($grade['gradeList'] as $key=>$val)
        {
            $pagedata['grade'][] = array(
                'name' => $key+1,
                'descritpion' => $val['grade_name'],
                'number' => $val['experience'],
            );
        }
        $pagedata['count'] = count($grade['gradeList']);
        $pagedata['title'] = "成长值体系";
        return $this->page('topm/member/experience/grade.html',$pagedata);
    }
}
