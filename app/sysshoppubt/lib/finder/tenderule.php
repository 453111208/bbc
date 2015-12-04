<?php
class sysshoppubt_finder_tenderule{
    public $column_editbutton = '操作';
    public $column_editbutton_width=220;

    public function column_editbutton(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=tenderule&act=create&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tenderrule_id'].'&p[1]='.$row['serial'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('修改').'\', width:800, height:500}';
            $title = app::get('sysshoppubt')->_('修改');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }
}