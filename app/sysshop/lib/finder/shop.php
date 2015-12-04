<?php
class sysshop_finder_shop{

    public $column_edit = '操作';
    public $column_edit_order = 3;
    public $column_edit_width = 200;


    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $colList[$k] = $this->_column_edit($row);
        }
    }

    public function _column_edit($row)
    {
        $sellerModle = app::get('sysshop')->model('seller');
        $sellerId = $sellerModle->getRow('seller_id',array('shop_id'=>$row['shop_id']));

        if($row['status'] == 'active')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=doUpdateShopStatus&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]=dead&p[2]='.$row['shop_name'];
            $title = app::get('sysshop')->_('关闭店铺');
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('关闭店铺').'\', width:400, height:260}';
            $return = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        elseif($row['status'] == 'dead')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=doUpdateShopStatus&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]=active&p[2]='.$row['shop_name'];
            $title = app::get('sysshop')->_('开启店铺');
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('开启店铺').'\', width:300, height:150}';
            $return = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        if($row['shop_type'] != 'self')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=updateShopInfo&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$sellerId['seller_id'].'&p[1]='.$row['shop_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('编辑入驻申请资料').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $target = '_blank';
            $title = app::get('sysshop')->_('类目费用');
            $url = '?app=desktop&amp;act=alertpages&amp;goto=%3Fapp%3Dsysshop%26ctl%3Dadmin_shop%26act%3DcatFeeList%26shopId%3D'.$row['shop_id'].'%26nobutton%3D1';
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }

        if($row['shop_type'] == 'self')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=updateSelfShop&finder_id='.$_GET['_finder']['finder_id'].'&p[1]='.$row['shop_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('编辑入驻申请资料').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }

        $url = '?app=sysshop&ctl=admin_shopnotice&act=addNotice&finder_id='.$_GET['_finder']['finder_id'].'&shop_id='.$row['shop_id'];
        $target = 'dialog::  {title:\''.app::get('sysshop')->_('').'\', width:500, height:400}';
        $title = app::get('sysshop')->_('添加商家通知');
        $return .= ' |<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

        return $return;
    }
}
