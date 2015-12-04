<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysuser_finder_trustlogin_cfg{

    /**
     * @var string 操作列名称
     */
    var $column_control = '配置';

    /**
     * 配置列显示的html
     * @param array 该行的数据
     * @return string html
     */
    function column_control(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=sysuser&ctl=admin_trustlogincfg&act=setting&p[0]='.$row['flag'].'&finder_id='. $_GET['_finder']['finder_id'];
            $target = 'dialog::  {title:\''.app::get('sysuser')->_('会员信息编辑').'\', width:500, height:400}';
            $title = app::get('sysuser')->_('配置');

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }

    
    

}
