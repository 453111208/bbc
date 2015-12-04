<?php
class sysshop_finder_shop{

    public $column_edit = '操作';
    public $column_edit_order = 3;
    public $column_edit_width = 200;

    public $detail_edit2 = '企业分类';
    public function detail_edit2($row){
            $shopcat=app::get('sysshop')->model('shop_rel_lv1cat')->getList("*",array("shop_id"=>$row));
            foreach ($shopcat as $key => $value) {
                # code...
                $cat_id=$value["cat_id"];
                $catInfo=app::get('syscategory')->model('cat')->getRow("*",array("cat_id"=>$cat_id));
                $shopcat[$key]["cat_name"]=$catInfo["cat_name"];

                $shopId=$value["shop_id"];
                $shop=app::get('sysshop')->model('shop')->getRow("*",array("shop_id"=>$shopId));
                $shopcat[$key]["shop_name"]=$shop["shop_name"];
            }
            $pagedata["shopcat"]=$shopcat;
            return view::make('sysshop/admin/shop/detail/detailshop.html', $pagedata)->render();
    }
     public $detail_certificate= '企业资质';
      public function detail_certificate($row){
       // shop_certificate
        // $sql="select shop_id,certificate_img 'img',certificate 'imgname','普通证书' type from sysshop_shop_certificate where shop_id=4 
        //         UNION ALL 
        //         select shop_id,manage_img 'img',manage 'imgname','回收处置证书' type from sysshop_shop_manage where shop_id=4";
        $shop_certificate=app::get('sysshop')->model('shop_certificate')->getList("*",array("shop_id"=>$row));
        $shop_manage=app::get('sysshop')->model('shop_manage')->getList("*",array("shop_id"=>$row));
        $pagedata["shop_certificate"]=$shop_certificate;
        $pagedata["shop_manage"]=$shop_manage;
        $pagedata["shop_id"]=$row;
        return view::make('sysshop/admin/shop/detail/certificate.html', $pagedata)->render();
      }

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
            $title = app::get('sysshop')->_('关闭企业');
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('关闭企业').'\', width:400, height:260}';
            $return = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        elseif($row['status'] == 'dead')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=doUpdateShopStatus&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]=active&p[2]='.$row['shop_name'];
            $title = app::get('sysshop')->_('开启企业');
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('开启企业').'\', width:300, height:150}';
            $return = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        if($row['shop_type'] != 'self')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=updateShopInfo&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$sellerId['seller_id'].'&p[1]='.$row['shop_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('编辑入驻申请资料').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }

        if($row['shop_type'] == 'self')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=updateSelfShop&finder_id='.$_GET['_finder']['finder_id'].'&p[1]='.$row['shop_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('编辑入驻申请资料').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        if($row['is_shopcenter'] == '0')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=openCenter&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]=1&p[2]='.$row['shop_name'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('开通企业展台').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('开通企业展台');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        if($row['is_shopcenter'] == '1')
        {
            $url = '?app=sysshop&ctl=admin_shop&act=openCenter&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]=0&p[2]='.$row['shop_name'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('关闭企业展台').'\', width:400, height:260}';
            $title = app::get('sysshop')->_('关闭企业展台');
            $return .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
        

        $url = '?app=sysshop&ctl=admin_shopnotice&act=addNotice&finder_id='.$_GET['_finder']['finder_id'].'&shop_id='.$row['shop_id'];
        $target = 'dialog::  {title:\''.app::get('sysshop')->_('').'\', width:700, height:500}';
        $title = app::get('sysshop')->_('添加企业通知');
        $return .= ' |<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

        return $return;
    }



}
