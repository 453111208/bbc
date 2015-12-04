<?php
class sysshop_finder_shopnotice {

    public $column_edit = '操作';
    public $column_edit_order = 1;

    /**
     * @brief 编辑链接
     *
     * @param $row
     *
     * @return page 
     */
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysshop&ctl=admin_shopnotice&act=noticeEdit&finder_id='.$_GET['_finder']['finder_id'].'&notice_id='.$row['notice_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }
}

