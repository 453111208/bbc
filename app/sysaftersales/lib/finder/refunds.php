<?php

class sysaftersales_finder_refunds {

    public $column_edit = '操作';
    public $column_edit_order = 1;
    public $column_edit_width = 60;
    public function column_edit(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $url = '?app=sysaftersales&ctl=refunds&act=refundsPay&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['aftersales_bn'];
            $target = 'dialog::{title:\''.app::get('sysaftersales')->_('同意退款').'\', width:800, height:300}';
            $title = app::get('sysaftersales')->_('同意退款');
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            $url = '?app=sysaftersales&ctl=refunds&act=rejectView&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['refunds_id'];
            $target = 'dialog::{title:\''.app::get('sysaftersales')->_('拒绝退款').'\', width:300, height:300}';
            $title = app::get('sysaftersales')->_('拒绝退款');
            $colList[$k] .= ' | <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';

            if( $row['status'] != '0' )
            {
                $colList[$k] = "";
            }
        }
    }
}
