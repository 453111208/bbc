<?php
class syspromotion_finder_activity_register{
    public $column_edit = "操作";
    public $column_edit_order = 1;
    public $column_edit_width = 10;

    public function column_edit(&$colList,$list)
    {
        $registerData = app::get('syspromotion')->model('activity');
        foreach($list as $k=>$row)
        {
            $url = url::route('shopadmin', ['app'=>'syspromotion','act'=>'index','ctl'=>'admin_activity_register','finder_id'=>$_GET['_finder']['finder_id'],'id'=>$row['id'],'finderview'=>'detail_basic','action'=>'detail','singlepage'=>'true']);
            $data = $registerData->getRow('activity_id', array('activity_id'=>$row['activity_id'],'release_time|sthan'=>time()));
            if($data)
            {
                $colList[$k] = '无法审核';
            }
            elseif($row['verify_status']=='pending' )
            {
                $colList[$k] = '<a href="'.$url.'" target="_blank" title="审核">审核</a>';
            }

            if($row['verify_status']=='agree')
            {
                $colList[$k] = '审核通过';
            }
            if($row['verify_status']=='refuse')
            {
                $colList[$k] = '审核驳回';
            }

        }
    }

    public $detail_basic = '报名信息';
    public function detail_basic($id)
    {
        $registerData = app::get('syspromotion')->model('activity_register')->getRow('*', array('id'=>$id));

        // 获取活动规则信息
        $activityParams = array(
            'activity_id' => $registerData['activity_id'],
            'fields' => '*',
        );
        $pagedata = app::get('topshop')->rpcCall('promotion.activity.info', $activityParams);
        $pagedata['limit_cat_str'] = implode(',', $pagedata['limit_cat']);
        $pagedata['shoptype_str'] = implode(',', $pagedata['shoptype']);
        $pagedata['shop_id'] = $registerData['shop_id'];
        $pagedata['verify_status'] = $registerData['verify_status'];
        $pagedata['id'] = $registerData['id'];

        // 获取商家活动报名的商品信息
        $itemParams = array(
            'fields' => '*',
            'shop_id' => $registerData['shop_id'],
            'activity_id' => $registerData['activity_id'],
        );

        $registerItemList = app::get('topshop')->rpcCall('promotion.activity.item.list', $itemParams);
        $pagedata['itemsList'] = $registerItemList['list'];
        $pagedata['now_time'] = time();

        return view::make('syspromotion/activity/register/detail.html',$pagedata)->render();
    }

}
