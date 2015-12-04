<?php
class sysshop_finder_service{
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
            $url = '?app=sysshop&ctl=admin_service&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&article_id='.$row['article_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('编辑');
            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }
}
