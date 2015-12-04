<?php
class sysitem_finder_item{
    public $column_edit = "操作";
    public $column_edit_order = 1;
    public $column_edit_width = 20;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysitem&ctl=admin_item&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&item_id='.$row['item_id'];
            $target = 'dialog::  {title:\''.app::get('sysinfo')->_('商品审核').'\', width:800, height:500}';
            $title = app::get('sysitem')->_('审核');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }

    public $column_edit2 = "发布企业";
    public $column_edit_order2 = 2;
    public $column_edit_width2 = 20;
    public function column_edit2(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $itemid= $row['item_id'];
            $iteminfo=app::get("sysitem")->model("item")->getRow("shop_id",array("item_id"=>$itemid));
            $shop_id=$iteminfo['shop_id'];
             $shop_info=app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shop_id));
            $html = '<span>' .  $shop_info["shop_name"] . '</span>';
            $colList[$k] =  $html;
        }
    }



}
