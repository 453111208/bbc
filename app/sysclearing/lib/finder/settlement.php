<?php
class sysclearing_finder_settlement{
    public $column_edit = '操作';
    public $column_edit_order = 1;
    public $column_edit_width = 60;

    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            if($row['settlement_status']=='2')
            {
                $colList[$k] = '已结算';
            }
            else
            {
                $url = '?app=sysclearing&ctl=admin_settlement&act=confirm&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['settlement_no'];
                $target = 'dialog::{title:\''.app::get('sysclearing')->_('结算确认').'\', width:300, height:200}';
                $title = app::get('sysclearing')->_('结算确认');
                $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
            }
            
        }
    }

}