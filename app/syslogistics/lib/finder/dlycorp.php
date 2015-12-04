<?php

/**
 * ShopEx licence
 * @author ajx
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class syslogistics_finder_dlycorp{


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
            $url = '?app=syslogistics&ctl=admin_logistics&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&p[0]='.$row['corp_id'];
            $target = 'dialog::  {title:\''.app::get('syslogistics')->_('物流公司编辑').'\', width:500, height:400}';
            $title = app::get('syslogistics')->_('编辑'); 

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';            
        }
    }
}

