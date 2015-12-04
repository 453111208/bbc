<?php
class sysshop_finder_shop_rel_lv1cat{
    public $column_edit = '操作';
    public $column_edit_order = 4;
    public $column_edit_width = 200;


    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshop&ctl=admin_shop&act=updateCatInfo&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shop_id'].'&p[1]='.$row['cat_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('编辑入驻类目费用').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑费用');
            $return .= ' <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';            
        }

        $colList[$k] = $return;
    }
}