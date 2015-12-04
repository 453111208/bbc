<?php

class sysrate_finder_feedback {

    public $column_edit = '处理';
    public $column_edit_order = 1;
    public $column_edit_width = 10;

    public function column_edit(&$colList, $list)
    {
        foreach( $list as $k=>$row )
        {
            if( $row['status'] == 'active' )
            {
                $title = '操作';
            }
            else
            {
                $title = '查看';
            }

            $url = '?app=sysrate&ctl=feedback&act=showFeedback&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['id'];

            $target = 'dialog::{title:\''.app::get('sysrate')->_('处理反馈意见').'\', width:600, height:300}';
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }
}

