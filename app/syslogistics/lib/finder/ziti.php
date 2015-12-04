<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class syslogistics_finder_ziti {


    public $column_edit = '编辑';
    public $column_edit_order = 2;

    /**
     * 物流公司编辑
     * @var array
     * @return html
     */
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=syslogistics&ctl=admin_ziti&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&id='.$row['id'];
            $target = 'dialog::  {title:\''.app::get('syslogistics')->_('编辑自提点').'\', width:600, height:260}';
            $title = app::get('syslogistics')->_('编辑');

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }

    public $column_areaName = '地区';
    public $column_areaName_order = 3;
    public function column_areaName(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $colList[$k] = area::getSelectArea($row['area'],'');
        }
    }
}

