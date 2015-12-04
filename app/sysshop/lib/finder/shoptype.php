<?php
class sysshop_finder_shoptype {

    public $column_edit = '配置';
    public $column_edit_order = 2;

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
            $url = '?app=sysshop&ctl=admin_shoptype&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['shoptype_id'];
            $target = 'dialog::  {title:\''.app::get('sysshop')->_('店铺类型配置').'\', width:500, height:400}';
            $title = app::get('sysshop')->_('配置');

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }
}

