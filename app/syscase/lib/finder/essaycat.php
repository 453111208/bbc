<?php
class syscase_finder_essaycat{
    public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=syscase&ctl=admin_essaycat&act=create&finder_id='.$_GET['_finder']['finder_id'].'&essaycat_id='.$row['essaycat_id'];
            $target = 'dialog::  {title:\''.app::get('syscase')->_('编辑').'\', width:800, height:500}';
            $title = app::get('syscase')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }
}