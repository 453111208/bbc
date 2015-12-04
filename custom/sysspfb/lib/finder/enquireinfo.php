<?php
class sysspfb_finder_enquireinfo {

    // public $column_edit = '编辑';
    // public $column_edit_order = 1;
    // public function column_edit(&$colList, $list){
    //     foreach($list as $k=>$row)
    //     {
    //         $html = '  <a href=\'?app=sysspfb&ctl=admin_requires&act=addRequire&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['supply_id'].'\'" target="dialog::{title:\''.app::get('sysspfb')->_('编辑').'\', width:640, height:420}">'.app::get('sysspfb')->_('编辑').'</a>';
    //         $objMdlsupply = app::get('sysspfb')->model('requireInfo');
    //         $propInfo = $objMdlsupply->getRow("is_def",array('require_id'=>$row['require_id']));
    //         $colList[$k] = $html;
    //     }
    // }
    public $column_edit2 = "用户名";
    public $column_edit_order2 = 2;
    public $column_edit_width2 = 20;
    public function column_edit2(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $user_id = $row['user_id'];
            $params["user_id"]=$user_id;
            $shopInfo=app::get('sysuser')->model("account")->getRow("*",array("user_id"=>$user_id));
            // $shop_id=$row['shop_id'];
            // $shop_info=app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shop_id));
            $html = '<span>' .  $shopInfo["login_account"] . '</span>';
            $colList[$k] = $html;
        }
    }
}