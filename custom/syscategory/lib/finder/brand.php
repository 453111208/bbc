<?php
class syscategory_finder_brand {

    public $column_edit = '编辑';
    public $column_edit_order = 1;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $url = '?app=syscategory&ctl=admin_brand&act=create&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['brand_id'];
            $target = 'dialog::  {title:\''.app::get('syscategory')->_('编辑品牌').'\', width:500, height:350}';

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . app::get('syscategory')->_('编辑') . '</a>';
        }
    }

    public $column_goods_pic = "品牌LOGO";
    public $column_goods_pic_order = COLUMN_IN_HEAD;
    public function column_goods_pic(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $src = $row['brand_logo'];
            $colList[$k] = "<a href='$src' class='img-tip pointer' target='_blank' onmouseover='bindFinderColTip(event);'><span>&nbsp;pic</span></a>";
        }
    }


    /*public $column_cat = '关联类目';
    public $column_cat_order = 2;
    public function column_cat(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=syscategory&ctl=admin_brand&act=brandRelCat&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['brand_id'];
            $target = 'dialog::  {title:\''.app::get('syscategory')->_('查看关联类目').'\', width:500, height:350}';

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . app::get('syscategory')->_('编辑') . '</a>';
        }
    }*/




}

