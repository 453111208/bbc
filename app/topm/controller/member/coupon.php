<?php
class topm_ctl_member_coupon extends topm_ctl_member{

    // 优惠券列表
    public function couponList()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            // $filter['pages'] = 1;
        }
        switch ($filter['is_valid']) {
            case 'no':
                $filter['is_valid']=1;
                break;
            case 'al':
                $filter['is_valid']=0;
                break;
            case 'ex':
                $filter['is_valid']=2;
                break;
            default:
                $filter['is_valid']=1;
                break;
        }
        $pageSize = 10;
        $params = array(
            // 'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'user_id'=>userAuth::id(),
            'is_valid'=>$filter['is_valid'],
        );

        $couponListData = app::get('topm')->rpcCall('user.coupon.list', $params, 'buyer');

        $count = $couponListData['count'];
        $couponList = $couponListData['coupons'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topm_ctl_member_coupon@couponList',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['couponList']= $couponList;
        $pagedata['count'] = $count;
        $pagedata['action'] = 'topm_ctl_member_coupon@couponList';

        $pagedata['title'] = "我的优惠券";
        // return $this->page('topm/member/couponlist.html',$pagedata);
        return $this->page('topm/member/coupon/index.html',$pagedata);
    }

    public function ajaxCouponData()
    {
        $filter = input::get();
        if(!$filter['pages'])
        {
            $filter['pages'] = 1;
        }
        $pageSize = 10;
        $params = array(
            'page_no' => $pageSize*($filter['pages']-1),
            'page_size' => $pageSize,
            'fields' =>'*',
            'user_id'=>userAuth::id(),
        );
        $couponListData = app::get('topm')->rpcCall('user.coupon.list', $params, 'buyer');

        $count = $couponListData['count'];
        $couponList = $couponListData['coupons'];

        //处理翻页数据
        $current = $filter['pages'] ? $filter['pages'] : 1;
        $filter['pages'] = time();
        if($count>0) $total = ceil($count/$pageSize);
        $pagedata['pagers'] = array(
            'link'=>url::action('topm_ctl_member_coupon@couponList',$filter),
            'current'=>$current,
            'total'=>$total,
            'token'=>$filter['pages'],
        );
        $pagedata['couponList']= $couponList;
        $pagedata['count'] = $count;
        $pagedata['action'] = 'topm_ctl_member_coupon@couponList';


        if( input::get('json') )
        {
            $data['html'] = view::make('topm/member/coupon/list.html',$pagedata)->render();
            $data['pagers'] = $pagedata['pagers'];
            $data['success'] = true;
            return response::json($data);exit;
        }

        return view::make('topm/member/coupon/list.html',$pagedata);
    }

    public function couponDetail()
    {
        $coupon_id = input::get('coupon_id');
        $pagedata['couponInfo'] = app::get('topm')->rpcCall('promotion.coupon.get', array('coupon_id'=>$coupon_id));
        // 获取会员等级名称
        $validGrade = explode(',', $pagedata['couponInfo']['valid_grade']);
        $gradeList = app::get('topm')->rpcCall('user.grade.list', array(), 'buyer');
        $gradeNameArr = array_bind_key($gradeList, 'grade_id');
        $validGradeNameArr = array();
        foreach($validGrade as $v)
        {
            $validGradeNameArr[] = $gradeNameArr[$v]['grade_name'];
        }
        $pagedata['couponInfo']['valid_grade_name'] = implode(',', $validGradeNameArr);
        return $this->page('topm/member/coupon/couponDetail.html', $pagedata);
    }

    // 删除用户领取的优惠券
    /*public function deleteCoupon()
    {
        $postCouponCodes = input::get('coupon_code');
        foreach ($postCouponCodes as $code)
        {
            $params = array('coupon_code' => $code);
            $res = app::get('topm')->rpcCall('user.coupon.remove', $params, 'buyer');
            if( $res === false )
            {
                $msg = app::get('topm')->_('删除优惠券失败');
                return $this->splash('error', null, $msg, true);
            }
        }
        return $this->splash('success',null,  $msg, true);
    }*/

}
