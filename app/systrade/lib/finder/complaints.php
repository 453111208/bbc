<?php

class systrade_finder_complaints {

    public $column_edit = '操作';
    public $column_edit_order = 1;
    public $column_edit_width = 10;

    public function column_edit(&$colList, $list)
    {
        foreach( $list as $k=>$row )
        {
            if( $row['status'] == 'WAIT_SYS_AGREE' )
            {
                $title = '处理投诉';
            }
            else
            {
                $title = '查看';
            }

            $url = '?app=systrade&ctl=admin_complaints&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['complaints_id'];

            $target = 'dialog::{title:\''.app::get('systrade')->_('处理订单投诉').'\', width:800, height:400}';
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }

    public $column_shopname = "被投商家";
    public $column_shopname_order = 31;
    public $column_shopname_width = 120;
    public function column_shopname(&$colList, $list)
    {
        $shopIds = array_column($list, 'shop_id');
        $shopIds = implode(',',$shopIds);
        if( $shopIds )
        {
            $shopData = app::get('systrade')->rpcCall('shop.get.shopname',array('shop_id'=>$shopIds));
            foreach($list as $k=>$row)
            {
                $colList[$k] = $shopData[$row['shop_id']];
            }
        }
    }

    public $column_username = "投诉来源";
    public $column_username_order = 31;
    public $column_username_width = 120;
    public function column_username(&$colList, $list)
    {
        $userIds = array_column($list, 'user_id');
        $userIds = implode(',', $userIds);
        if( $userIds )
        {
            $userData = app::get('systrade')->rpcCall('user.get.account.name',array('user_id'=>$userIds));
            foreach($list as $k=>$row)
            {
                $colList[$k] = $userData[$row['user_id']];
            }
        }
    }
}

