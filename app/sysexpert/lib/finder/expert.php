<?php
class sysexpert_finder_expert{
	public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysexpert&ctl=admin_expert&act=create&finder_id='.$_GET['_finder']['finder_id'].'&expert_id='.$row['expert_id'];
            $target = 'dialog::  {title:\''.app::get('sysexpert')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysexpert')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }
}