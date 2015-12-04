<?php
class sysspfb_finder_brand {

    public $column_edit = '编辑';
    public $column_edit_order = 1;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
             $html = '  <a href=\'?app=sysspfb&ctl=admin_brand&act=approve&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['item_id'].'\'" target="dialog::{title:\''.app::get('sysspfb')->_('编辑').'\', width:640, height:420}">'.app::get('sysspfb')->_('编辑').'</a>';
         $objMdlItems = app::get('sysspfb')->model('item');
            $ItemInfo = $objMdlItems->getRow("*",array('item_id'=>$row['item_id']));
            $colList[$k] = $html;        
        }
    }


    /*public $column_cat = '关联类目';
    public $column_cat_order = 2;
    public function column_cat(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysspfb&ctl=admin_brand&act=brandRelCat&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['brand_id'];
            $target = 'dialog::  {title:\''.app::get('sysspfb')->_('查看关联类目').'\', width:500, height:350}';

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . app::get('sysspfb')->_('编辑') . '</a>';
        }
    }*/




}

