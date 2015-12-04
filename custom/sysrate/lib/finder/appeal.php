<?php

class sysrate_finder_appeal {

    public $column_edit = '审核';
    public $column_edit_order = 1;
    public $column_edit_width = 10;

    public function column_edit(&$colList, $list)
    {
        foreach( $list as $k=>$row )
        {
            if( $row['status'] == 'WAIT' )
            {
                $url = '?app=sysrate&ctl=appeal&act=checkView&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['appeal_id'];
                $target = 'dialog::{title:\''.app::get('sysrate')->_('审核申诉').'\', width:800, height:600}';
                $title = '操作';
                $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            }
            else
            {
                $url = '?app=sysrate&ctl=appeal&act=checkView&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['appeal_id'];
                $target = 'dialog::{title:\''.app::get('sysrate')->_('查看审核申诉').'\', width:800, height:600}';
                $title = '查看';

                $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            }
        }
    }
}

