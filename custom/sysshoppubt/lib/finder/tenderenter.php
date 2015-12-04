<?php
class sysshoppubt_finder_tenderenter{

     //操作按钮
    public $column_editbutton = '操作';
    public function column_editbutton(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshoppubt&ctl=tenderenter&act=close&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['tender_id'].'&p[1]='.$row['uniqid'];

            $target = 'dialog::  {title:\''.app::get('sysshoppubt')->_('操作').'\', width:500, height:200}';
            $title = app::get('sysshoppubt')->_('关闭');
            $button = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            $colList[$k] = $button;
        }
    }

}