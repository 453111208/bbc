<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/* TODO: Add code here */
class desktop_finder_users{
    var $column_control = '操作';

    function __construct($app)
    {
        $this->app=$app;
    }
    
    function column_control(&$colList, $list)
    {
        foreach($list as $k => $row)
        {
            $colList[$k] = '<a href="?app=desktop&ctl=users&act=edit&_finder[finder_id]='.$_GET['_finder']['finder_id'].'&p[0]='.$row['user_id'].'" target="dialog::{title:\''.app::get('desktop')->_('编辑操作员').'\', width:680, height:450}">'.app::get('desktop')->_('编辑').'</a>';
        }
        
        
    }
}

?>
