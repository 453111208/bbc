<?php
class sysnotice_finder_noticetype{
	public $column_edit = "编辑";
    public $column_edit_order = 1;
    public $column_edit_width = 50;
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysnotice&ctl=admin_affiche&act=typecreate&finder_id='.$_GET['_finder']['finder_id'].'&type_id='.$row['type_id'];
            $target = 'dialog::  {title:\''.app::get('sysnotice')->_('编辑').'\', width:800, height:500}';
            $title = app::get('sysnotice')->_('编辑');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $colList[$k] = $button;
        }
    }
}