<?php
class sysexpert_finder_literary{
    public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysexpert&ctl=admin_literary&act=create&finder_id='.$_GET['_finder']['finder_id'].'&literary_id='.$row['literary_id'];
            $target = 'dialog::  {title:\''.app::get('sysexpert')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysexpert')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }
}